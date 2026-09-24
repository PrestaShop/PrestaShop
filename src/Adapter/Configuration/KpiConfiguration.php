<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use ConfigurationKPI;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Class KpiConfiguration provides access to legacy ConfigurationKpi methods.
 */
class KpiConfiguration extends Configuration
{
    /**
     * Read KPI configuration values from the ps_configuration_kpi table.
     *
     * The parent Configuration::get() targets the standard ps_configuration
     * table. Because get() is defined in the parent, __call() never intercepts
     * it, so without this override every KPI cache lookup would fall back to
     * ps_configuration (which does not store KPI keys) and return null. This
     * caused $helper->refresh to always evaluate to true in the *Kpi adapters,
     * defeating the cache and forcing the four KPI AJAX calls (conversion_rate,
     * abandoned_cart, average_order_value, netprofit_visit) to fire on every
     * back-office orders/carts list page load.
     */
    public function get($key, $default = null, ?ShopConstraint $shopConstraint = null): mixed
    {
        ConfigurationKPI::setKpiDefinition();
        try {
            return parent::get($key, $default, $shopConstraint);
        } finally {
            ConfigurationKPI::unsetKpiDefinition();
        }
    }

    /**
     * Changes configuration definition before calling its methods.
     *
     * @param string $name
     * @param mixed $arguments
     *
     * @return mixed|void
     */
    public function __call($name, $arguments)
    {
        if (is_callable([$this, $name])) {
            ConfigurationKPI::setKpiDefinition();
            $result = call_user_func([$this, $name], $arguments);
            ConfigurationKPI::unsetKpiDefinition();

            return $result;
        }
    }
}
