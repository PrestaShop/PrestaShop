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

namespace LegacyTests\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;
use PrestaShopBundle\Routing\Converter\LegacyRoute;
use PrestaShopBundle\Routing\Converter\RouterProvider;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RouterProviderTest extends TestCase
{
    public function testBuildRoutes()
    {
        $router = $this->buildMultipleRouterMock([
            [
                'route_name' => 'admin_products',
                'path' => '/products',
                '_legacy_link' => 'AdminProducts',
            ],
            [
                'route_name' => 'admin_products_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminProducts:add',
                    'AdminProducts:create',
                ],
            ],
        ]);
        $routerProvider = new RouterProvider($router);
        $legacyRoutes = $routerProvider->getLegacyRoutes();
        static::assertCount(2, $legacyRoutes);
        static::assertNotEmpty($legacyRoutes['admin_products']);

        /** @var LegacyRoute $legacyRoute */
        $legacyRoute = $legacyRoutes['admin_products'];
        static::assertEquals('admin_products', $legacyRoute->getRouteName());
        static::assertSame(['AdminProducts' => [
            'index' => 'admin_products',
        ]], $legacyRoute->getControllersActions());

        $legacyRoute = $legacyRoutes['admin_products_create'];
        static::assertEquals('admin_products_create', $legacyRoute->getRouteName());
        static::assertSame(['AdminProducts' => [
            'add' => 'admin_products_create',
            'create' => 'admin_products_create',
        ]], $legacyRoute->getControllersActions());
    }

    public function testControllersActions()
    {
        $router = $this->buildMultipleRouterMock([
            [
                'route_name' => 'admin_products',
                'path' => '/products',
                '_legacy_link' => 'AdminProducts',
            ],
            [
                'route_name' => 'admin_products_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminProducts:add',
                    'AdminProducts:create',
                ],
            ],
            [
                'route_name' => 'admin_categories_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminCategories:add',
                    'AdminCategories:create',
                ],
            ],
        ]);
        $routerProvider = new RouterProvider($router);
        $controllersActions = $routerProvider->getControllersActions();
        static::assertCount(2, $controllersActions);
        static::assertSame([
            'AdminProducts' => [
                'index' => 'admin_products',
                'add' => 'admin_products_create',
                'create' => 'admin_products_create',
            ],
            'AdminCategories' => [
                'add' => 'admin_categories_create',
                'create' => 'admin_categories_create',
            ],
        ], $controllersActions);
    }

    public function testGetActionsByController()
    {
        $router = $this->buildMultipleRouterMock([
            [
                'route_name' => 'admin_products',
                'path' => '/products',
                '_legacy_link' => 'AdminProducts',
            ],
            [
                'route_name' => 'admin_products_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminProducts:add',
                    'AdminProducts:create',
                ],
            ],
            [
                'route_name' => 'admin_categories_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminCategories:add',
                    'AdminCategories:create',
                ],
            ],
        ]);
        $routerProvider = new RouterProvider($router);
        $controllerActions = $routerProvider->getActionsByController('AdminProducts');
        static::assertCount(3, $controllerActions);
        static::assertSame(['index', 'add', 'create'], $controllerActions);
    }

    public function testGetLegacyRouteByAction()
    {
        $router = $this->buildMultipleRouterMock([
            [
                'route_name' => 'admin_products',
                'path' => '/products',
                '_legacy_link' => 'AdminProducts',
            ],
            [
                'route_name' => 'admin_products_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminProducts:add',
                    'AdminProducts:create',
                ],
            ],
            [
                'route_name' => 'admin_categories_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminCategories:add',
                    'AdminCategories:create',
                ],
            ],
        ]);
        $routerProvider = new RouterProvider($router);
        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'index');
        static::assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'list');
        static::assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', '');
        static::assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', null);
        static::assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'add');
        static::assertEquals('admin_products_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'create');
        static::assertEquals('admin_products_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminCategories', 'create');
        static::assertEquals('admin_categories_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminCategories', 'add');
        static::assertEquals('admin_categories_create', $legacyRoute->getRouteName());
    }

    public function testControllerNotFound()
    {
        $router = $this->buildMultipleRouterMock([
            [
                'route_name' => 'admin_products',
                'path' => '/products',
                '_legacy_link' => 'AdminProducts',
            ],
            [
                'route_name' => 'admin_products_create',
                'path' => '/products/create',
                '_legacy_link' => [
                    'AdminProducts:add',
                    'AdminProducts:create',
                ],
            ],
        ]);
        $routerProvider = new RouterProvider($router);

        $caughtException = null;

        try {
            $routerProvider->getLegacyRouteByAction('AdminCategories', 'add');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        static::assertNotNull($caughtException);
        static::assertEquals('Could not find a route matching for legacy controller: AdminCategories', $caughtException->getMessage());

        $caughtException = null;

        try {
            $routerProvider->getLegacyRouteByAction('AdminProducts', 'edit');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        static::assertNotNull($caughtException);
        static::assertEquals('Could not find a route matching for legacy action: AdminProducts:edit', $caughtException->getMessage());
    }

    /**
     * @param array $routes
     * @return \PHPUnit_Framework_MockObject_MockObject|RouterInterface
     */
    private function buildMultipleRouterMock(array $routes)
    {
        $routeCollection = $this->buildRouteCollection($routes);

        $mockRouter = $this
            ->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRouter
            ->expects(static::once())
            ->method('getRouteCollection')
            ->willReturn($routeCollection);

        $mockRouter
            ->method('generate')
            ->will(static::returnCallback(
                function($routeName) use ($routeCollection) {
                    $route = $routeCollection->get($routeName);

                    return null !== $route ? $route->getPath() : null;
                }
            ));

        return $mockRouter;
    }

    /**
     * @param array $routes
     * @return RouteCollection
     */
    private function buildRouteCollection(array $routes)
    {
        $routeCollection = new RouteCollection();
        foreach ($routes as $route) {
            $routeDefaults = [
                '_legacy_link' => $route['_legacy_link'],
            ];
            if (!empty($route['_legacy_parameters'])) {
                $routeDefaults['_legacy_parameters'] = $route['_legacy_parameters'];
            }
            $routeCollection->add($route['route_name'], new Route(
                $route['path'],
                $routeDefaults
            ));
        }

        return $routeCollection;
    }
}
