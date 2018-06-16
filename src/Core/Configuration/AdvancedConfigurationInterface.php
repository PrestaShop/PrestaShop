<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Interface AdvancedConfigurationInterface defines contract for advanced configuration
 */
interface AdvancedConfigurationInterface extends ConfigurationInterface
{
    /**
     * Get configuration value as integer
     *
     * @param string $key
     * @param int $default
     *
     * @return int
     */
    public function getInt($key, $default = 0);

    /**
     * Get configuration value as boolean
     *
     * @param string $key
     * @param bool $default
     *
     * @return bool
     */
    public function getBool($key, $default = false);

    /**
     * Get all configuration values
     *
     * @return array
     */
    public function all();

    /**
     * Get all configuration value keys
     *
     * @return string[]
     */
    public function keys();

    /**
     * Check if configuration exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Get configuration values count
     *
     * @return int
     */
    public function count();

    /**
     * Remove configuration
     *
     * @param string $key
     *
     * @return bool
     */
    public function remove($key);
}
