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
 * Computes the refreshed value for the "Product Average Gross Margin" KPI.
 */
class ProductAvgGrossMarginKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $value = round(100 * $this->statsRepository->getProductAverageGrossMargin($shopIds), 2) . '%';

        $tooltip = $this->translator->trans(
            'Gross margin expressed in percentage assesses how cost-effectively you sell your goods. Out of $100, you will retain $%value% to cover profit and expenses.',
            ['%value%' => $value],
            'Admin.Stats.Help'
        );

        $this->configuration->set('PRODUCT_AVG_GROSS_MARGIN', $value);
        $this->configuration->set('PRODUCT_AVG_GROSS_MARGIN_EXPIRE', strtotime('+6 hour'));

        return new KpiRefreshValue((string) $value, $tooltip);
    }
}
