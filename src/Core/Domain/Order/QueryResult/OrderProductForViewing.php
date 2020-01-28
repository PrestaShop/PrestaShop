<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use JsonSerializable;

class OrderProductForViewing implements JsonSerializable
{
    const TYPE_PACK = 'pack';
    const TYPE_PRODUCT_WITH_COMBINATIONS = 'product_with_combinations';
    const TYPE_PRODUCT_WITHOUT_COMBINATIONS = 'product_without_combinations';

    /**
     * @var int
     */
    private $id;

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
     * @var float
     */
    private $unitPriceTaxExclRaw;

    /**
     * @var float
     */
    private $unitPriceTaxInclRaw;

    /**
     * @var float
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
     * @var int
     */
    private $orderInvoiceId;

    /**
     * @var string
     */
    private $orderInvoiceNumber;

    public function __construct(
        ?int $orderDetailId,
        int $id,
        string $name,
        string $reference,
        string $supplierReference,
        int $quantity,
        string $unitPrice,
        string $totalPrice,
        int $availableQuantity,
        ?string $imagePath,
        float $unitPriceTaxExclRaw,
        float $unitPriceTaxInclRaw,
        float $taxRate,
        string $amountRefunded,
        int $quantityRefunded,
        string $amountRefundable,
        string $location,
        ?int $orderInvoiceId,
        string $orderInvoiceNumber,
        string $type,
        array $packItems = []
    ) {
        $this->id = $id;
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
        $this->location = $location;
        $this->orderInvoiceId = $orderInvoiceId;
        $this->orderInvoiceNumber = $orderInvoiceNumber;
        $this->type = $type;
        $this->packItems = $packItems;
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
     * @return float
     */
    public function getTaxRate(): float
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
     * Get unit price without taxes, as a float value
     *
     * @return float
     */
    public function getUnitPriceTaxExclRaw(): float
    {
        return $this->unitPriceTaxExclRaw;
    }

    /**
     * Get unit price including taxes, as a float value
     *
     * @return float
     */
    public function getUnitPriceTaxInclRaw(): float
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
     * How much (money) can be refunded for this product
     *
     * @return string
     */
    public function getAmountRefundable(): string
    {
        return $this->amountRefundable;
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
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'orderDetailId' => $this->getOrderDetailId(),
            'name' => $this->getName(),
            'reference' => $this->getReference(),
            'supplierReference' => $this->getSupplierReference(),
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
