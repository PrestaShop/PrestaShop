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

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;
use PrestaShopBundle\Routing\Converter\LegacyRoute;
use PrestaShopBundle\Routing\Converter\LegacyRouteFactory;
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

        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );

        $legacyRoutes = $routerProvider->getLegacyRoutes();
        $this->assertCount(2, $legacyRoutes);
        $this->assertNotEmpty($legacyRoutes['admin_products']);

        /** @var LegacyRoute $legacyRoute */
        $legacyRoute = $legacyRoutes['admin_products'];
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());
        $this->assertSame(['AdminProducts' => [
            'index' => 'admin_products',
        ]], $legacyRoute->getControllersActions());

        $legacyRoute = $legacyRoutes['admin_products_create'];
        $this->assertEquals('admin_products_create', $legacyRoute->getRouteName());
        $this->assertSame(['AdminProducts' => [
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
        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );
        $controllersActions = $routerProvider->getControllersActions();
        $this->assertCount(2, $controllersActions);
        $this->assertSame([
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

        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );

        $controllerActions = $routerProvider->getActionsByController('AdminProducts');
        $this->assertCount(3, $controllerActions);
        $this->assertSame(['index', 'add', 'create'], $controllerActions);
    }

    public function testGetActionsByControllerInsensitive()
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

        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );

        $controllerActions = $routerProvider->getActionsByController('adminproducts');
        $this->assertCount(3, $controllerActions);
        $this->assertSame(['index', 'add', 'create'], $controllerActions);

        $controllerActions = $routerProvider->getActionsByController('adMinProDuctS');
        $this->assertCount(3, $controllerActions);
        $this->assertSame(['index', 'add', 'create'], $controllerActions);
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
            [
                'route_name' => 'admin_modules_create',
                'path' => '/modules/create',
                '_legacy_link' => [
                    'AdminModules:add',
                    'AdminModules:create',
                ],
            ],
            [
                'route_name' => 'awesome_modules_create',
                'path' => '/module/awesome/modules/create',
                '_legacy_link' => [
                    'AdminModules:add',
                    'AdminModules:create',
                ],
            ],
        ]);

        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'index');
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'list');
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', '');
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', null);
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'add');
        $this->assertEquals('admin_products_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'create');
        $this->assertEquals('admin_products_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminCategories', 'create');
        $this->assertEquals('admin_categories_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminCategories', 'add');
        $this->assertEquals('admin_categories_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminModules', 'create');
        $this->assertEquals('admin_modules_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminModules', 'add');
        $this->assertEquals('admin_modules_create', $legacyRoute->getRouteName());
    }

    public function testGetLegacyRouteByActionInsensitive()
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
            [
                'route_name' => 'admin_modules_create',
                'path' => '/modules/create',
                '_legacy_link' => [
                    'AdminModules:add',
                    'AdminModules:create',
                ],
            ],
            [
                'route_name' => 'awesome_modules_create',
                'path' => '/module/awesome/modules/create',
                '_legacy_link' => [
                    'AdminModules:add',
                    'AdminModules:create',
                ],
            ],
        ]);

        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );

        $legacyRoute = $routerProvider->getLegacyRouteByAction('adminproducts', 'index');
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdMinProDucts', '');
        $this->assertEquals('admin_products', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('ADMINPRODUCTS', 'add');
        $this->assertEquals('admin_products_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('ADMINPRODUCTS', 'ADD');
        $this->assertEquals('admin_products_create', $legacyRoute->getRouteName());

        $legacyRoute = $routerProvider->getLegacyRouteByAction('AdminProducts', 'Create');
        $this->assertEquals('admin_products_create', $legacyRoute->getRouteName());
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

        $routerProvider = new RouterProvider(
            $router,
            new LegacyRouteFactory($this->createMock(FeatureFlagRepository::class))
        );

        $caughtException = null;

        try {
            $routerProvider->getLegacyRouteByAction('AdminCategories', 'add');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertEquals('Could not find a route matching for legacy controller: AdminCategories', $caughtException->getMessage());

        $caughtException = null;

        try {
            $routerProvider->getLegacyRouteByAction('AdminProducts', 'edit');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertEquals('Could not find a route matching for legacy action: AdminProducts:edit', $caughtException->getMessage());
    }

    /**
     * @param array $routes
     *
     * @return MockObject|RouterInterface
     */
    private function buildMultipleRouterMock(array $routes)
    {
        $routeCollection = $this->buildRouteCollection($routes);

        $mockRouter = $this
            ->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRouter
            ->expects($this->once())
            ->method('getRouteCollection')
            ->willReturn($routeCollection);

        $mockRouter
            ->method('generate')
            ->will($this->returnCallback(
                function ($routeName) use ($routeCollection) {
                    $route = $routeCollection->get($routeName);

                    return null !== $route ? $route->getPath() : null;
                }
            ));

        return $mockRouter;
    }

    /**
     * @param array $routes
     *
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
