<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;

/**
 * Computes the refreshed value for the "% of Product in Stock" KPI.
 */
class PercentProductStockKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContextInterface $shopContext,
        protected readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $shopIds = $this->shopContext->getContextShopIds();
        $counters = $this->statsRepository->getProductStockCounters($shopIds);

        $value = round($counters['total'] ? 100 * $counters['with_stock'] / $counters['total'] : 0, 2) . '%';

        $this->configuration->set('PERCENT_PRODUCT_STOCK', $value);
        $this->configuration->set('PERCENT_PRODUCT_STOCK_EXPIRE', strtotime('+4 hour'));

        return new KpiRefreshValue((string) $value);
    }
}
