<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class ListAvailableShipmentsForProduct
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var ProductId
     */
    private $productId;

    public function __construct(int $orderId, int $productId)
    {
        $this->orderId = new OrderId($orderId);
        $this->productId = new ProductId($productId);
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }
}
