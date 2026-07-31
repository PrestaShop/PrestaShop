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
 * Computes the refreshed value for the "Average Customer Age" KPI.
 */
final class AvgCustomerAgeKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $averageAgeInYears = round($this->statsRepository->getAverageCustomerAgeInDays($shopIds) / 365);

        $value = $this->translator->trans(
            '%value% years',
            ['%value%' => $averageAgeInYears],
            'Admin.Stats.Feature'
        );

        $this->configuration->set('AVG_CUSTOMER_AGE', $value);
        $this->configuration->set('AVG_CUSTOMER_AGE_EXPIRE', strtotime('+1 day'));

        return new KpiRefreshValue((string) $value);
    }
}
