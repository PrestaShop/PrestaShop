<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            throw new RouteNotFoundException(sprintf('Could not find a route matching for legacy controller: %s', $controller));
        }

        return array_keys($controllerActions);
    }

    /**
     * Return the LegacyRoute object matching $controller and $action.
     *
     * @param string $controller
     * @param string|null $action
     *
     * @return LegacyRoute
     *
     * @throws RouteNotFoundException
     */
    public function getLegacyRouteByAction($controller, $action)
    {
        $this->initControllerActions();

        $controllerActions = $this->getControllerActions($controller);
        if (null === $controllerActions) {
            throw new RouteNotFoundException(sprintf('Could not find a route matching for legacy controller: %s', $controller));
        }

        $action = LegacyRoute::isIndexAction($action) ? 'index' : $action;
        $routeName = $this->getRouteName($controllerActions, $action);
        if (null === $routeName) {
            throw new RouteNotFoundException(sprintf('Could not find a route matching for legacy action: %s', $controller . ':' . $action));
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
        $routeName = null;
        foreach ($controllerActions as $controllerAction => $actionRoute) {
            if (strtolower($controllerAction) == strtolower($action)) {
                $routeName = $actionRoute;
                break;
            }
        }

        if (is_array($routeName)) {
            return $routeName[0];
        }

        return $routeName;
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

    /**
     * @param string $controller
     *
     * @return array|null
     */
    private function getControllerActions($controller)
    {
        $controllerActions = null;
        foreach ($this->controllersActions as $listController => $actions) {
            if (strtolower($listController) == strtolower($controller)) {
                $controllerActions = $actions;
                break;
            }
        }

        return $controllerActions;
    }
}
