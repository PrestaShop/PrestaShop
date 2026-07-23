<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;

/**
 * Computes the refreshed value for the "Average Order Value" KPI.
 */
class AverageOrderValueKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContextInterface $shopContext,
        protected readonly ConfigurationInterface $configuration,
        protected readonly LocaleInterface $locale,
        protected readonly CurrencyDataProviderInterface $currencyDataProvider
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

        $counters = $this->statsRepository->getAverageOrderValueCounters($dateFrom, $dateTo, $shopIds);

        $amount = $counters['orders'] ? $counters['total_paid_tax_excl'] / $counters['orders'] : 0;
        $value = $this->locale->formatPrice($amount, $this->currencyDataProvider->getDefaultCurrencyIsoCode());

        $this->configuration->set('AVG_ORDER_VALUE', $value);
        $this->configuration->set(
            'AVG_ORDER_VALUE_EXPIRE',
            strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')))
        );

        return new KpiRefreshValue((string) $value);
    }
}
