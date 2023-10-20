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
