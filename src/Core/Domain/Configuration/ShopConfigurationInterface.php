<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Configuration;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * @method getInt($key, $default = 0, ShopConstraint $shopConstraint = null)
 * @method getBoolean($key, $default = false, ShopConstraint $shopConstraint = null)
 */
interface ShopConfigurationInterface extends ConfigurationInterface
{
    /**
     * @param string $key
     * @param mixed|null $default
     * @param ShopConstraint|null $shopConstraint
     *
     * @return mixed
     */
    public function get($key, $default = null, ?ShopConstraint $shopConstraint = null);

    /**
     * @param string $key
     * @param mixed $value
     * @param ShopConstraint|null $shopConstraint
     *
     * @return ShopConfigurationInterface
     */
    public function set($key, $value, ?ShopConstraint $shopConstraint = null);

    /**
     * @param string $key
     * @param ShopConstraint|null $shopConstraint
     *
     * @return bool
     */
    public function has($key, ?ShopConstraint $shopConstraint = null);

    /**
     * @param string $key
     *
     * @return ShopConfigurationInterface
     */
    public function remove($key);

    /**
     * Get all configuration keys
     *
     * @return array
     */
    public function keys();
}
