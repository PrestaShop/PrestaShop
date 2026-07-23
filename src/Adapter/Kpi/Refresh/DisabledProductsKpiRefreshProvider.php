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
 * Computes the refreshed value for the "Disabled Products" KPI.
 */
class DisabledProductsKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $counters = $this->statsRepository->getProductActivationCounters($shopIds);

        $value = round($counters['total'] ? 100 * $counters['disabled'] / $counters['total'] : 0, 2) . '%';

        $tooltip = $this->translator->trans(
            '%value% of your products are disabled and not visible to your customers',
            ['%value%' => $value],
            'Admin.Stats.Help'
        );

        $this->configuration->set('DISABLED_PRODUCTS', $value);
        $this->configuration->set('DISABLED_PRODUCTS_EXPIRE', strtotime('+2 hour'));

        return new KpiRefreshValue((string) $value, $tooltip);
    }
}
