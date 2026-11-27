<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use Doctrine\Common\Annotations\Reader;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\EventListener\Admin\AccessDeniedListener;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class AccessDeniedListenerTest extends TestCase
{
    /**
     * @dataProvider getUnsupportedControllers
     */
    public function testUnsupportedControllerPreservesAccessDenied(?string $controller, Throwable $exception): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects(self::never())->method('generate');
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::never())->method('trans');
        $reader = $this->createMock(Reader::class);
        $reader->expects(self::never())->method('getMethodAnnotation');
        $listener = new AccessDeniedListener($router, $translator, new RequestStack(), $reader);
        $event = $this->createExceptionEvent($controller, $exception);

        $listener->onKernelException($event);

        self::assertFalse($event->hasResponse());
        self::assertSame($exception, $event->getThrowable());
        self::assertFalse($event->isPropagationStopped());
    }

    public function getUnsupportedControllers(): iterable
    {
        yield 'null controller, security exception' => [null, new AccessDeniedException()];
        yield 'service controller, security exception' => ['controller.service', new AccessDeniedException()];
        yield 'null controller, HTTP exception' => [null, new AccessDeniedHttpException()];
        yield 'service controller, HTTP exception' => ['controller.service', new AccessDeniedHttpException()];
    }

    public function testValidControllerPreservesAttributeResponse(): void
    {
        $controller = new class() {
            #[AdminSecurity('ROLE_EMPLOYEE', jsonResponse: true)]
            public function index(): void
            {
            }
        };
        $router = $this->createMock(RouterInterface::class);
        $router->expects(self::never())->method('generate');
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::once())->method('trans')
            ->with('Access Denied.', [], 'Admin.Notifications.Error')
            ->willReturn('Access Denied.');
        $reader = $this->createMock(Reader::class);
        $reader->expects(self::never())->method('getMethodAnnotation');
        $listener = new AccessDeniedListener($router, $translator, new RequestStack(), $reader);
        $event = $this->createExceptionEvent(get_class($controller) . '::index', new AccessDeniedException());

        $listener->onKernelException($event);

        self::assertTrue($event->hasResponse());
        $response = $event->getResponse();
        self::assertNotNull($response);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        self::assertSame(['status' => false, 'message' => 'Access Denied.'], json_decode($response->getContent(), true));
    }

    private function createExceptionEvent(?string $controller, Throwable $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request([], [], ['_controller' => $controller]),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }
}
