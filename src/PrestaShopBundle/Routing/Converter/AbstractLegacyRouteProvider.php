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

namespace PrestaShopBundle\Routing\Converter;

use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;

/**
 * Class AbstractLegacyRouteProvider.
 */
abstract class AbstractLegacyRouteProvider implements LegacyRouteProviderInterface
{
    /**
     * @var array|null
     */
    protected $controllersActions;

    /**
     * @return LegacyRoute[]
     */
    abstract public function getLegacyRoutes();

    /**
     * @return array
     */
    public function getControllersActions()
    {
        $this->initControllerActions();

        return $this->controllersActions;
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsByController($controller)
    {
        $this->initControllerActions();

        if (!isset($this->controllersActions[$controller])) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy controller: %s', $controller)
            );
        }

        return array_keys($this->controllersActions[$controller]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLegacyRouteByAction($controller, $action)
    {
        $this->initControllerActions();

        if (!isset($this->controllersActions[$controller])) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy controller: %s', $controller)
            );
        }

        $controllerActions = $this->controllersActions[$controller];
        $action = LegacyRoute::isIndexAction($action) ? 'index' : $action;
        if (!isset($controllerActions[$action])) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy action: %s', $controller . ':' . $action)
            );
        }

        $routeName = $controllerActions[$action];
        $legacyRoutes = $this->getLegacyRoutes();

        return $legacyRoutes[$routeName];
    }

    /**
     * Init the controller actions has map.
     */
    private function initControllerActions()
    {
        if (null === $this->controllersActions) {
            $this->controllersActions = [];
            /** @var LegacyRoute $legacyRoute */
            foreach ($this->getLegacyRoutes() as $legacyRoute) {
                $this->controllersActions = array_merge_recursive($this->controllersActions, $legacyRoute->getControllersActions());
            }
        }
    }
}
