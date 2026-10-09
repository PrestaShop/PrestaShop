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
     * {@inheritdoc}
     */
    public function get($key, $default = null, ?ShopConstraint $shopConstraint = null): mixed
    {
        return $this->onKpiTable(fn () => parent::get($key, $default, $shopConstraint));
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, ?ShopConstraint $shopConstraint = null, array $options = [])
    {
        return $this->onKpiTable(fn () => parent::set($key, $value, $shopConstraint, $options));
    }

    /**
     * {@inheritdoc}
     */
    public function has($key, ?ShopConstraint $shopConstraint = null): bool
    {
        return $this->onKpiTable(fn () => parent::has($key, $shopConstraint));
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return $this->onKpiTable(fn () => parent::remove($key));
    }

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
            return $this->onKpiTable(fn () => call_user_func_array([$this, $name], $arguments));
        }
    }

    /**
     * Runs a call against the KPI configuration table and restores the regular definition after it.
     *
     * WHY: the definition being swapped is static and shared by every Configuration call in the
     * request, so it has to be restored even when the call throws, or the rest of the request reads
     * the KPI table.
     *
     * @return mixed
     */
    private function onKpiTable(callable $call)
    {
        ConfigurationKPI::setKpiDefinition();

        try {
            return $call();
        } finally {
            ConfigurationKPI::unsetKpiDefinition();
        }
    }
}
