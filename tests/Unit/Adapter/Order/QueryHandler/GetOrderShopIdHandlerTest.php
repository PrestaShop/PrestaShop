<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Order\QueryHandler;

use Order;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\QueryHandler\GetOrderShopIdHandler;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderShopId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class GetOrderShopIdHandlerTest extends TestCase
{
    public function testItReturnsOrderShopId(): void
    {
        $orderId = new OrderId(42);
        $order = $this->createMock(Order::class);
        $order->id_shop = 2;
        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($order);

        $handler = new GetOrderShopIdHandler($orderRepository);

        $shopId = $handler->handle(new GetOrderShopId($orderId->getValue()));

        $this->assertSame(2, $shopId->getValue());
    }
}
