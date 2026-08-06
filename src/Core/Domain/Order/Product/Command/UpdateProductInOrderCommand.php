<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\Command;

use InvalidArgumentException;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidProductQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Updates product in given order.
 */
class UpdateProductInOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var DecimalNumber
     */
    private $priceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $priceTaxExcluded;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int|null
     */
    private $orderInvoiceId;

    /**
     * @var null|array<int, array{
     *     shipment_id: int,
     *     quantity: int
     * }>
     */
    private ?array $shipmentsQuantities;

    /**
     * @param int $orderId
     * @param int $orderDetailId
     * @param string $priceTaxIncluded
     * @param string $priceTaxExcluded
     * @param int $quantity
     * @param int|null $orderInvoiceId
     * @param null|array<int, array{
     *     shipment_id: int,
     *     quantity: int
     * }> $shipmentsQuantities
     */
    public function __construct(
        int $orderId,
        int $orderDetailId,
        string $priceTaxIncluded,
        string $priceTaxExcluded,
        int $quantity,
        ?int $orderInvoiceId = null,
        ?array $shipmentsQuantities = null
    ) {
        $this->orderId = new OrderId($orderId);
        $this->orderDetailId = $orderDetailId;
        try {
            $this->priceTaxIncluded = new DecimalNumber($priceTaxIncluded);
            $this->priceTaxExcluded = new DecimalNumber($priceTaxExcluded);
        } catch (InvalidArgumentException) {
            throw new InvalidAmountException();
        }
        $this->setQuantity($quantity);
        $this->setShipmentsQuantities($shipmentsQuantities);
        $this->orderInvoiceId = $orderInvoiceId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getOrderDetailId()
    {
        return $this->orderDetailId;
    }

    /**
     * @return DecimalNumber
     */
    public function getPriceTaxIncluded()
    {
        return $this->priceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getPriceTaxExcluded()
    {
        return $this->priceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return int|null
     */
    public function getOrderInvoiceId()
    {
        return $this->orderInvoiceId;
    }

    /**
     * @param int $quantity
     *
     * @throws InvalidProductQuantityException
     */
    private function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidProductQuantityException('When adding a product quantity must be strictly positive');
        }
        $this->quantity = $quantity;
    }

    /**
     * @param null|array<int, array{
     *     shipment_id: int,
     *     quantity: int
     * }> $shipmentsQuantities
     *
     * @throws InvalidProductQuantityException
     */
    private function setShipmentsQuantities(?array $shipmentsQuantities): void
    {
        if (!empty($shipmentsQuantities)) {
            $hasPositiveQuantity = false;

            foreach ($shipmentsQuantities as $shipmentQuantity) {
                if ($shipmentQuantity['quantity'] < 0) {
                    throw new InvalidProductQuantityException('Shipment quantity must be positive');
                }

                if ($shipmentQuantity['quantity'] >= 1) {
                    $hasPositiveQuantity = true;
                }
            }

            if (!$hasPositiveQuantity) {
                throw new InvalidProductQuantityException(
                    'At least one shipment quantity must be greater than or equal to 1'
                );
            }
        }

        $this->shipmentsQuantities = $shipmentsQuantities;
    }

    /**
     * @return null|array<int, array{
     *     shipment_id: int,
     *     quantity: int
     * }>
     */
    public function getShipmentsQuantities()
    {
        return $this->shipmentsQuantities;
    }
}
