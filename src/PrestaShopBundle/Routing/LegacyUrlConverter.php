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
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class LegacyUrlConverter
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Route[]
     */
    private $legacyRoutes;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->legacyRoutes = [];
        /** @var Route $route */
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            $routeDefaults = $route->getDefaults();
            if (!empty($routeDefaults['_legacy_link'])) {
                $this->legacyRoutes[$routeName] = $route;
            }
        }
    }

    /**
     * @param array $parameters
     * @return string
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function convertByParameters(array $parameters)
    {
        $routeName = $this->findRouteNameByArguments($parameters);
        $parameters = $this->convertParametersForRoute($parameters, $routeName);

        return $this->router->generate($routeName, $parameters);
    }

    /**
     * @param string $url
     * @return string
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
     * @param array $parameters
     * @param string $routeName
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

        unset($parameters['controller']);
        unset($parameters['action']);

        return $parameters;
    }

    /**
     * @param array $arguments
     * @return string
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    private function findRouteNameByArguments(array $arguments)
    {
        if (empty($arguments['controller'])) {
            throw new ArgumentException('Missing required controller argument');
        }
        $legacyController = $arguments['controller'];
        $legacyAction = !empty($arguments['action']) ? $arguments['action'] : null;

        foreach ($this->legacyRoutes as $routeName => $route) {
            $legacyLink = $this->breakLegacyLink($route);
            if ($legacyController != $legacyLink['controller']) {
                continue;
            }

            if ($this->isIndexAction($legacyAction) && $this->isIndexAction($legacyLink['action'])) {
                return $routeName;
            } elseif (!empty($legacyAction) && $legacyAction === $legacyLink['action']) {
                return $routeName;
            }
        }

        throw new RouteNotFoundException(
            'Could not find a route matching for legacy controller: %s',
            [$legacyController.(null !== $legacyAction ? ':'.$legacyAction : '')]
        );
    }

    /**
     * @param Route $route
     * @return array
     */
    private function breakLegacyLink(Route $route)
    {
        $routeDefaults = $route->getDefaults();
        $legacyLink = $routeDefaults['_legacy_link'];
        $linkParts = explode(':', $legacyLink);
        $legacyController = $linkParts[0];
        $legacyAction = count($linkParts) > 1 ? $linkParts[1] : null;

        return [
            'controller' => $legacyController,
            'action' => $legacyAction,
        ];
    }

    /**
     * @param string|null $action
     * @return bool
     */
    private function isIndexAction($action)
    {
        $indexAliases = ['list', 'index'];

        return empty($action) || in_array(strtolower($action), $indexAliases);
    }
}
