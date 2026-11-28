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
 * Computes the refreshed value for the "Orders per Customer" KPI.
 */
class OrdersPerCustomerKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $customers = $this->statsRepository->countActiveCustomers($shopIds);

        if ($customers > 0) {
            $orders = $this->statsRepository->countValidOrders($shopIds);
            $value = round($orders / $customers, 2);
        } else {
            $value = $customers;
        }

        $this->configuration->set('ORDERS_PER_CUSTOMER', $value);
        $this->configuration->set('ORDERS_PER_CUSTOMER_EXPIRE', strtotime('+1 day'));

        return new KpiRefreshValue((string) $value);
    }
}
