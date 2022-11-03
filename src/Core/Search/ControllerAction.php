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

namespace PrestaShop\PrestaShop\Core\Search;

/**
 * Utility class to extract information from modern controller FQCN.
 */
final class ControllerAction
{
    /**
     * Retrieve the Controller's action and name from a FQCN notation of Symfony controller.
     * This function expects a string like MyNamespace\Foo\FooController::bazAction.
     *
     * @param string $controller
     *
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
     * Get current controller name.
     *
     * @param string $controller the full controller name
     *
     * @return string
     */
    private static function getControllerName($controller)
    {
        preg_match('~(\w+)Controller(?:::(?:\w+)Action)?$~', $controller, $matches);

        return !empty($matches) ? strtolower($matches[1]) : 'N/A';
    }

    /**
     * Get current action name.
     *
     * @param string $controller the full controller name
     *
     * @return string
     */
    private static function getActionName($controller)
    {
        preg_match('~::(\w+)Action$~', $controller, $matches);

        return !empty($matches) ? strtolower($matches[1]) : 'N/A';
    }
}
