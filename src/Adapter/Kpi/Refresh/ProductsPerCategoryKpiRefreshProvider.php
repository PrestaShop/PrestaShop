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
 * Computes the refreshed value for the "Average number of products per category" KPI.
 */
class ProductsPerCategoryKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $products = $this->statsRepository->getProductActivationCounters($shopIds)['total'];
        $categories = $this->statsRepository->countTotalCategories($shopIds);

        $value = $categories > 0 ? (string) round($products / $categories) : '0';

        $this->configuration->set('PRODUCTS_PER_CATEGORY', $value);
        $this->configuration->set('PRODUCTS_PER_CATEGORY_EXPIRE', strtotime('+1 hour'));

        return new KpiRefreshValue($value);
    }
}
