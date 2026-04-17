<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Search\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\PersistFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use Symfony\Component\HttpFoundation\Request;

class PersistFiltersBuilderTest extends TestCase
{
    private const EMPLOYEE_ID = 42;
    private const SHOP_ID = 51;

    public function testBuildWithoutParameters(): void
    {
        $builder = new PersistFiltersBuilder(
            $this->buildUnusedRepository(),
            $this->buildUnusedEmployeeProviderMock(),
            self::SHOP_ID
        );
        $filters = $builder->buildFilters();
        $this->assertNull($filters);
    }

    public function testBuildWithFilterId(): void
    {
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $inputFilters = new Filters($expectedFilters, 'language');
        $savedFilters = [
            'limit' => 10,
        ];

        $builder = new PersistFiltersBuilder(
            $this->buildRepositoryByFilterIdMock($savedFilters, 'language'),
            $this->buildEmployeeProviderMock(),
            self::SHOP_ID
        );

        $filters = $builder->buildFilters($inputFilters);
        $this->assertNotNull($filters);
        $this->assertEquals($expectedFilters, $filters->all());
        $this->assertEquals('language', $filters->getFilterId());
    }

    public function testBuildWithRequest(): void
    {
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $inputFilters = new Filters($expectedFilters);
        $savedFilters = [
            'limit' => 10,
        ];

        $builder = new PersistFiltersBuilder(
            $this->buildRepositoryByRouteMock($savedFilters, 'language', 'index'),
            $this->buildEmployeeProviderMock(),
            self::SHOP_ID
        );
        $builder->setConfig([
            'request' => $this->buildRequestMock('PrestaShopBundle\Controller\Admin\Improve\International\LanguageController::indexAction'),
        ]);

        $filters = $builder->buildFilters($inputFilters);
        $this->assertNotNull($filters);
        $this->assertEquals($expectedFilters, $filters->all());
        $this->assertEmpty($filters->getFilterId());
    }

    public function testNoNeedForPersist(): void
    {
        $expectedFilters = [
            'limit' => 10,
            'offset' => 10,
        ];
        $inputFilters = new Filters($expectedFilters, 'language');
        $inputFilters->setNeedsToBePersisted(false);

        $builder = new PersistFiltersBuilder(
            $this->buildUnusedRepository(),
            $this->buildEmployeeProviderMock(1),
            self::SHOP_ID
        );

        $filters = $builder->buildFilters($inputFilters);
        $this->assertNotNull($filters);
        $this->assertEquals($expectedFilters, $filters->all());
        $this->assertEquals('language', $filters->getFilterId());
    }

    /**
     * @param array $filters
     * @param string $filterId
     *
     * @return AdminFilterRepository
     */
    private function buildRepositoryByFilterIdMock(array $filters, string $filterId): AdminFilterRepository
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryMock
            ->expects($this->once())
            ->method('createOrUpdateByEmployeeAndFilterId')
            ->with(
                $this->equalTo(self::EMPLOYEE_ID),
                $this->equalTo(self::SHOP_ID),
                $this->equalTo($filters),
                $this->equalTo($filterId)
            )
        ;

        $repositoryMock
            ->expects($this->never())
            ->method('createOrUpdateByEmployeeAndRouteParams')
        ;

        return $repositoryMock;
    }

    /**
     * @param array $filters
     * @param string $controller
     * @param string $action
     *
     * @return AdminFilterRepository
     */
    private function buildRepositoryByRouteMock(array $filters, string $controller, string $action): AdminFilterRepository
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryMock
            ->expects($this->once())
            ->method('createOrUpdateByEmployeeAndRouteParams')
            ->with(
                $this->equalTo(self::EMPLOYEE_ID),
                $this->equalTo(self::SHOP_ID),
                $this->equalTo($filters),
                $this->equalTo($controller),
                $this->equalTo($action)
            )
        ;

        $repositoryMock
            ->expects($this->never())
            ->method('createOrUpdateByEmployeeAndFilterId')
        ;

        return $repositoryMock;
    }

    /**
     * @return AdminFilterRepository
     */
    private function buildUnusedRepository(): AdminFilterRepository
    {
        $repositoryMock = $this->getMockBuilder(AdminFilterRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $repositoryMock
            ->expects($this->never())
            ->method('createOrUpdateByEmployeeAndRouteParams')
        ;

        $repositoryMock
            ->expects($this->never())
            ->method('createOrUpdateByEmployeeAndFilterId')
        ;

        return $repositoryMock;
    }

    /**
     * @return ContextEmployeeProviderInterface
     */
    private function buildEmployeeProviderMock(?int $calledTimes = null): ContextEmployeeProviderInterface
    {
        $employeeProviderMock = $this->getMockBuilder(ContextEmployeeProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $employeeProviderMock
            ->expects(null !== $calledTimes ? $this->exactly($calledTimes) : $this->atLeastOnce())
            ->method('getId')
            ->willReturn(self::EMPLOYEE_ID)
        ;

        return $employeeProviderMock;
    }

    /**
     * @return ContextEmployeeProviderInterface
     */
    private function buildUnusedEmployeeProviderMock(): ContextEmployeeProviderInterface
    {
        $employeeProviderMock = $this->getMockBuilder(ContextEmployeeProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $employeeProviderMock
            ->expects($this->never())
            ->method('getId')
        ;

        return $employeeProviderMock;
    }

    /**
     * @param string $controller
     *
     * @return Request
     */
    private function buildRequestMock(string $controller): Request
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('_controller')
            )
            ->willReturn($controller)
        ;

        return $requestMock;
    }
}
