<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShopBundle\EventListener\Admin\LegacyUrlListener;
use PrestaShopBundle\Routing\Converter\LegacyRouteFactory;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use PrestaShopBundle\Routing\Converter\RouterProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

class LegacyUrlListenerTest extends TestCase
{
    /**
     * A controller that has not been migrated renders the legacy page, and that page builds its
     * links relative to index.php. Served from index.php/ the browser reads the trailing slash as a
     * directory and resolves them one level deeper, so each one gains a second index.php.
     */
    public function testItCanonicalisesALegacyUrlCarryingATrailingSlash(): void
    {
        $event = $this->createEvent('/admin-dev/index.php/?controller=AdminCartRules');

        $this->createListener()->onKernelRequest($event);

        $response = $event->getResponse();
        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertSame('/admin-dev/index.php?controller=AdminCartRules', $response->getTargetUrl());
        self::assertSame(308, $response->getStatusCode());
    }

    /**
     * The canonical URL has to be left alone. Request::getPathInfo() answers "/" for it exactly as
     * it does for the trailing-slash form, so a check written against it redirects this URL to
     * itself and the browser stops after following the loop to its limit.
     */
    public function testItLeavesTheCanonicalLegacyUrlAlone(): void
    {
        $event = $this->createEvent('/admin-dev/index.php?controller=AdminCartRules');

        $this->createListener()->onKernelRequest($event);

        self::assertNull($event->getResponse());
    }

    /**
     * Without a legacy controller this is the back office homepage, a route in its own right.
     */
    public function testItLeavesTheHomepageAlone(): void
    {
        $event = $this->createEvent('/admin-dev/index.php/');

        $this->createListener()->onKernelRequest($event);

        self::assertNull($event->getResponse());
    }

    /**
     * Reached without the front controller in the path, the trailing slash belongs to the directory
     * and not to index.php. Stripping it makes the web server send the request straight back here,
     * which the browser reports as ERR_TOO_MANY_REDIRECTS. This is the form the back office is
     * actually reached in, so every page load loops, starting with the login.
     */
    public function testItLeavesADirectoryTrailingSlashAlone(): void
    {
        $event = $this->createEvent('/admin-dev/?controller=AdminCartRules');

        self::assertSame('/admin-dev', $event->getRequest()->getBaseUrl());

        $this->createListener()->onKernelRequest($event);

        self::assertNull($event->getResponse());
    }

    /**
     * A converter holding no route for the requested controller is what "not migrated" looks like,
     * and it is the only case where the legacy page - and its relative links - is really rendered.
     */
    private function createListener(): LegacyUrlListener
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('admin_products_index', new Route('/products', ['_legacy_link' => 'AdminProducts']));

        $router = $this->getMockBuilder(Router::class)->disableOriginalConstructor()->getMock();
        $router->method('getRouteCollection')->willReturn($routeCollection);
        $router->method('getContext')->willReturn(new RequestContext());

        $converter = new LegacyUrlConverter(
            $router,
            new RouterProvider($router, new LegacyRouteFactory($this->createMock(FeatureFlagManager::class)))
        );

        return new LegacyUrlListener($converter);
    }

    private function createEvent(string $requestUri): RequestEvent
    {
        $request = Request::create($requestUri, 'GET', [], [], [], [
            'SCRIPT_NAME' => '/admin-dev/index.php',
            'SCRIPT_FILENAME' => '/admin-dev/index.php',
            'PHP_SELF' => $requestUri,
        ]);

        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }
}
