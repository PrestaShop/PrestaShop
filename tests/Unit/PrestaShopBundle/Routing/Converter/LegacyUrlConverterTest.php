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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShopBundle\Routing\Converter\Exception\ArgumentException;
use PrestaShopBundle\Routing\Converter\Exception\RouteNotFoundException;
use PrestaShopBundle\Routing\Converter\LegacyRouteFactory;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use PrestaShopBundle\Routing\Converter\RouterProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class LegacyUrlConverterTest extends TestCase
{
    public function testBasic()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
        ]);
        $this->assertEquals('/products', $url);

        $url = $converter->convertByUrl('?controller=AdminProducts');
        $this->assertEquals('/products', $url);
    }

    public function testMinifiedController()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'adminproducts',
        ]);
        $this->assertEquals('/products', $url);

        $url = $converter->convertByUrl('?controller=adminproducts');
        $this->assertEquals('/products', $url);
    }

    public function testBasicTab()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'tab' => 'AdminProducts',
        ]);
        $this->assertEquals('/products', $url);

        $url = $converter->convertByUrl('?tab=AdminProducts');
        $this->assertEquals('/products', $url);
    }

    public function testIndexAlias()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'action' => 'index',
        ]);
        $this->assertEquals('/products', $url);

        $url = $converter->convertByUrl('?controller=AdminProducts&action=index');
        $this->assertEquals('/products', $url);
    }

    public function testTabIndexAlias()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'tab' => 'AdminProducts',
            'action' => 'index',
        ]);
        $this->assertEquals('/products', $url);

        $url = $converter->convertByUrl('?tab=AdminProducts&action=index');
        $this->assertEquals('/products', $url);
    }

    public function testInsensitiveListAlias()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'action' => 'LIST',
        ]);
        $this->assertEquals('/products', $url);

        $url = $converter->convertByUrl('?controller=AdminProducts&action=LIST');
        $this->assertEquals('/products', $url);
    }

    public function testAction()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'action' => 'create',
        ]);
        $this->assertEquals('/products/create', $url);

        $url = $converter->convertByUrl('?controller=AdminProducts&action=create');
        $this->assertEquals('/products/create', $url);
    }

    public function testTabAction()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'tab' => 'AdminProducts',
            'action' => 'create',
        ]);
        $this->assertEquals('/products/create', $url);

        $url = $converter->convertByUrl('?tab=AdminProducts&action=create');
        $this->assertEquals('/products/create', $url);
    }

    public function testActionWithTrue()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'create' => true,
        ]);
        $this->assertEquals('/products/create', $url);

        $url = $converter->convertByUrl('?controller=AdminProducts&create=1');
        $this->assertEquals('/products/create', $url);
    }

    public function testActionWithEmptyString()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'create' => '',
        ]);
        $this->assertEquals('/products/create', $url);

        $url = $converter->convertByUrl('?controller=AdminProducts&create');
        $this->assertEquals('/products/create', $url);
    }

    public function testMultipleLegacyLinks()
    {
        $router = $this->buildRouterMock('admin_module_manage', '/manage/{category}/{keyword}', [
            'AdminModulesManage',
            'AdminModulesSf',
        ]);
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));

        //First controller
        $url = $converter->convertByParameters([
            'controller' => 'AdminModulesManage',
        ]);
        $this->assertEquals('/manage/{category}/{keyword}', $url);

        $url = $converter->convertByUrl('?controller=AdminModulesManage');
        $this->assertEquals('/manage/{category}/{keyword}', $url);

        //Second controller
        $url = $converter->convertByParameters([
            'controller' => 'AdminModulesSf',
        ]);
        $this->assertEquals('/manage/{category}/{keyword}', $url);

        $url = $converter->convertByUrl('?controller=AdminModulesSf');
        $this->assertEquals('/manage/{category}/{keyword}', $url);
    }

    /**
     * If a non existent action is used in the url (meaning one that has not been
     * migrated yet) it must not return the index route but throw an Exception instead
     */
    public function testNonExistentAction()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));

        $caughtException = null;

        try {
            $converter->convertByParameters([
                'controller' => 'AdminProducts',
                'action' => 'create',
            ]);
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);

        try {
            $converter->convertByParameters([
                'controller' => 'AdminProducts',
                'create' => true,
            ]);
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);

        try {
            $converter->convertByParameters([
                'controller' => 'AdminProducts',
                'create' => '',
            ]);
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);

        try {
            $converter->convertByUrl('?controller=AdminProducts&action=create');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);

        try {
            $converter->convertByUrl('?controller=AdminProducts&create=1');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);

        try {
            $converter->convertByUrl('?controller=AdminProducts&create=');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);

        try {
            $converter->convertByUrl('?controller=AdminProducts&create');
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
    }

    /**
     *  The parameter id_product|product_id must not be considered as a non migrated action
     *  (as would have been ?controller=AdminProducts&export_products_xml=1)
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function testIdEqualToOne()
    {
        $router = $this->buildRouterMock('admin_products_index', '/products', 'AdminProducts');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $convertedUrl = $converter->convertByUrl('?controller=AdminProducts&id_product=1');
        $this->assertEquals('/products', $convertedUrl);

        $convertedUrl = $converter->convertByUrl('?controller=AdminProducts&product_id=1');
        $this->assertEquals('/products', $convertedUrl);
    }

    public function testActionWithArgument()
    {
        $router = $this->buildRouterMock(
            'admin_products_edit',
            '/products/edit/{id}',
            'AdminProducts:edit',
            ['id_product' => 'id'],
            ['id' => 2]
        );

        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'action' => 'edit',
            'id_product' => 2,
        ]);
        //Mock returns the original path but the parameters are checked
        $this->assertEquals('/products/edit/{id}', $url);

        //Mock returns the original path but the parameters are checked
        $url = $converter->convertByUrl('?controller=AdminProducts&action=edit&id_product=2');
        $this->assertEquals('/products/edit/{id}', $url);

        //Try with id parameter like in route
        $url = $converter->convertByUrl('?controller=AdminProducts&action=edit&id=2');
        $this->assertEquals('/products/edit/{id}', $url);
    }

    public function testActionWithArgumentWithoutMatching()
    {
        $router = $this->buildRouterMock(
            'admin_products_edit',
            '/products/edit/{id}',
            'AdminProducts:edit',
            null, //No parameters matching rules defined
            ['id' => 42]
        );

        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'action' => 'edit',
            'id' => '42',
        ]);
        //Mock returns the original path but the parameters are checked
        $this->assertEquals('/products/edit/{id}', $url);

        //Mock returns the original path but the parameters are checked
        $url = $converter->convertByUrl('?controller=AdminProducts&action=edit&id=42');
        $this->assertEquals('/products/edit/{id}', $url);
    }

    public function testConvertByRequest()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $request = new Request([
            'controller' => 'AdminProducts',
            'action' => 'create',
        ], [], [], [], [], [
            'SERVER_PORT' => 80,
            'SERVER_NAME' => 'localhost',
        ]);
        $request->overrideGlobals();

        $contextMock = $this->getMockBuilder(RequestContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock
            ->expects($this->once())
            ->method('fromRequest')
            ->with(
                $this->equalTo($request)
            );
        $router
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($contextMock);

        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $url = $converter->convertByRequest($request);
        $this->assertEquals('/products/create', $url);
    }

    public function testMissingController()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));
        $this->expectException(ArgumentException::class);
        $this->expectExceptionMessage('Missing required controller argument');
        $converter->convertByParameters([
            'action' => 'create',
        ]);
    }

    public function testControllerNotFound()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));

        /** @var RouteNotFoundException $caughtException */
        $caughtException = null;

        try {
            $converter->convertByParameters([
                'controller' => 'AdminModules',
                'action' => 'configure',
            ]);
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(RouteNotFoundException::class, $caughtException);
        $this->assertEquals('Could not find a route matching for legacy controller: AdminModules', $caughtException->getMessage());
    }

    public function testActionNotFound()
    {
        $router = $this->buildRouterMock('admin_products_create', '/products/create', 'AdminProducts:create');
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));

        /** @var RouteNotFoundException $caughtException */
        $caughtException = null;

        try {
            $converter->convertByParameters([
                'controller' => 'AdminProducts',
                'action' => 'configure',
            ]);
        } catch (RouteNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(RouteNotFoundException::class, $caughtException);
        $this->assertEquals('Could not find a route matching for legacy action: AdminProducts:configure', $caughtException->getMessage());
    }

    /**
     * This tests is used to test the component with a list of routes and mainly to
     * check possible conflicts when action is
     *
     * @throws ArgumentException
     * @throws RouteNotFoundException
     */
    public function testMultipleRoutesActionWithTrue()
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
                '_legacy_link' => 'AdminProducts:add',
            ],
        ]);
        $converter = new LegacyUrlConverter($router, new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class))));

        //Test index by parameter
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
        ]);
        $this->assertEquals('/products', $url);

        //Test index by url
        $url = $converter->convertByUrl('?controller=AdminProducts');
        $this->assertEquals('/products', $url);

        //Test create by parameter action
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'action' => 'add',
        ]);
        $this->assertEquals('/products/create', $url);

        //Test create by boolean value
        $url = $converter->convertByParameters([
            'controller' => 'AdminProducts',
            'add' => true,
        ]);
        $this->assertEquals('/products/create', $url);

        //Test url create by parameter action
        $url = $converter->convertByUrl('?controller=AdminProducts&action=add');
        $this->assertEquals('/products/create', $url);

        //Test url create by boolean value
        $url = $converter->convertByUrl('?controller=AdminProducts&add');
        $this->assertEquals('/products/create', $url);
    }

    /**
     * @param string $routeName
     * @param string $routePath
     * @param string|array $legacyLink
     * @param array|null $legacyParameters
     * @param array|null $expectedParameters
     *
     * @return MockObject|RouterInterface
     */
    private function buildRouterMock($routeName, $routePath, $legacyLink, array $legacyParameters = null, array $expectedParameters = null)
    {
        $routeCollection = new RouteCollection();

        $routeDefaults = [
            '_legacy_link' => $legacyLink,
        ];
        if (null !== $legacyParameters) {
            $routeDefaults['_legacy_parameters'] = $legacyParameters;
        }
        $routeCollection->add($routeName, new Route(
            $routePath,
            $routeDefaults
        ));

        $mockRouter = $this
            ->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRouter
            ->method('getRouteCollection')
            ->willReturn($routeCollection);

        if (null !== $expectedParameters) {
            $mockRouter
                ->method('generate')
                ->with(
                    $this->equalTo($routeName),
                    $this->equalTo($expectedParameters)
                )
                ->willReturn($routePath);
        } else {
            $mockRouter
                ->method('generate')
                ->with(
                    $this->equalTo($routeName),
                    $this->anything()
                )
                ->willReturn($routePath);
        }

        return $mockRouter;
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
            ->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockRouter
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
