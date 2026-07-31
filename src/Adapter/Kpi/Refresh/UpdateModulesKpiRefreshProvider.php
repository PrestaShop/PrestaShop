<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use Module;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;

/**
 * Computes the refreshed value for the "Update Modules" KPI.
 *
 * This reads modules' manifest files on disk (not a DB aggregate), so it relies directly
 * on the legacy Module::getModulesOnDisk() static helper.
 */
class UpdateModulesKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly LegacyContext $legacyContext,
        protected readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $employeeId = (int) $this->legacyContext->getContext()->employee->id;
        $modules = Module::getModulesOnDisk(true, $employeeId);

        $value = 0;
        foreach ($modules as $module) {
            if ($module->installed && !empty($module->version_addons)) {
                ++$value;
            }
        }

        $this->configuration->set('UPDATE_MODULES', $value);
        $this->configuration->set('UPDATE_MODULES_EXPIRE', strtotime('+2 min'));

        return new KpiRefreshValue((string) $value);
    }
}
