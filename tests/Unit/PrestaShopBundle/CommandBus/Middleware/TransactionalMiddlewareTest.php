<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\CommandBus\Middleware;

use Doctrine\ORM\EntityManager;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\CommandBus\Middleware\TransactionalMiddleware;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class TransactionalMiddlewareTest extends TestCase
{
    /** @var HandlersLocatorInterface&MockObject */
    private $handlersLocator;

    /** @var EntityManager&MockObject */
    private $entityManager;

    /** @var TransactionalMiddleware */
    private $middleware;

    protected function setUp(): void
    {
        $this->handlersLocator = $this->createMock(HandlersLocatorInterface::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->middleware = new TransactionalMiddleware($this->handlersLocator, $this->entityManager);
    }

    public function testNonTransactionalHandlerExecutesWithoutTransaction(): void
    {
        $envelope = new Envelope(new stdClass());
        $stack = $this->createMock(StackInterface::class);
        $nextMiddleware = $this->createMock(MiddlewareInterface::class);

        $handlerDescriptor = new HandlerDescriptor(
            fn () => null,
            ['transactional' => false]
        );

        $this->handlersLocator
            ->expects($this->once())
            ->method('getHandlers')
            ->with($envelope)
            ->willReturn([$handlerDescriptor]);

        $stack
            ->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $this->entityManager
            ->expects($this->never())
            ->method('wrapInTransaction');

        $result = $this->middleware->handle($envelope, $stack);

        $this->assertSame($envelope, $result);
    }

    public function testTransactionalHandlerWrapsExecutionInTransaction(): void
    {
        $envelope = new Envelope(new stdClass());
        $stack = $this->createMock(StackInterface::class);
        $nextMiddleware = $this->createMock(MiddlewareInterface::class);

        $handlerDescriptor = new HandlerDescriptor(
            fn () => null,
            ['transactional' => true]
        );

        $this->handlersLocator
            ->expects($this->once())
            ->method('getHandlers')
            ->with($envelope)
            ->willReturn([$handlerDescriptor]);

        $this->entityManager
            ->expects($this->once())
            ->method('wrapInTransaction')
            ->willReturnCallback(static function (callable $func) {
                return $func();
            });

        $stack
            ->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willReturn($envelope);

        $result = $this->middleware->handle($envelope, $stack);

        $this->assertSame($envelope, $result);
    }

    public function testRollbackOccursWhenExceptionIsThrownInsideTransactionalHandler(): void
    {
        $envelope = new Envelope(new stdClass());
        $stack = $this->createMock(StackInterface::class);
        $nextMiddleware = $this->createMock(MiddlewareInterface::class);

        $handlerDescriptor = new HandlerDescriptor(
            fn () => null,
            ['transactional' => true]
        );

        $this->handlersLocator
            ->expects($this->once())
            ->method('getHandlers')
            ->with($envelope)
            ->willReturn([$handlerDescriptor]);

        $this->entityManager
            ->expects($this->once())
            ->method('wrapInTransaction')
            ->willReturnCallback(static function (callable $func) {
                return $func();
            });

        $stack
            ->expects($this->once())
            ->method('next')
            ->willReturn($nextMiddleware);

        $nextMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($envelope, $stack)
            ->willThrowException(new Exception('Database error'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database error');

        $this->middleware->handle($envelope, $stack);
    }
}
