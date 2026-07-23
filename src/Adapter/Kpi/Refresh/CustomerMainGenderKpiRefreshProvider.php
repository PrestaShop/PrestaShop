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
 * Computes the refreshed value for the "Customer Main Gender" KPI.
 */
final class CustomerMainGenderKpiRefreshProvider implements KpiRefreshProviderInterface
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
        $counters = $this->statsRepository->getCustomerGenderCounters($shopIds);

        if (null === $counters) {
            $value = $this->translator->trans('No customers', [], 'Admin.Stats.Feature');
        } else {
            if ($counters['male'] > $counters['female'] && $counters['male'] >= $counters['neutral']) {
                $percentage = round(100 * $counters['male'] / $counters['total']);
                $value = $this->translator->trans(
                    '%percentage%% Male Customers',
                    ['%percentage%' => $percentage],
                    'Admin.Stats.Feature'
                );
            } elseif ($counters['female'] >= $counters['male'] && $counters['female'] >= $counters['neutral']) {
                $percentage = round(100 * $counters['female'] / $counters['total']);
                $value = $this->translator->trans(
                    '%percentage%% Female Customers',
                    ['%percentage%' => $percentage],
                    'Admin.Stats.Feature'
                );
            } else {
                $percentage = round(100 * $counters['neutral'] / $counters['total']);
                $value = $this->translator->trans(
                    '%percentage%% Neutral Customers',
                    ['%percentage%' => $percentage],
                    'Admin.Stats.Feature'
                );
            }
        }

        $this->configuration->set('CUSTOMER_MAIN_GENDER', $value);
        $this->configuration->set('CUSTOMER_MAIN_GENDER_EXPIRE', strtotime('+1 day'));

        return new KpiRefreshValue((string) $value);
    }
}
