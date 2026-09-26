<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderShopId;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler\GetOrderShopIdHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsQueryHandler]
final class GetOrderShopIdHandler implements GetOrderShopIdHandlerInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
    ) {
    }

    public function handle(GetOrderShopId $query): ShopId
    {
        $order = $this->orderRepository->get($query->getOrderId());

        return new ShopId((int) $order->id_shop);
    }
}
