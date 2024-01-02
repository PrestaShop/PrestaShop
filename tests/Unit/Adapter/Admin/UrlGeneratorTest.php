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

namespace Tests\Unit\Adapter\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Admin\UrlGenerator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class UrlGeneratorTest extends TestCase
{
    public function testGenerateEquivalentRoute(): void
    {
        $generator = new UrlGenerator($this->getMockLegacyContext(), $this->getMockRouter());

        // the following route contains a "_legacy" equivalent
        list($controller, $parameters) = $generator->getLegacyOptions('admin_products_index');
        $this->assertEquals('AdminProducts', $controller);
        $this->assertCount(0, $parameters);
    }

    private function getMockLegacyContext(): LegacyContext
    {
        $mock = $this->createMock(LegacyContext::class);

        return $mock;
    }

    private function getMockRouter(): Router
    {
        $route = new Route('/');
        $route->setDefault('_legacy_controller', 'AdminProducts');

        $routeCollection = new RouteCollection();
        $routeCollection->add('admin_products_index', $route);

        $mock = $this->createMock(Router::class);
        $mock->method('getRouteCollection')->willReturn($routeCollection);

        return $mock;
    }
}
