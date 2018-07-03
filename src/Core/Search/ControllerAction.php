<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Search;

/**
 * Utility class to extract information from modern controller FQCN.
 */
final class ControllerAction
{
    /**
     * Retrieve the Controller's action and name from a FQCN.
     *
     * @param string $controller
     * @return array
     */
    public static function fromString($controller)
    {
        return [
            self::getControllerName($controller),
            self::getActionName($controller),
        ];
    }

    /**
     * Get current controller name
     * @param string $controller the full controller name.
     * @return string
     */
    public static function getControllerName($controller)
    {
        // Set an offset to avoid matching the first occurrence of Controller.
        preg_match('/([a-zA-Z]*)Controller/', $controller, $matches, 0, 30);

        return !empty($matches) ? strtolower($matches[1]) : 'N/A';
    }

    /**
     * Get current action name
     * @param string $controller the full controller name.
     * @return string
     */
    public static function getActionName($controller)
    {
        preg_match('/::([a-zA-Z]*)Action/', $controller, $matches);

        return !empty($matches) ? strtolower($matches[1]) : 'N/A';
    }
}
