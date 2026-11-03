<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Stats\Repository;

use AdminStatsController;
use Configuration;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;

/**
 * Parity between the migrated KPI queries and the legacy AdminStatsController helpers,
 * computed on the same test database.
 */
class StatsRepositoryTest extends KernelTestCase
{
    private const DATE_FROM = '2000-01-01';
    private const DATE_TO = '2100-12-31';

    private ContextMocker $contextMocker;

    private StatsRepository $statsRepository;

    /**
     * @var int[]
     */
    private array $shopIds;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->contextMocker = (new ContextMocker())->mockContext();
        $this->statsRepository = self::getContainer()->get(StatsRepository::class);
        $this->shopIds = array_map('intval', Shop::getContextListShopID());
    }

    protected function tearDown(): void
    {
        $this->contextMocker->resetContext();
        parent::tearDown();
    }

    public function testModuleCountersMatchLegacy(): void
    {
        $this->assertSame((int) AdminStatsController::getInstalledModules(), $this->statsRepository->countInstalledModules($this->shopIds));
        $this->assertSame((int) AdminStatsController::getDisabledModules(), $this->statsRepository->countDisabledModules($this->shopIds));
    }

    public function testProductCountersMatchLegacy(): void
    {
        $activation = $this->statsRepository->getProductActivationCounters($this->shopIds);
        $this->assertSame(AdminStatsController::getDisabledProducts(), $activation['disabled']);
        $this->assertSame(AdminStatsController::getTotalProducts(), $activation['total']);

        $stock = $this->statsRepository->getProductStockCounters($this->shopIds);
        $this->assertGreaterThan(0, $stock['total']);
        $this->assertSame(
            AdminStatsController::getPercentProductStock(),
            round(100 * $stock['with_stock'] / $stock['total'], 2) . '%'
        );
        $this->assertSame(
            AdminStatsController::getPercentProductOutOfStock(),
            round(100 * ($stock['total'] - $stock['with_stock']) / $stock['total'], 2) . '%'
        );

        $this->assertSame(
            AdminStatsController::getProductAverageGrossMargin(),
            round(100 * $this->statsRepository->getProductAverageGrossMargin($this->shopIds), 2) . '%'
        );
    }

    public function testCategoryCountersMatchLegacy(): void
    {
        $this->assertSame(AdminStatsController::getDisabledCategories(), $this->statsRepository->countDisabledCategories($this->shopIds));
        $this->assertSame(AdminStatsController::getTotalCategories(), $this->statsRepository->countTotalCategories($this->shopIds));
        $this->assertSame(
            AdminStatsController::getEmptyCategories(),
            $this->statsRepository->countEmptyCategories((int) Configuration::get('PS_ROOT_CATEGORY'), $this->shopIds)
        );
    }

    public function testCustomerCountersMatchLegacy(): void
    {
        $legacyGender = AdminStatsController::getCustomerMainGender();
        $counters = $this->statsRepository->getCustomerGenderCounters($this->shopIds);
        $this->assertNotFalse($legacyGender);
        $this->assertNotNull($counters);
        $this->assertSame(
            $legacyGender['value'],
            round(100 * $counters[$legacyGender['type']] / $counters['total'])
        );

        $this->assertEquals(
            AdminStatsController::getAverageCustomerAge(),
            round($this->statsRepository->getAverageCustomerAgeInDays($this->shopIds) / 365)
        );
    }

    public function testOrderAndVisitCountersMatchLegacy(): void
    {
        $this->assertSame(
            (int) AdminStatsController::getOrders(self::DATE_FROM, self::DATE_TO),
            $this->statsRepository->countOrders(self::DATE_FROM, self::DATE_TO, $this->shopIds)
        );
        $this->assertEqualsWithDelta(
            (float) AdminStatsController::getTotalSales(self::DATE_FROM, self::DATE_TO),
            $this->statsRepository->getTotalSales(self::DATE_FROM, self::DATE_TO, $this->shopIds),
            0.01
        );
        $this->assertSame(
            (int) AdminStatsController::getVisits(true, self::DATE_FROM, self::DATE_TO),
            $this->statsRepository->countVisits(self::DATE_FROM, self::DATE_TO, true, $this->shopIds)
        );
        $this->assertSame(
            (int) AdminStatsController::getAbandonedCarts(self::DATE_FROM . ' 00:00:00', self::DATE_TO . ' 23:59:59'),
            $this->statsRepository->countAbandonedCarts(self::DATE_FROM . ' 00:00:00', self::DATE_TO . ' 23:59:59', $this->shopIds)
        );
    }

    public function testPendingMessagesMatchLegacy(): void
    {
        $this->assertSame((int) AdminStatsController::getPendingMessages(), $this->statsRepository->countPendingMessages($this->shopIds));
    }
}
