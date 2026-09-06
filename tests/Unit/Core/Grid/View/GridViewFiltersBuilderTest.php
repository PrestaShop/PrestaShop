<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\View;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRangeComputer;
use PrestaShop\PrestaShop\Core\Grid\View\DynamicDateRuleApplier;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewFiltersBuilder;
use PrestaShop\PrestaShop\Core\Grid\View\GridViewUrlGenerator;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\Entity\AdminGridConfiguration;
use PrestaShopBundle\Entity\AdminGridView;
use PrestaShopBundle\Entity\Repository\AdminGridViewRepository;
use Symfony\Component\HttpFoundation\Request;

class GridViewFiltersBuilderTest extends TestCase
{
    private const EMPLOYEE_ID = 7;
    private const SHOP_ID = 1;

    public function testItReplacesTheCriteriaWithTheViewSnapshot(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: self::EMPLOYEE_ID, shared: false, criteria: [
            'limit' => 100,
            'orderBy' => 'total',
            'sortOrder' => 'DESC',
            'filters' => ['status' => 'paid'],
        ]);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters());

        $this->assertSame(['status' => 'paid'], $filters->getFilters());
        $this->assertSame(100, $filters->getLimit());
        $this->assertSame('total', $filters->getOrderBy());
        $this->assertSame('desc', strtolower((string) $filters->getOrderWay()));
        $this->assertNull($filters->getOffset());
    }

    public function testAViewWithoutFiltersResetsTheGrid(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: self::EMPLOYEE_ID, shared: false, criteria: []);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters());

        $this->assertSame([], $filters->getFilters());
        $this->assertSame(50, $filters->getLimit());
    }

    public function testItIgnoresViewsOfAnotherGrid(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: self::EMPLOYEE_ID, shared: false, criteria: [
            'filters' => ['status' => 'paid'],
        ], filterId: 'customer');

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters());

        $this->assertSame(['date_add' => ['from' => '2020-01-01']], $filters->getFilters());
    }

    public function testItIgnoresPrivateViewsOfAnotherEmployee(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: 999, shared: false, criteria: [
            'filters' => ['status' => 'paid'],
        ]);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters());

        $this->assertSame(['date_add' => ['from' => '2020-01-01']], $filters->getFilters());
    }

    public function testItAppliesSharedViewsOfAnotherEmployee(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: 999, shared: true, criteria: [
            'filters' => ['status' => 'paid'],
        ]);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters());

        $this->assertSame(['status' => 'paid'], $filters->getFilters());
    }

    public function testItIgnoresTheViewWhenTheFeatureIsDisabled(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: self::EMPLOYEE_ID, shared: false, criteria: [
            'filters' => ['status' => 'paid'],
        ]);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters(), featureFlagEnabled: false);

        $this->assertSame(['date_add' => ['from' => '2020-01-01']], $filters->getFilters());
    }

    public function testItIgnoresTheViewWhenTheRouteDoesNotMatch(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: self::EMPLOYEE_ID, shared: false, criteria: [
            'filters' => ['status' => 'paid'],
        ]);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters(), requestRoute: 'admin_customers_index');

        $this->assertSame(['date_add' => ['from' => '2020-01-01']], $filters->getFilters());
    }

    public function testItIgnoresTheViewOutsideSingleShopContext(): void
    {
        $gridView = $this->buildGridView(ownerEmployeeId: self::EMPLOYEE_ID, shared: false, criteria: [
            'filters' => ['status' => 'paid'],
        ]);

        $filters = $this->applyBuilder($gridView, $this->buildCurrentFilters(), singleShopContext: false);

        $this->assertSame(['date_add' => ['from' => '2020-01-01']], $filters->getFilters());
    }

    private function applyBuilder(
        AdminGridView $gridView,
        Filters $filters,
        bool $featureFlagEnabled = true,
        bool $singleShopContext = true,
        string $requestRoute = 'admin_orders_index',
    ): Filters {
        $repository = $this->createMock(AdminGridViewRepository::class);
        $repository->method('find')->with(42)->willReturn($gridView);

        $employee = $this->createMock(Employee::class);
        $employee->method('getId')->willReturn(self::EMPLOYEE_ID);
        $employeeContext = $this->createMock(EmployeeContext::class);
        $employeeContext->method('getEmployee')->willReturn($employee);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getId')->willReturn(self::SHOP_ID);
        $shopContext->method('isSingleShopContext')->willReturn($singleShopContext);

        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $featureFlagStateChecker->method('isEnabled')->willReturn($featureFlagEnabled);

        $builder = new GridViewFiltersBuilder(
            $repository,
            new DynamicDateRuleApplier(new DynamicDateRangeComputer()),
            $employeeContext,
            $shopContext,
            $featureFlagStateChecker
        );

        $builder->setConfig([
            'request' => new Request(
                [GridViewUrlGenerator::SELECTED_VIEW_PARAM => '42'],
                [],
                ['_route' => $requestRoute]
            ),
        ]);

        return $builder->buildFilters($filters);
    }

    private function buildCurrentFilters(): Filters
    {
        return new Filters([
            'limit' => 50,
            'offset' => 20,
            'orderBy' => 'id_order',
            'sortOrder' => 'ASC',
            'filters' => ['date_add' => ['from' => '2020-01-01']],
        ], 'order');
    }

    private function buildGridView(int $ownerEmployeeId, bool $shared, array $criteria, string $filterId = 'order'): AdminGridView
    {
        $configuration = new AdminGridConfiguration();
        $configuration
            ->setEmployeeId($ownerEmployeeId)
            ->setShopId(self::SHOP_ID)
            ->setGridId($filterId)
            ->setFilterId($filterId)
            ->setControllerRoute('admin_orders_index')
        ;

        $gridView = new AdminGridView();
        $gridView
            ->setGridConfiguration($configuration)
            ->setName('Test view')
            ->setFilterId($filterId)
            ->setShared($shared)
            ->setFilters((string) json_encode($criteria))
        ;

        return $gridView;
    }
}
