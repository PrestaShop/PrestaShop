<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
