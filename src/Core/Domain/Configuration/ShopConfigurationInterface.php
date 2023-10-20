<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public function get($key, $default = null, ShopConstraint $shopConstraint = null);

    /**
     * @param string $key
     * @param mixed $value
     * @param ShopConstraint|null $shopConstraint
     *
     * @return ShopConfigurationInterface
     */
    public function set($key, $value, ShopConstraint $shopConstraint = null);

    /**
     * @param string $key
     * @param ShopConstraint|null $shopConstraint
     *
     * @return bool
     */
    public function has($key, ShopConstraint $shopConstraint = null);

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
