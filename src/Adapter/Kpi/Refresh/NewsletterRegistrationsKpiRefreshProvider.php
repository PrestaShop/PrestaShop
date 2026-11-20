<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use Shop;

/**
 * Computes the refreshed value for the "Newsletter Registrations" KPI.
 */
class NewsletterRegistrationsKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContext $shopContext,
        protected readonly ConfigurationInterface $configuration,
        protected readonly ModuleDataProvider $moduleDataProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        // Customers of the shops sharing orders, as the legacy page counts them
        $shopIds = array_map('intval', $this->shopContext->getContextListShopID(Shop::SHARE_ORDER));
        $value = $this->statsRepository->countActiveNewsletterCustomers($shopIds);

        if ($this->moduleDataProvider->isInstalled('ps_emailsubscription')) {
            $value += $this->statsRepository->countActiveEmailSubscriptions($shopIds);
        }

        $this->configuration->set('NEWSLETTER_REGISTRATIONS', $value);
        $this->configuration->set('NEWSLETTER_REGISTRATIONS_EXPIRE', strtotime('+6 hour'));

        return new KpiRefreshValue((string) $value);
    }
}
