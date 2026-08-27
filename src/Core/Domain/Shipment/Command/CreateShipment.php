<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class CreateShipment
{
    /** @var OrderId */
    private $orderId;

    /** @var CarrierId */
    private $carrierId;

    /** @var ProductId */
    private $productId;

    /** @var ?CombinationId */
    private $productCombinationId;

    private int $quantity;

    public function __construct(int $orderId, int $carrierId, int $productId, int $quantity, ?int $combinationId = 0)
    {
        $this->orderId = new OrderId($orderId);
        $this->carrierId = new CarrierId($carrierId);
        $this->productId = new ProductId($productId);
        $this->quantity = $quantity;
        if ($combinationId > 0) {
            $this->productCombinationId = new CombinationId($quantity);
        }
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getProductCombinationId(): ?CombinationId
    {
        return $this->productCombinationId;
    }
}
