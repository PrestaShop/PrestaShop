<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Routing\Converter;

use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class AbstractLegacyRouteProvider.
 */
abstract class AbstractLegacyRouteProvider implements LegacyRouteProviderInterface
{
    /**
     * @var AdapterInterface
     */
    protected $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @var array|null
     */
    protected $controllersActions;

    /**
     * This is the only method that child classes need to implement.
     *
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

        $controllerActions = $this->getControllerActions($controller);
        if (null === $controllerActions) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy controller: %s', $controller)
            );
        }

        return array_keys($controllerActions);
    }

    /**
     * {@inheritdoc}
     */
    public function getLegacyRouteByAction($controller, $action)
    {
        $this->initControllerActions();

        $controllerActions = $this->getControllerActions($controller);
        if (null === $controllerActions) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy controller: %s', $controller)
            );
        }

        $action = LegacyRoute::isIndexAction($action) ? 'index' : $action;
        $routeName = $this->getRouteName($controllerActions, $action);
        if (null === $routeName) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy action: %s', $controller . ':' . $action)
            );
        }

        return $this->getLegacyRoutes()[$routeName];
    }

    /**
     * Get the route name.
     *
     * @param array $controllerActions
     * @param string $action
     *
     * @return string|null
     */
    private function getRouteName(array $controllerActions, $action)
    {
        foreach ($controllerActions as $controllerAction => $actionRoute) {
            if (strtolower($controllerAction) === strtolower($action)) {
                return is_array($actionRoute) ? $actionRoute[0] : $actionRoute;
            }
        }

        return null;
    }

    /**
     * Init the controller actions has map.
     */
    private function initControllerActions()
    {
        $cacheItem = $this->cache->getItem('controller_actions');

        if (!$cacheItem->isHit()) {
            $controllersActions = [[]];
            /** @var LegacyRoute $legacyRoute */
            foreach ($this->getLegacyRoutes() as $legacyRoute) {
                $controllersActions[] = $legacyRoute->getControllersActions();
            }

            $controllersActions = array_merge_recursive(...$this->controllersActions);
            $cacheItem->set($controllersActions);
            $this->cache->save($cacheItem);
        }

        $this->controllersActions = $cacheItem->get();

        return $cacheItem->get();
    }

    /**
     * @param string $controller
     *
     * @return array|null
     */
    private function getControllerActions($controller)
    {
        foreach ($this->controllersActions as $listController => $actions) {
            if (strtolower($listController) === strtolower($controller)) {
                return $actions;
            }
        }

        return null;
    }
}
