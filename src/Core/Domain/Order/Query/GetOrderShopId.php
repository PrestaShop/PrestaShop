<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class GetOrderShopId
{
    private readonly OrderId $orderId;

    public function __construct(
        int $orderId,
    ) {
        $this->orderId = new OrderId($orderId);
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }
}
