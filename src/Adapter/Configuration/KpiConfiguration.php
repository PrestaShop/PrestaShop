<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use ConfigurationKPI;
use PrestaShop\PrestaShop\Adapter\Configuration;

/**
 * Class KpiConfiguration provides access to legacy ConfigurationKpi methods.
 */
class KpiConfiguration extends Configuration
{
    /**
     * Changes configuration definition before calling it's methods.
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
