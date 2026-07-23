<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Core\Kpi\Refresh\Exception\UnknownKpiException;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * This is a service locator that allows fetching KPI refresh providers via their KPI key.
 */
final class KpiRefreshProviderLocator
{
    public function __construct(
        #[AutowireLocator('prestashop.kpi_refresh_provider', indexAttribute: 'key')]
        protected readonly ServiceProviderInterface $providers
    ) {
    }

    /**
     * @throws UnknownKpiException
     */
    public function getProvider(string $kpiKey): KpiRefreshProviderInterface
    {
        if (!$this->providers->has($kpiKey)) {
            throw new UnknownKpiException(sprintf('No KPI refresh provider is registered for KPI key "%s".', $kpiKey));
        }

        return $this->providers->get($kpiKey);
    }
}
