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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Computes the refreshed value for the "80/20 Sales Catalog" KPI.
 */
class Sales8020KpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContextInterface $shopContext,
        protected readonly ConfigurationInterface $configuration,
        protected readonly TranslatorInterface $translator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $shopIds = $this->shopContext->getContextShopIds();
        $dateFrom = date('Y-m-d', strtotime('-30 days'));
        $dateTo = date('Y-m-d');

        $distinctProducts = $this->statsRepository->countDistinctProductsSold($dateFrom, $dateTo, $shopIds);

        $tooltip = null;
        if ($distinctProducts === 0) {
            $value = '0%';
        } else {
            $totalProducts = $this->statsRepository->getProductActivationCounters($shopIds)['total'];
            $percent = round(100 * $distinctProducts / $totalProducts) . '%';

            $tooltip = $this->translator->trans(
                'Within your catalog, %value% of your products have had sales in the last 30 days',
                ['%value%' => $percent],
                'Admin.Stats.Help'
            );

            $value = $this->translator->trans(
                '%value%% of your Catalog',
                ['%value%' => $percent],
                'Admin.Stats.Feature'
            );
        }

        $this->configuration->set('8020_SALES_CATALOG', $value);
        $this->configuration->set('8020_SALES_CATALOG_EXPIRE', strtotime('+12 hour'));

        return new KpiRefreshValue((string) $value, $tooltip);
    }
}
