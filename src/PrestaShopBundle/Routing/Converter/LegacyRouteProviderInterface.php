<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
