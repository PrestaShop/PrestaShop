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
use PrestaShopBundle\Routing\Converter\LegacyRoute;

class LegacyRouteTest extends TestCase
{
    public function testConstructor()
    {
        $legacyRoute = new LegacyRoute('product_index', ['AdminProduct'], []);
        static::assertEquals('product_index', $legacyRoute->getRouteName());
        static::assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertNull($legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(1, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['index']);
        static::assertEquals('product_index', $controllerActions['index']);
    }

    public function testConstructorAction()
    {
        $legacyRoute = new LegacyRoute('product_create', ['AdminProduct:create'], []);
        static::assertEquals('product_create', $legacyRoute->getRouteName());
        static::assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(1, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['create']);
        static::assertEquals('product_create', $controllerActions['create']);
    }

    public function testConstructorParameters()
    {
        $legacyRoute = new LegacyRoute('product_create', ['AdminProduct:create'], ['id_product' => 'productId']);
        static::assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        static::assertNotEmpty($routeParameters);
        static::assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(1, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['create']);
        static::assertEquals('product_create', $controllerActions['create']);
    }

    public function testConstructorAliases()
    {
        $legacyRoute = new LegacyRoute('product_create', ['AdminProduct:create', 'AdminProduct:new', 'SFProduct:new'], ['id_product' => 'productId']);
        static::assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        static::assertNotEmpty($routeParameters);
        static::assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(3, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('create', $legacyLink['action']);
        $legacyLink = $legacyLinks[1];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('new', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(2, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);
        static::assertNotEmpty($controllersActions['SFProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(2, $controllerActions);
        static::assertNotEmpty($controllerActions['create']);
        static::assertEquals('product_create', $controllerActions['create']);
        static::assertNotEmpty($controllerActions['new']);
        static::assertEquals('product_create', $controllerActions['new']);

        $controllerActions = $controllersActions['SFProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['new']);
        static::assertEquals('product_create', $controllerActions['new']);
    }

    public function testStaticConstructor()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_index', [
            '_legacy_link' => 'AdminProduct',
        ]);
        static::assertEquals('product_index', $legacyRoute->getRouteName());
        static::assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertNull($legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(1, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['index']);
        static::assertEquals('product_index', $controllerActions['index']);
    }

    public function testStaticConstructorAction()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_create', [
            '_legacy_link' => 'AdminProduct:create',
        ]);
        static::assertEquals('product_create', $legacyRoute->getRouteName());
        static::assertEmpty($legacyRoute->getRouteParameters());

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(1, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['create']);
        static::assertEquals('product_create', $controllerActions['create']);
    }

    public function testStaticParameters()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_create', [
            '_legacy_link' => 'AdminProduct:create',
            '_legacy_parameters' => [
                'id_product' => 'productId',
            ],
        ]);
        static::assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        static::assertNotEmpty($routeParameters);
        static::assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(1, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('create', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(1, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['create']);
        static::assertEquals('product_create', $controllerActions['create']);
    }

    public function testStaticAliases()
    {
        $legacyRoute = LegacyRoute::buildLegacyRoute('product_create', [
            '_legacy_link' => ['AdminProduct:create', 'AdminProduct:new', 'SFProduct:new'],
            '_legacy_parameters' => [
                'id_product' => 'productId',
            ],
        ]);
        static::assertEquals('product_create', $legacyRoute->getRouteName());
        $routeParameters = $legacyRoute->getRouteParameters();
        static::assertNotEmpty($routeParameters);
        static::assertEquals('productId', $routeParameters['id_product']);

        $legacyLinks = $legacyRoute->getLegacyLinks();
        static::assertCount(3, $legacyLinks);
        $legacyLink = $legacyLinks[0];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('create', $legacyLink['action']);
        $legacyLink = $legacyLinks[1];
        static::assertEquals('AdminProduct', $legacyLink['controller']);
        static::assertEquals('new', $legacyLink['action']);

        $controllersActions = $legacyRoute->getControllersActions();
        static::assertCount(2, $controllersActions);
        static::assertNotEmpty($controllersActions['AdminProduct']);
        static::assertNotEmpty($controllersActions['SFProduct']);

        $controllerActions = $controllersActions['AdminProduct'];
        static::assertCount(2, $controllerActions);
        static::assertNotEmpty($controllerActions['create']);
        static::assertEquals('product_create', $controllerActions['create']);
        static::assertNotEmpty($controllerActions['new']);
        static::assertEquals('product_create', $controllerActions['new']);

        $controllerActions = $controllersActions['SFProduct'];
        static::assertCount(1, $controllerActions);
        static::assertNotEmpty($controllerActions['new']);
        static::assertEquals('product_create', $controllerActions['new']);
    }
}
