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

namespace PrestaShopBundle\Routing\Converter;

use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;

/**
 * Interface LegacyRouteProviderInterface is used by LegacyUrlConverter to fetch
 * the information about legacy routes, stored in LegacyRoute objects.
 */
interface LegacyRouteProviderInterface
{
    /**
     * Returns the list of LegacyRoute based on what was set in the routing files.
     *
     * @return LegacyRoute[]
     */
    public function getLegacyRoutes();

    /**
     * Returns the list of controllers, their action and the associated route.
     * e.g: $controllerActions = [
     *      'AdminPreferences' => [
     *          'index' => 'admin_preferences',
     *          'update' => 'admin_preferences_save',
     *      ],
     *      'AdminMeta' => [
     *          'index' => 'admin_metas_index',
     *          'search' => 'admin_metas_search',
     *      ],
     * ];.
     *
     * @return array
     */
    public function getControllersActions();

    /**
     * Return the list of actions for a defined controller.
     *
     * @param string $controller
     *
     * @return string[]
     */
    public function getActionsByController($controller);

    /**
     * Return the LegacyRoute object matching $controller and $action.
     *
     * @param string $controller
     * @param string $action
     *
     * @return LegacyRoute
     *
     * @throws RouteNotFoundException
     */
    public function getLegacyRouteByAction($controller, $action);
}
