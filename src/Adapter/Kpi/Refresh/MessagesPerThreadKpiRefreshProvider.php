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
 * Computes the refreshed value for the "Messages per Thread" KPI.
 */
class MessagesPerThreadKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $dateFrom = date('Y-m-d', strtotime('-31 day'));
        $dateTo = date('Y-m-d', strtotime('-1 day'));

        $counts = $this->statsRepository->getMessageCountsPerClosedThread($dateFrom, $dateTo, $shopIds);

        $value = empty($counts) ? '0' : (string) round(array_sum($counts) / count($counts), 1);

        $this->configuration->set('MESSAGES_PER_THREAD', $value);
        $this->configuration->set('MESSAGES_PER_THREAD_EXPIRE', strtotime('+12 hour'));

        return new KpiRefreshValue($value);
    }
}
