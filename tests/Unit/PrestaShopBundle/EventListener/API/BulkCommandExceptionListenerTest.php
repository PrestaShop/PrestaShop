<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\API;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureException;
use PrestaShopBundle\EventListener\API\BulkCommandExceptionListener;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

class BulkCommandExceptionListenerTest extends TestCase
{
    private BulkCommandExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new BulkCommandExceptionListener();
    }

    public function testItWrapsInHttpExceptionWithMultiStatus(): void
    {
        $bulkException = new BulkFeatureException(
            [new FeatureException('Feature #1 not found')],
            'Bulk error'
        );

        $event = $this->createExceptionEvent($bulkException);
        $this->listener->onKernelException($event);

        $wrappedException = $event->getThrowable();
        $this->assertInstanceOf(HttpException::class, $wrappedException);
        $this->assertSame(Response::HTTP_MULTI_STATUS, $wrappedException->getStatusCode());
        $this->assertSame('Bulk error', $wrappedException->getMessage());
        $this->assertSame($bulkException, $wrappedException->getPrevious());
    }

    public function testItIgnoresNonBulkExceptions(): void
    {
        $runtimeException = new RuntimeException('Some error');
        $event = $this->createExceptionEvent($runtimeException);
        $this->listener->onKernelException($event);

        $this->assertSame($runtimeException, $event->getThrowable());
    }

    private function createExceptionEvent(Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        return new ExceptionEvent(
            $kernel,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }
}
