<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\EventListener\Admin\TwoFactorInProgressListener;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

class TwoFactorInProgressListenerTest extends TestCase
{
    #[DataProvider('requestStates')]
    public function testPublicRouteSafetyNetAndTargetPath(
        string $route,
        bool $inProgress,
        bool $mainRequest,
        string $method,
        bool $ajax,
        bool $redirect,
        bool $saveTarget
    ): void {
        if (!interface_exists(TwoFactorTokenInterface::class)) {
            self::markTestSkipped('The locked Scheb dependencies are required to test an in-progress token.');
        }
        $security = $this->createMock(Security::class);
        $security->method('getToken')->willReturn($inProgress ? $this->createMock(TwoFactorTokenInterface::class) : null);
        $router = $this->createMock(RouterInterface::class);
        $router->expects($redirect ? self::once() : self::never())->method('generate')->with('2fa_login')->willReturn('/2fa');
        $request = Request::create('/requested', $method);
        $request->attributes->set('_route', $route);
        if ($ajax) {
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        }
        $request->setSession(new Session(new MockArraySessionStorage()));
        $event = new RequestEvent($this->createMock(HttpKernelInterface::class), $request, $mainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST);

        (new TwoFactorInProgressListener($security, $router))->onKernelRequest($event);

        self::assertSame($redirect, $event->hasResponse());
        self::assertSame($saveTarget ? $request->getUri() : null, $request->getSession()->get('_security.main.target_path'));
        if ($redirect) {
            self::assertSame('/2fa', $event->getResponse()->headers->get('Location'));
        }
    }

    public static function requestStates(): iterable
    {
        foreach (['2fa_login', '2fa_login_check', '2fa_resend', 'admin_logout'] as $route) {
            yield $route => [$route, true, true, 'GET', false, false, false];
        }
        yield 'public homepage is protected during 2FA' => ['admin_homepage', true, true, 'GET', false, true, true];
        yield 'protected route' => ['admin_products_index', true, true, 'GET', false, true, true];
        yield 'POST target is not stored' => ['admin_homepage', true, true, 'POST', false, true, false];
        yield 'AJAX target is not stored' => ['admin_homepage', true, true, 'GET', true, true, false];
        yield 'subrequest' => ['admin_homepage', true, false, 'GET', false, false, false];
        yield 'not in progress' => ['admin_homepage', false, true, 'GET', false, false, false];
    }
}
