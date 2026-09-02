<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use JsonSerializable;

class OrderProductForViewing implements JsonSerializable
{
    public const TYPE_PACK = 'pack';
    public const TYPE_PRODUCT_WITH_COMBINATIONS = 'product_with_combinations';
    public const TYPE_PRODUCT_WITHOUT_COMBINATIONS = 'product_without_combinations';

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $combinationId;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $name;

    /**
     * @var OrderProductForViewing[]
     */
    private $packItems;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $supplierReference;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $unitPrice;

    /**
     * @var string
     */
    private $totalPrice;

    /**
     * @var int
     */
    private $availableQuantity;

    /**
     * @var string|null
     */
    private $imagePath;

    /**
     * @var string
     */
    private $unitPriceTaxExclRaw;

    /**
     * @var string
     */
    private $unitPriceTaxInclRaw;

    /**
     * @var string
     */
    private $taxRate;

    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var string
     */
    private $amountRefunded;

    /**
     * @var int
     */
    private $quantityRefunded;

    /**
     * @var string
     */
    private $amountRefundable;

    /**
     * @var string
     */
    private $amountRefundableRaw;

    /**
     * @var int
     */
    private $orderInvoiceId;

    /**
     * @var string
     */
    private $orderInvoiceNumber;

    /**
     * @var bool
     */
    private $availableOutOfStock;

    /**
     * @var OrderProductCustomizationsForViewing
     */
    private $customizations;

    /**
     * @var string
     */
    private $mpn;

    /**
     * @var int[]
     */
    private $shipmentIds;

    /**
     * @param int $orderDetailId
     * @param int $id
     * @param int $combinationId
     * @param string $name
     * @param string $reference
     * @param string $supplierReference
     * @param int $quantity
     * @param string $unitPrice
     * @param string $totalPrice
     * @param int $availableQuantity
     * @param string|null $imagePath
     * @param string $unitPriceTaxExclRaw
     * @param string $unitPriceTaxInclRaw
     * @param string $taxRate
     * @param string $amountRefunded
     * @param int $quantityRefunded
     * @param string $amountRefundable
     * @param string $amountRefundableRaw
     * @param string $location
     * @param int|null $orderInvoiceId
     * @param string $orderInvoiceNumber
     * @param string $type
     * @param bool $availableOutOfStock
     * @param array $packItems
     * @param OrderProductCustomizationsForViewing|null $customizations
     * @param string $mpn
     * @param int[] $shipmentIds
     */
    public function __construct(
        ?int $orderDetailId,
        int $id,
        int $combinationId,
        string $name,
        string $reference,
        string $supplierReference,
        int $quantity,
        string $unitPrice,
        string $totalPrice,
        int $availableQuantity,
        ?string $imagePath,
        string $unitPriceTaxExclRaw,
        string $unitPriceTaxInclRaw,
        string $taxRate,
        string $amountRefunded,
        int $quantityRefunded,
        string $amountRefundable,
        string $amountRefundableRaw,
        string $location,
        ?int $orderInvoiceId,
        string $orderInvoiceNumber,
        string $type,
        bool $availableOutOfStock,
        array $packItems = [],
        ?OrderProductCustomizationsForViewing $customizations = null,
        string $mpn = '',
        array $shipmentIds = []
    ) {
        $this->id = $id;
        $this->combinationId = $combinationId;
        $this->name = $name;
        $this->reference = $reference;
        $this->supplierReference = $supplierReference;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->totalPrice = $totalPrice;
        $this->availableQuantity = $availableQuantity;
        $this->imagePath = $imagePath;
        $this->unitPriceTaxExclRaw = $unitPriceTaxExclRaw;
        $this->unitPriceTaxInclRaw = $unitPriceTaxInclRaw;
        $this->taxRate = $taxRate;
        $this->orderDetailId = $orderDetailId;
        $this->amountRefunded = $amountRefunded;
        $this->quantityRefunded = $quantityRefunded;
        $this->amountRefundable = $amountRefundable;
        $this->amountRefundableRaw = $amountRefundableRaw;
        $this->location = $location;
        $this->orderInvoiceId = $orderInvoiceId;
        $this->orderInvoiceNumber = $orderInvoiceNumber;
        $this->type = $type;
        $this->availableOutOfStock = $availableOutOfStock;
        $this->packItems = $packItems;
        $this->customizations = $customizations;
        $this->mpn = $mpn;
        $this->shipmentIds = $shipmentIds;
    }

    /**
     * Get product's order detail ID
     *
     * @return int|null
     */
    public function getOrderDetailId(): ?int
    {
        return $this->orderDetailId;
    }

    /**
     * Get product ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int[]
     */
    public function getShipmentIds(): array
    {
        return $this->shipmentIds;
    }

    /**
     * @return int
     */
    public function getCombinationId(): int
    {
        return $this->combinationId;
    }

    /**
     * Get product's name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OrderProductForViewing[]
     */
    public function getPackItems(): array
    {
        return $this->packItems;
    }

    /**
     * Product reference
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * Get product's supplier reference
     *
     * @return string
     */
    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    /**
     * get tax rate to be applied on this product
     *
     * @return string
     */
    public function getTaxRate(): string
    {
        return $this->taxRate;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get product's location
     *
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Get product's quantity
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Get product's unit price
     *
     * @return string
     */
    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    /**
     * Get product's formatted total price
     *
     * @return string
     */
    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    /**
     * Get available quantity for this product
     *
     * @return int
     */
    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }

    /**
     * Get image path for this product
     *
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * Get unit price without taxes
     *
     * @return string
     */
    public function getUnitPriceTaxExclRaw(): string
    {
        return $this->unitPriceTaxExclRaw;
    }

    /**
     * Get unit price including taxes
     *
     * @return string
     */
    public function getUnitPriceTaxInclRaw(): string
    {
        return $this->unitPriceTaxInclRaw;
    }

    /**
     * How much (money) has already been refunded for this product
     *
     * @return string
     */
    public function getAmountRefunded(): string
    {
        return $this->amountRefunded;
    }

    /**
     * How many (quantity) of this product has already been refunded
     *
     * @return int
     */
    public function getQuantityRefunded(): int
    {
        return $this->quantityRefunded;
    }

    /**
     * How much (money) can be refunded for this product (formatted for display)
     *
     * @return string
     */
    public function getAmountRefundable(): string
    {
        return $this->amountRefundable;
    }

    /**
     * How much (money) can be refunded for this product
     *
     * @return string
     */
    public function getAmountRefundableRaw(): string
    {
        return $this->amountRefundableRaw;
    }

    /**
     * How many (quantity) of this product can be refunded
     *
     * @return int
     */
    public function getQuantityRefundable(): int
    {
        return $this->quantity - $this->quantityRefunded;
    }

    /**
     * Can this product be refunded
     *
     * @return bool
     */
    public function isRefundable(): bool
    {
        if ($this->quantity <= $this->quantityRefunded) {
            return false;
        }

        return true;
    }

    /**
     * Get the id of this product's invoice
     *
     * @return int
     */
    public function getOrderInvoiceId(): ?int
    {
        return $this->orderInvoiceId;
    }

    /**
     * Get the number (reference) of this product's invoice
     *
     * @return string
     */
    public function getOrderInvoiceNumber(): string
    {
        return $this->orderInvoiceNumber;
    }

    /**
     * Get customizations of this product
     *
     * @return OrderProductCustomizationsForViewing|null
     */
    public function getCustomizations(): ?OrderProductCustomizationsForViewing
    {
        return $this->customizations;
    }

    /**
     * @return bool
     */
    public function isAvailableOutOfStock(): bool
    {
        return $this->availableOutOfStock;
    }

    /**
     * Get product MPN
     *
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'orderDetailId' => $this->getOrderDetailId(),
            'name' => $this->getName(),
            'reference' => $this->getReference(),
            'supplierReference' => $this->getSupplierReference(),
            'mpn' => $this->getMpn(),
            'location' => $this->getLocation(),
            'imagePath' => $this->getImagePath(),
            'quantity' => $this->getQuantity(),
            'availableQuantity' => $this->getAvailableQuantity(),
            'unitPrice' => $this->getUnitPrice(),
            'unitPriceTaxExclRaw' => $this->getUnitPriceTaxExclRaw(),
            'unitPriceTaxInclRaw' => $this->getUnitPriceTaxInclRaw(),
            'totalPrice' => $this->getTotalPrice(),
            'taxRate' => $this->getTaxRate(),
            'type' => $this->getType(),
            'packItems' => $this->getPackItems(),
        ];
    }
}
