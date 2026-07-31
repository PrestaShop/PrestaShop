<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use Country;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Computes the refreshed value for the "Main Country" KPI.
 *
 * No clean repository method exposes a country's localized name by id + language id (the
 * CountryRepository read is not language-scoped), so a direct legacy Country ObjectModel read
 * is used here, same allowance as for the Category ObjectModel elsewhere in this namespace.
 */
class MainCountryKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContextInterface $shopContext,
        protected readonly ConfigurationInterface $configuration,
        protected readonly TranslatorInterface $translator,
        protected readonly LanguageContext $languageContext
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $shopIds = $this->shopContext->getContextShopIds();
        $dateFrom = date('Y-m-d', strtotime('-30 day'));
        $dateTo = date('Y-m-d');

        $row = $this->statsRepository->getMainCountry($dateFrom, $dateTo, $shopIds);

        if (null === $row) {
            $value = $this->translator->trans('No orders', [], 'Admin.Stats.Feature');
        } else {
            $totalOrders = $this->statsRepository->countOrders($dateFrom, $dateTo, $shopIds);
            $percent = round(100 * $row['orders'] / $totalOrders, 1);
            $country = new Country($row['id_country'], $this->languageContext->getId());

            $value = $this->translator->trans(
                '%d%% %s',
                ['%d%%' => $percent, '%s' => $country->name],
                'Admin.Stats.Feature'
            );
        }

        $this->configuration->set('MAIN_COUNTRY', $value);
        $this->configuration->set('MAIN_COUNTRY_EXPIRE', strtotime('+1 day'));

        return new KpiRefreshValue((string) $value);
    }
}
