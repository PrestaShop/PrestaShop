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
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Adds product to an existing order.
 */
class AddProductToOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var DecimalNumber
     */
    private $productPriceTaxIncluded;

    /**
     * @var DecimalNumber
     */
    private $productPriceTaxExcluded;

    /**
     * @var int
     */
    private $productQuantity;

    /**
     * @var int|null invoice id or null if new invoice should be created
     */
    private $orderInvoiceId;

    /**
     * @var bool|null bool if product is being added using new invoice
     */
    private $hasFreeShipping;

    private ?int $shipmentId = null;

    private ?int $carrierId = null;

    private ?bool $isVirtual = null;

    /**
     * Add product to an order with new invoice. It applies to orders that were already paid and waiting for payment.
     *
     * @param int $orderId
     * @param int $productId
     * @param int $combinationId
     * @param string $productPriceTaxIncluded
     * @param string $productPriceTaxExcluded
     * @param int $productQuantity
     * @param bool|null $hasFreeShipping
     * @param int|null $shipmentId
     * @param int|null $carrierId
     * @param bool|null $isVirtual
     *
     * @return self
     *
     * @throws InvalidProductQuantityException
     * @throws InvalidAmountException
     * @throws OrderException
     */
    public static function withNewInvoice(
        int $orderId,
        int $productId,
        int $combinationId,
        string $productPriceTaxIncluded,
        string $productPriceTaxExcluded,
        int $productQuantity,
        ?bool $hasFreeShipping = null,
        ?int $shipmentId = null,
        ?int $carrierId = null,
        ?bool $isVirtual = null
    ) {
        $command = new self(
            $orderId,
            $productId,
            $combinationId,
            $productPriceTaxIncluded,
            $productPriceTaxExcluded,
            $productQuantity
        );

        $command->hasFreeShipping = $hasFreeShipping;
        $command->shipmentId = $shipmentId;
        $command->carrierId = $carrierId;
        $command->isVirtual = $isVirtual;

        return $command;
    }

    /**
     * Add product to an order using existing invoice. It applies only for orders that were not yet paid.
     *
     * @param int $orderId
     * @param int $orderInvoiceId
     * @param int $productId
     * @param int $combinationId
     * @param string $productPriceTaxIncluded
     * @param string $productPriceTaxExcluded
     * @param int $productQuantity
     * @param int|null $shipmentId
     * @param int|null $carrierId
     * @param bool|null $isVirtual
     *
     * @return self
     *
     * @throws InvalidProductQuantityException
     * @throws InvalidAmountException
     * @throws OrderException
     */
    public static function toExistingInvoice(
        int $orderId,
        int $orderInvoiceId,
        int $productId,
        int $combinationId,
        string $productPriceTaxIncluded,
        string $productPriceTaxExcluded,
        int $productQuantity,
        ?int $shipmentId = null,
        ?int $carrierId = null,
        ?bool $isVirtual = null
    ) {
        $command = new self(
            $orderId,
            $productId,
            $combinationId,
            $productPriceTaxIncluded,
            $productPriceTaxExcluded,
            $productQuantity
        );

        $command->orderInvoiceId = $orderInvoiceId;
        $command->shipmentId = $shipmentId;
        $command->carrierId = $carrierId;
        $command->isVirtual = $isVirtual;

        return $command;
    }

    /**
     * @param int $orderId
     * @param int $productId
     * @param int $combinationId
     * @param string $productPriceTaxIncluded
     * @param string $productPriceTaxExcluded
     * @param int $productQuantity
     *
     * @throws InvalidProductQuantityException
     * @throws InvalidAmountException
     * @throws OrderException
     */
    private function __construct(
        int $orderId,
        int $productId,
        int $combinationId,
        string $productPriceTaxIncluded,
        string $productPriceTaxExcluded,
        int $productQuantity
    ) {
        $this->orderId = new OrderId($orderId);
        $this->productId = new ProductId($productId);
        $this->combinationId = !empty($combinationId) ? new CombinationId($combinationId) : null;
        try {
            $this->productPriceTaxIncluded = new DecimalNumber($productPriceTaxIncluded);
            $this->productPriceTaxExcluded = new DecimalNumber($productPriceTaxExcluded);
        } catch (InvalidArgumentException) {
            throw new InvalidAmountException();
        }
        $this->setProductQuantity($productQuantity);
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return DecimalNumber
     */
    public function getProductPriceTaxIncluded(): DecimalNumber
    {
        return $this->productPriceTaxIncluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getProductPriceTaxExcluded(): DecimalNumber
    {
        return $this->productPriceTaxExcluded;
    }

    /**
     * @return int
     */
    public function getProductQuantity(): int
    {
        return $this->productQuantity;
    }

    /**
     * @return int|null
     */
    public function getOrderInvoiceId(): ?int
    {
        return $this->orderInvoiceId;
    }

    /**
     * @return bool|null
     */
    public function hasFreeShipping(): ?bool
    {
        return $this->hasFreeShipping;
    }

    public function getShipmentId(): ?int
    {
        return $this->shipmentId;
    }

    public function getCarrierId(): ?int
    {
        return $this->carrierId;
    }

    public function isVirtual(): ?bool
    {
        return $this->isVirtual;
    }

    /**
     * @param int $productQuantity
     *
     * @throws InvalidProductQuantityException
     */
    private function setProductQuantity(int $productQuantity): void
    {
        if ($productQuantity <= 0) {
            throw new InvalidProductQuantityException('When adding a product quantity must be strictly positive');
        }
        $this->productQuantity = $productQuantity;
    }
}
