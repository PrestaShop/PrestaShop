<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class AddProductToShipment
{
    /** @var ShipmentId */
    private $shipmentId;

    /** @var ProductId */
    private $productId;

    /** @var OrderId */
    private $orderId;

    /** @var ?CombinationId */
    private $combinationId;

    public function __construct(int $shipmentId, int $productId, int $orderId, int $combinationId = 0)
    {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->productId = new ProductId($productId);
        $this->orderId = new OrderId($orderId);
        if ($combinationId > 0) {
            $this->combinationId = new CombinationId($combinationId);
        }
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }
}
