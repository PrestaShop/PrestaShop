<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\PrestaShopBundle\Controller\ArgumentResolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\SearchParametersInterface;
use PrestaShopBundle\Controller\ArgumentResolver\SearchParametersResolver;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Event\FilterSearchCriteriaEvent;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class SearchParametersResolverTest extends TestCase
{
    private const EMPLOYEE_ID = 99;
    private const SHOP_ID = 13;

    public function testConstructor()
    {
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock(),
            $this->buildTokenStorageMock(),
            $this->buildAdminFilterRepositoryMock(),
            $this->buildEventDispatcherMock(),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);
    }

    public function testSupports()
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        //With no employee
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock(),
            $this->buildTokenStorageMock(),
            $this->buildAdminFilterRepositoryMock(),
            $this->buildEventDispatcherMock(),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);

        $this->assertFalse($resolver->supports($requestMock, $this->buildArgumentMetaDataMock(SampleFilters::class)));

        //With employee
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock(),
            $this->buildTokenStorageMock(true),
            $this->buildAdminFilterRepositoryMock(),
            $this->buildEventDispatcherMock(),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);

        $this->assertTrue($resolver->supports($requestMock, $this->buildArgumentMetaDataMock(SampleFilters::class)));
        $this->assertFalse($resolver->supports($requestMock, $this->buildArgumentMetaDataMock(Employee::class)));
    }

    public function testResolveDefaults()
    {
        //With employee
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock(),
            $this->buildTokenStorageMock(true),
            $this->buildAdminFilterRepositoryMock(),
            $this->buildEventDispatcherMock(SampleFilters::getDefaults()),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);

        $request = $this->buildRequestMock([]);

        /** @var \Generator $yieldFilters */
        $yieldFilters = $resolver->resolve($request, $this->buildArgumentMetaDataMock(SampleFilters::class));
        /** @var SampleFilters $filters */
        $filters = $yieldFilters->current();
        $this->assertEquals(SampleFilters::getDefaults(), $filters->all());
    }

    public function testSavedParameters()
    {
        $savedParameters = [
            'limit' => 5,
            'offset' => 20,
        ];
        $expectedParameters = array_merge(SampleFilters::getDefaults(), $savedParameters);

        //With employee
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock(['limit' => 5, 'offset' => 20]),
            $this->buildTokenStorageMock(true),
            $this->buildAdminFilterRepositoryMock(), //No request parameters so no saving
            $this->buildEventDispatcherMock($expectedParameters),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);

        $request = $this->buildRequestMock([]);

        /** @var \Generator $yieldFilters */
        $yieldFilters = $resolver->resolve($request, $this->buildArgumentMetaDataMock(SampleFilters::class));
        /** @var SampleFilters $filters */
        $filters = $yieldFilters->current();
        $this->assertNotEquals(SampleFilters::getDefaults(), $filters->all());
        $this->assertEquals(5, $filters->getLimit());
        $this->assertEquals(20, $filters->getOffset());
    }

    public function testRequestParameters()
    {
        $requestParameters = [
            'orderBy' => 'name',
            'sortOrder' => 'asc',
            'filters' => [
                'name' => 'test',
            ],
            'unknownParameter' => 'plop',
        ];
        $expectedParameters = array_merge(SampleFilters::getDefaults(), $requestParameters);

        //With employee
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock(null, $requestParameters),
            $this->buildTokenStorageMock(true),
            $this->buildAdminFilterRepositoryMock($requestParameters),
            $this->buildEventDispatcherMock($expectedParameters),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);

        //Request must be GET and have one of these three parameters (filters|limit|sortOrder)
        $request = $this->buildRequestMock(['orderBy' => 'name']);

        /** @var \Generator $yieldFilters */
        $yieldFilters = $resolver->resolve($request, $this->buildArgumentMetaDataMock(SampleFilters::class));
        /** @var SampleFilters $filters */
        $filters = $yieldFilters->current();
        $this->assertNotEquals(SampleFilters::getDefaults(), $filters->all());
        $this->assertEquals('name', $filters->getOrderBy());
        $this->assertEquals('asc', $filters->getOrderWay());
        $this->assertEquals($requestParameters['filters'], $filters->getFilters());

        //Default values
        $this->assertEquals(42, $filters->getOffset());
        $this->assertEquals(51, $filters->getLimit());
    }

    public function testRequestOverridesSaved()
    {
        $requestParameters = [
            'orderBy' => 'name',
            'sortOrder' => 'asc',
            'filters' => [
                'name' => 'test',
            ],
            'limit' => 33,
            'unknownParameter' => 'plop',
        ];
        $savedParameters = [
            'limit' => 5,
            'offset' => 20,
        ];
        $expectedParameters = array_merge($savedParameters, $requestParameters);

        //With employee
        $resolver = new SearchParametersResolver(
            $this->buildSearchParametersMock($savedParameters, $requestParameters),
            $this->buildTokenStorageMock(true),
            $this->buildAdminFilterRepositoryMock($expectedParameters),
            $this->buildEventDispatcherMock($expectedParameters),
            self::SHOP_ID
        );
        $this->assertNotNull($resolver);

        //Request must be GET and have one of these three parameters (filters|limit|sortOrder)
        $request = $this->buildRequestMock(['orderBy' => 'name']);

        /** @var \Generator $yieldFilters */
        $yieldFilters = $resolver->resolve($request, $this->buildArgumentMetaDataMock(SampleFilters::class));
        /** @var SampleFilters $filters */
        $filters = $yieldFilters->current();
        $this->assertNotEquals(SampleFilters::getDefaults(), $filters->all());
        $this->assertEquals('name', $filters->getOrderBy());
        $this->assertEquals('asc', $filters->getOrderWay());
        $this->assertEquals($requestParameters['filters'], $filters->getFilters());
        $this->assertEquals(20, $filters->getOffset());
        $this->assertEquals(33, $filters->getLimit());
    }

    /**
     * @param array|null $expectedFilters
     *
     * @return MockObject|EventDispatcherInterface
     */
    private function buildEventDispatcherMock(array $expectedFilters = null)
    {
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        if (null !== $expectedFilters) {
            $dispatcherMock
                ->expects($this->once())
                ->method('dispatch')
                ->with(
                    $this->callback(function (FilterSearchCriteriaEvent $event) use ($expectedFilters) {
                        $this->assertInstanceOf(FilterSearchCriteriaEvent::class, $event);
                        /** @var SampleFilters $filters */
                        $filters = $event->getSearchCriteria();
                        $this->assertNotNull($filters);
                        $this->assertInstanceOf(SampleFilters::class, $filters);
                        $this->assertEquals($expectedFilters, $filters->all());

                        return true;
                    }),
                    $this->equalTo(FilterSearchCriteriaEvent::NAME)
                );
        }

        return $dispatcherMock;
    }

    /**
     * @param string $type
     *
     * @return MockObject|ArgumentMetadata
     */
    private function buildArgumentMetaDataMock($type)
    {
        $argumentMetadataMock = $this->getMockBuilder(ArgumentMetadata::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $argumentMetadataMock
            ->expects($this->once())
            ->method('getType')
            ->willReturn($type)
        ;

        return $argumentMetadataMock;
    }

    /**
     * @param array $parameters
     * @param bool $postQuery
     *
     * @return MockObject|Request
     */
    private function buildRequestMock(array $parameters = [], $postQuery = false)
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $requestMock
            ->method('get')
            ->willReturnCallback(function ($parameter, $default = null) {
                return $default;
            });

        $parametersBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $parametersBagMock
            ->method('has')
            ->willReturnCallback(function ($parameter) use ($parameters) {
                return isset($parameters[$parameter]);
            })
        ;
        $parametersBagMock
            ->method('get')
            ->willReturnCallback(function ($parameter, $default = null) use ($parameters) {
                return $parameters[$parameter] ?? $default;
            })
        ;

        $emptyParametersBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $emptyParametersBagMock
            ->method('has')
            ->willReturn(false)
        ;

        $requestMock
            ->expects($this->once())
            ->method('isMethod')
            ->willReturn(!$postQuery)
        ;
        if ($postQuery) {
            $requestMock->request = $parametersBagMock;
            $requestMock->query = $emptyParametersBagMock;
        } else {
            $requestMock->query = $parametersBagMock;
            $requestMock->request = $emptyParametersBagMock;
        }

        return $requestMock;
    }

    /**
     * @param bool $withEmployee
     *
     * @return MockObject|TokenStorageInterface
     */
    private function buildTokenStorageMock($withEmployee = false)
    {
        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if ($withEmployee) {
            $employeeMock = $this->getMockBuilder(Employee::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;

            $employeeMock
                ->method('getId')
                ->willReturn(self::EMPLOYEE_ID)
            ;

            $tokenMock = $this->getMockBuilder(SerializableTokenInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $tokenMock
                ->expects($this->once())
                ->method('getUser')
                ->willReturn($employeeMock)
            ;

            $tokenStorageMock
                ->expects($this->once())
                ->method('getToken')
                ->willReturn($tokenMock)
            ;
        }

        return $tokenStorageMock;
    }

    /**
     * @param array|null $repoParameters
     * @param array $requestParameters
     *
     * @return SearchParametersInterface
     */
    private function buildSearchParametersMock(array $repoParameters = null, array $requestParameters = []): SearchParametersInterface
    {
        $searchParametersMock = $this->getMockBuilder(SearchParametersInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $repoParameters) {
            $repoFilters = new SampleFilters($repoParameters);
            $searchParametersMock
                ->expects($this->once())
                ->method('getFiltersFromRepository')
                ->willReturn($repoFilters)
            ;
        }

        $requestFilters = new SampleFilters($requestParameters);
        $searchParametersMock
            ->expects(empty($requestParameters) ? $this->any() : $this->once())
            ->method('getFiltersFromRequest')
            ->willReturn($requestFilters)
        ;

        return $searchParametersMock;
    }

    /**
     * @return MockObject|AdminFilterRepository
     */
    private function buildAdminFilterRepositoryMock(array $expectedParameters = null)
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $expectedParameters) {
            $savedParameters = array_merge(SampleFilters::getDefaults(), $expectedParameters);
            unset($savedParameters['offset']);
            $repositoryMock
                ->expects($this->once())
                ->method('createOrUpdateByEmployeeAndRouteParams')
                ->with(
                    $this->equalTo(self::EMPLOYEE_ID),
                    $this->equalTo(self::SHOP_ID),
                    $this->equalTo($savedParameters)
                )
            ;
        }

        return $repositoryMock;
    }
}

class SampleFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 51,
            'offset' => 42,
            'orderBy' => 'id_sample',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}

interface SerializableTokenInterface extends TokenInterface
{
    public function __serialize(): array;

    public function __unserialize(array $data): void;
}
