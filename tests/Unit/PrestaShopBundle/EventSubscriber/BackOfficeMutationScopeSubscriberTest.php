<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventSubscriber;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Mutation\BackOfficeMutationLogger;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\EventSubscriber\BackOfficeMutationScopeSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class BackOfficeMutationScopeSubscriberTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::CONTROLLER => 'onKernelController'],
            BackOfficeMutationScopeSubscriber::getSubscribedEvents()
        );
    }

    public function testMainAdminRequestIsMarked(): void
    {
        $request = new Request();
        $controller = new class() extends PrestaShopAdminController {
            public function testAction(): void
            {
            }
        };

        $subscriber = new BackOfficeMutationScopeSubscriber();
        $subscriber->onKernelController(
            $this->createControllerEvent(
                [$controller, 'testAction'],
                $request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        self::assertTrue(
            $request->attributes->get(BackOfficeMutationLogger::REQUEST_ATTRIBUTE)
        );
    }

    public function testMainNonAdminRequestIsNotMarked(): void
    {
        $request = new Request();
        $controller = new class() {
            public function testAction(): void
            {
            }
        };

        $subscriber = new BackOfficeMutationScopeSubscriber();
        $subscriber->onKernelController(
            $this->createControllerEvent(
                [$controller, 'testAction'],
                $request,
                HttpKernelInterface::MAIN_REQUEST
            )
        );

        self::assertFalse(
            $request->attributes->has(BackOfficeMutationLogger::REQUEST_ATTRIBUTE)
        );
    }

    public function testAdminSubRequestIsNotMarked(): void
    {
        $request = new Request();
        $controller = new class() extends PrestaShopAdminController {
            public function testAction(): void
            {
            }
        };

        $subscriber = new BackOfficeMutationScopeSubscriber();
        $subscriber->onKernelController(
            $this->createControllerEvent(
                [$controller, 'testAction'],
                $request,
                HttpKernelInterface::SUB_REQUEST
            )
        );

        self::assertFalse(
            $request->attributes->has(BackOfficeMutationLogger::REQUEST_ATTRIBUTE)
        );
    }

    public function testInvokableAdminControllerIsMarkedForMutationLogging(): void
    {
        $request = new Request();
        $controller = new class() extends PrestaShopAdminController {
            public function __invoke(): void
            {
            }
        };
        (new BackOfficeMutationScopeSubscriber())->onKernelController(
            $this->createControllerEvent($controller, $request, HttpKernelInterface::MAIN_REQUEST)
        );
        self::assertTrue($request->attributes->get(BackOfficeMutationLogger::REQUEST_ATTRIBUTE));
    }

    private function createControllerEvent(
        callable $controller,
        Request $request,
        int $requestType
    ): ControllerEvent {
        return new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            $controller,
            $request,
            $requestType
        );
    }
}
