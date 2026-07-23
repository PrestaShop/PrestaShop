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
 * Computes the refreshed value for the "Conversion Rate" KPI.
 */
class ConversionRateKpiRefreshProvider implements KpiRefreshProviderInterface
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

        $visitors = $this->statsRepository->countVisits($dateFrom, $dateTo, true, $shopIds);
        $orders = $this->statsRepository->countOrders($dateFrom, $dateTo, $shopIds);

        if ($visitors > 0) {
            $value = round(100 * $orders / $visitors, 2);
        } elseif ($orders > 0) {
            $value = '&infin;';
        } else {
            $value = 0;
        }
        $value = $value . '%';

        $this->configuration->set('CONVERSION_RATE', $value);
        $this->configuration->set('CONVERSION_RATE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));

        return new KpiRefreshValue((string) $value);
    }
}
