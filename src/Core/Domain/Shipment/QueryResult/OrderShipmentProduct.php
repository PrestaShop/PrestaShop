<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult;

class OrderShipmentProduct
{
    public function __construct(
        private int $orderDetailId,
        private int $quantity,
        private string $productName,
        private string $productReference,
        private string $productImagePath,
    ) {
    }

    /**
     * @return int
     */
    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getProductReference(): string
    {
        return $this->productReference;
    }

    /**
     * @return string
     */
    public function getProductImagePath(): string
    {
        return $this->productImagePath;
    }

    public function toArray(): array
    {
        return [
            'order_detail_id' => $this->getOrderDetailId(),
            'quantity' => $this->getQuantity(),
            'product_name' => $this->getProductName(),
            'product_reference' => $this->getProductReference(),
            'product_image_path' => $this->getProductImagePath(),
        ];
    }
}
