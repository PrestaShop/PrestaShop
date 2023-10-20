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

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\SearchParameters;
use PrestaShopBundle\Entity\AdminFilter;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class SearchParametersTest extends TestCase
{
    public function testConstructor()
    {
        $searchParameters = new SearchParameters($this->buildAdminFilterRepositoryMock());
        $this->assertNotNull($searchParameters);
    }

    public function testGetFiltersFromGetRequest()
    {
        $searchParameters = new SearchParameters($this->buildAdminFilterRepositoryMock());
        $this->assertNotNull($searchParameters);

        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($expectedParameters);
        /** @var SampleFilters $filters */
        $filters = $searchParameters->getFiltersFromRequest($requestMock, SampleFilters::class);
        $this->assertNotNull($filters);
        $this->assertInstanceOf(SampleFilters::class, $filters);
        $this->assertInstanceOf(SearchCriteriaInterface::class, $filters);
        unset($expectedParameters['unknownParameter']);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals(10, $filters->getLimit());
        $this->assertEquals(10, $filters->getOffset());
        $this->assertNull($filters->getOrderBy());
        $this->assertNull($filters->getOrderWay());
        $this->assertNull($filters->getFilters());
        $this->assertNull($filters->get('unknownParameter'));
    }

    public function testGetFiltersFromPostRequest()
    {
        $searchParameters = new SearchParameters($this->buildAdminFilterRepositoryMock());
        $this->assertNotNull($searchParameters);

        $expectedParameters = [
            'orderBy' => 'name',
            'sortOrder' => 'asc',
            'filters' => [
                'name' => 'test',
            ],
            'unknownParameter' => 'plop',
        ];
        $requestMock = $this->buildRequestMock($expectedParameters, true);
        /** @var SampleFilters $filters */
        $filters = $searchParameters->getFiltersFromRequest($requestMock, SampleFilters::class);
        $this->assertNotNull($filters);
        $this->assertInstanceOf(SampleFilters::class, $filters);
        $this->assertInstanceOf(SearchCriteriaInterface::class, $filters);
        unset($expectedParameters['unknownParameter']);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals('name', $filters->getOrderBy());
        $this->assertEquals('asc', $filters->getOrderWay());
        $this->assertEquals($expectedParameters['filters'], $filters->getFilters());
        $this->assertNull($filters->getLimit());
        $this->assertNull($filters->getOffset());
        $this->assertNull($filters->get('unknownParameter'));
    }

    public function testGetFiltersFromRepository()
    {
        $expectedParameters = [
            'limit' => 10,
            'offset' => 10,
        ];

        $searchParameters = new SearchParameters($this->buildAdminFilterRepositoryMock($expectedParameters));
        $this->assertNotNull($searchParameters);

        /** @var SampleFilters $filters */
        $filters = $searchParameters->getFiltersFromRepository(1, 1, 'ProductController', 'list', SampleFilters::class);
        $this->assertNotNull($filters);
        $this->assertInstanceOf(SampleFilters::class, $filters);
        $this->assertInstanceOf(SearchCriteriaInterface::class, $filters);
        $this->assertEquals($expectedParameters, $filters->all());
        $this->assertEquals(10, $filters->getLimit());
        $this->assertEquals(10, $filters->getOffset());
        $this->assertNull($filters->getOrderBy());
        $this->assertNull($filters->getOrderWay());
        $this->assertNull($filters->getFilters());
    }

    /**
     * @param array|null $filters
     *
     * @return MockObject|AdminFilterRepository
     */
    private function buildAdminFilterRepositoryMock(array $filters = null)
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $filters) {
            $adminFilterMock = $this->getMockBuilder(AdminFilter::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;

            $adminFilterMock
                ->expects($this->once())
                ->method('getFilter')
                ->willReturn(json_encode($filters))
            ;

            $repositoryMock
                ->expects($this->once())
                ->method('findByEmployeeAndRouteParams')
                ->willReturn($adminFilterMock)
            ;
        }

        return $repositoryMock;
    }

    /**
     * @param array $parameters
     * @param bool $postQuery
     *
     * @return MockObject|Request
     */
    private function buildRequestMock(array $parameters, $postQuery = false)
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parametersBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $parametersBagMock
            ->expects($this->once())
            ->method('all')
            ->willReturn($parameters)
        ;
        $emptyParametersBagMock = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $emptyParametersBagMock
            ->expects($this->once())
            ->method('all')
            ->willReturn([])
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
}

class SampleFilters extends Filters
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaults()
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_sample',
            'sortOrder' => 'desc',
            'filters' => [],
        ];
    }
}
