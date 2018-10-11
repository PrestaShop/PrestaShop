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

namespace PrestaShopBundle\Routing;

use PrestaShopBundle\Routing\Exception\ArgumentException;
use PrestaShopBundle\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class LegacyUrlConverter is able to convert query parameters or an url into a
 * migrated Symfony url. It uses information set in the routes via _legacy_link
 * to do so.
 */
final class LegacyUrlConverter
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $legacyRoutes;

    /**
     * @var array
     */
    private $controllersActions;

    /**
     * LegacyUrlConverter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function convertByParameters(array $parameters)
    {
        if (empty($parameters['controller'])) {
            throw new ArgumentException('Missing required controller argument');
        }

        $this->initRoutes();
        if (!isset($this->controllersActions[$parameters['controller']])) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy controller: %s', $parameters['controller'])
            );
        }

        $routeName = $this->findRouteNameByParameters($parameters);
        $parameters = $this->convertParametersForRoute($parameters, $routeName);

        return $this->router->generate($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $url
     *
     * @return string
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function convertByUrl($url)
    {
        $parsedUrl = parse_url($url);
        $parameters = array();
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parameters);
        }

        return $this->convertByParameters($parameters);
    }

    /**
     * This conversion method is used by the listener, indeed it needs to update the
     * router context because it is executed before the RouterListener. Thus the router
     * does not have the appropriate information to generate an admin url and would redirect
     * to the front office.
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function convertByRequest(Request $request)
    {
        $this->router->getContext()->fromRequest($request);

        return $this->convertByUrl($request->getUri());
    }

    /**
     * @param array $parameters
     * @param string $routeName
     *
     * @return array
     */
    private function convertParametersForRoute(array $parameters, $routeName)
    {
        $route = $this->legacyRoutes[$routeName];
        $routeDefaults = $route->getDefaults();
        if (!empty($routeDefaults['_legacy_parameters']) && is_array($routeDefaults['_legacy_parameters'])) {
            foreach ($routeDefaults['_legacy_parameters'] as $legacyParameter => $parameter) {
                if (isset($parameters[$legacyParameter])) {
                    $parameters[$parameter] = $parameters[$legacyParameter];
                    unset($parameters[$legacyParameter]);
                }
            }
        }

        $legacyAction = $this->getActionFromParameters($parameters);
        unset($parameters['controller']);
        unset($parameters['action']);
        unset($parameters[$legacyAction]);

        return $parameters;
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    private function findRouteNameByParameters(array $parameters)
    {
        $legacyController = $parameters['controller'];
        $controllerActions = $this->controllersActions[$legacyController];

        $legacyAction = $this->getActionFromParameters($parameters);
        if (!isset($controllerActions[$legacyAction])) {
            throw new RouteNotFoundException(
                sprintf('Could not find a route matching for legacy action: %s', $legacyController . ':' . $legacyAction)
            );
        }

        return $controllerActions[$legacyAction];
    }

    /**
     * @param array $parameters
     *
     * @return null|string
     */
    private function getActionFromParameters(array $parameters)
    {
        $legacyAction = null;

        if (!empty($parameters['action'])) {
            $legacyAction = $parameters['action'];
        }

        //Actions can be defined as simple query parameter (e.g: ?controller=AdminProducts&save)
        if (null === $legacyAction) {
            //We prioritize the actions defined in the migrated routes
            $controllerActions = array_keys($this->controllersActions[$parameters['controller']]);
            foreach ($parameters as $parameter => $value) {
                if (in_array($parameter, $controllerActions)) {
                    $legacyAction = $parameter;
                    break;
                }
            }
        }

        //Last chance if a non migrated action is present (note: a bit risky since any empty parameter can be
        //interpreted as an action.. but some old link need this feature, ?controller=AdminModulesPositions&addToHook)
        if (null === $legacyAction) {
            foreach ($parameters as $parameter => $value) {
                if ($value === '' || 1 === (int) $value) {
                    $legacyAction = $parameter;
                    break;
                }
            }
        }

        return $this->isIndexAction($legacyAction) ? 'index' : $legacyAction;
    }

    /**
     * @param Route $route
     *
     * @return array
     */
    private function breakLegacyLinks(Route $route)
    {
        $routeDefaults = $route->getDefaults();
        $legacyLinks = $routeDefaults['_legacy_link'];
        if (!is_array($legacyLinks)) {
            $legacyLinks = [$legacyLinks];
        }

        $brokenLegacyLinks = [];
        foreach ($legacyLinks as $legacyLink) {
            $linkParts = explode(':', $legacyLink);
            $legacyController = $linkParts[0];
            $legacyAction = isset($linkParts[1]) ? $linkParts[1] : null;
            $brokenLegacyLinks[] = [
                'controller' => $legacyController,
                'action' => $legacyAction,
            ];
        }

        return $brokenLegacyLinks;
    }

    /**
     * @param string|null $action
     *
     * @return bool
     */
    private function isIndexAction($action)
    {
        $indexAliases = ['list', 'index'];

        return empty($action) || in_array(strtolower($action), $indexAliases);
    }

    /**
     * Delay initialisation of routes and controller action.
     */
    private function initRoutes()
    {
        if (null !== $this->legacyRoutes && null !== $this->controllersActions) {
            return;
        }

        $this->legacyRoutes = [];
        $this->controllersActions = [];
        /** @var Route $route */
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            $this->addRoute($routeName, $route);
        }
    }

    /**
     * @param string $routeName
     * @param Route $route
     */
    private function addRoute($routeName, Route $route)
    {
        $routeDefaults = $route->getDefaults();
        if (empty($routeDefaults['_legacy_link'])) {
            return;
        }

        $this->legacyRoutes[$routeName] = $route;

        $legacyLinks = $this->breakLegacyLinks($route);
        foreach ($legacyLinks as $legacyLink) {
            $controller = $legacyLink['controller'];
            if (!isset($this->controllersActions[$controller])) {
                $this->controllersActions[$controller] = [];
            }

            $action = $this->isIndexAction($legacyLink['action']) ? 'index' : $legacyLink['action'];
            $this->controllersActions[$controller][$action] = $routeName;
        }
    }
}
