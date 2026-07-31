<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use Configuration;
use Currency;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;

/**
 * Computes the refreshed value for the "Net Profit per Visit" KPI.
 *
 * The per-order fee computation (module flat/variable fees, carrier shipping fees) reads
 * dynamic, per-module/per-carrier Configuration keys that cannot be expressed as a SQL
 * aggregate, so it is transcribed here faithfully from the legacy
 * AdminStatsController::getExpenses() method. The default-currency and default-country
 * comparisons stay on the legacy Currency/Configuration statics, matching the two
 * explicitly allowed exceptions for this KPI.
 */
class NetProfitVisitKpiRefreshProvider implements KpiRefreshProviderInterface
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

        $totalVisitors = $this->statsRepository->countVisits($dateFrom, $dateTo, false, $shopIds);
        $netProfit = $this->statsRepository->getTotalSales($dateFrom, $dateTo, $shopIds);
        $netProfit -= $this->getExpenses($dateFrom, $dateTo, $shopIds);

        $averageMarginPercent = (int) $this->configuration->get('CONF_AVERAGE_PRODUCT_MARGIN');
        $netProfit -= $this->statsRepository->getPurchases($dateFrom, $dateTo, $averageMarginPercent, $shopIds);

        $isoCode = $this->currencyDataProvider->getDefaultCurrencyIsoCode();

        if ($totalVisitors > 0) {
            $value = $this->locale->formatPrice($netProfit / $totalVisitors, $isoCode);
        } elseif ($netProfit != 0) {
            $value = '&infin;';
        } else {
            $value = $this->locale->formatPrice(0, $isoCode);
        }

        $this->configuration->set('NETPROFIT_VISIT', $value);
        $this->configuration->set(
            'NETPROFIT_VISIT_EXPIRE',
            strtotime(date('Y-m-d 00:00:00', strtotime('+1 day')))
        );

        return new KpiRefreshValue((string) $value);
    }

    /**
     * @param int[] $shopIds
     */
    private function getExpenses(string $dateFrom, string $dateTo, array $shopIds): float
    {
        $orders = $this->statsRepository->getOrdersForExpensesComputation($dateFrom, $dateTo, $shopIds);
        $defaultCurrencyId = Currency::getDefaultCurrencyId();
        $defaultCountryId = (int) Configuration::get('PS_COUNTRY_DEFAULT');

        $expenses = 0.0;
        foreach ($orders as $order) {
            $module = strtoupper($order['module']);
            $carrierReference = strtoupper((string) $order['carrier_reference']);
            $isDefaultCurrency = $order['id_currency'] == $defaultCurrencyId;
            $isDefaultCountry = $order['id_country'] == $defaultCountryId;

            $flatFees = (float) $this->configuration->get('CONF_ORDER_FIXED') + (
                $isDefaultCurrency
                    ? (float) $this->configuration->get('CONF_' . $module . '_FIXED')
                    : (float) $this->configuration->get('CONF_' . $module . '_FIXED_FOREIGN')
            );

            $varFees = $order['total_paid_tax_incl'] * (
                $isDefaultCurrency
                    ? (float) $this->configuration->get('CONF_' . $module . '_VAR')
                    : (float) $this->configuration->get('CONF_' . $module . '_VAR_FOREIGN')
            ) / 100;

            $shippingFees = $order['total_shipping_tax_excl'] * (
                $isDefaultCountry
                    ? (float) $this->configuration->get('CONF_' . $carrierReference . '_SHIP')
                    : (float) $this->configuration->get('CONF_' . $carrierReference . '_SHIP_OVERSEAS')
            ) / 100;

            $expenses += $flatFees + $varFees + $shippingFees;
        }

        return $expenses;
    }
}
