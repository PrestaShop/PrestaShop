<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Routing\Converter;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\Converter\LegacyRoute;

class LegacyRouteTest extends TestCase
{
    public function testConstructor()
    {
        $legacyRoute = new LegacyRoute('product_index', ['AdminProduct'], []);
        $this->assertEquals('product_index', $legacyRoute->getRouteName());
        $this->assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertNull($legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(1, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['index']);
        $this->assertEquals('product_index', $controllerActions['index']);
    }

    public function testConstructorAction()
    {
        $legacyRoute = new LegacyRoute('product_create', ['AdminProduct:create'], []);
        $this->assertEquals('product_create', $legacyRoute->getRouteName());
        $this->assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(1, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['create']);
        $this->assertEquals('product_create', $controllerActions['create']);
    }

    public function testConstructorParameters()
    {
        $legacyRoute = new LegacyRoute('product_create', ['AdminProduct:create'], ['id_product' => 'productId']);
        $this->assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        $this->assertNotEmpty($routeParameters);
        $this->assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(1, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['create']);
        $this->assertEquals('product_create', $controllerActions['create']);
    }

    public function testConstructorAliases()
    {
        $legacyRoute = new LegacyRoute('product_create', ['AdminProduct:create', 'AdminProduct:new', 'SFProduct:new'], ['id_product' => 'productId']);
        $this->assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        $this->assertNotEmpty($routeParameters);
        $this->assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(3, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('create', $legacyLink['action']);
        $legacyLink = $legacyLinks[1];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('new', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(2, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);
        $this->assertNotEmpty($controllersActions['SFProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(2, $controllerActions);
        $this->assertNotEmpty($controllerActions['create']);
        $this->assertEquals('product_create', $controllerActions['create']);
        $this->assertNotEmpty($controllerActions['new']);
        $this->assertEquals('product_create', $controllerActions['new']);

        $controllerActions = $controllersActions['SFProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['new']);
        $this->assertEquals('product_create', $controllerActions['new']);
    }

    public function testStaticConstructor()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_index', [
            '_legacy_link' => 'AdminProduct',
        ]);
        $this->assertEquals('product_index', $legacyRoute->getRouteName());
        $this->assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertNull($legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(1, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['index']);
        $this->assertEquals('product_index', $controllerActions['index']);
    }

    public function testStaticConstructorAction()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_create', [
            '_legacy_link' => 'AdminProduct:create',
        ]);
        $this->assertEquals('product_create', $legacyRoute->getRouteName());
        $this->assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(1, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['create']);
        $this->assertEquals('product_create', $controllerActions['create']);
    }

    public function testStaticParameters()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_create', [
            '_legacy_link' => 'AdminProduct:create',
            '_legacy_parameters' => [
                'id_product' => 'productId',
            ],
        ]);
        $this->assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        $this->assertNotEmpty($routeParameters);
        $this->assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(1, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['create']);
        $this->assertEquals('product_create', $controllerActions['create']);
    }

    public function testStaticAliases()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_create', [
            '_legacy_link' => ['AdminProduct:create', 'AdminProduct:new', 'SFProduct:new'],
            '_legacy_parameters' => [
                'id_product' => 'productId',
            ],
        ]);
        $this->assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        $this->assertNotEmpty($routeParameters);
        $this->assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        $this->assertCount(3, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('create', $legacyLink['action']);
        $legacyLink = $legacyLinks[1];
        $this->assertEquals('AdminProduct', $legacyLink['controller']);
        $this->assertEquals('new', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        $this->assertCount(2, $controllersActions);
        $this->assertNotEmpty($controllersActions['AdminProduct']);
        $this->assertNotEmpty($controllersActions['SFProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        $this->assertCount(2, $controllerActions);
        $this->assertNotEmpty($controllerActions['create']);
        $this->assertEquals('product_create', $controllerActions['create']);
        $this->assertNotEmpty($controllerActions['new']);
        $this->assertEquals('product_create', $controllerActions['new']);

        $controllerActions = $controllersActions['SFProduct'];
        $this->assertCount(1, $controllerActions);
        $this->assertNotEmpty($controllerActions['new']);
        $this->assertEquals('product_create', $controllerActions['new']);
    }
}
