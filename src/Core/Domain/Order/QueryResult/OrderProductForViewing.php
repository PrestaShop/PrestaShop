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

class OrderProductForViewing
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
     * @var array<OrderProductForViewing>
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
        string $location,
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
        $this->location = $location;
        $this->packItems = $packItems;
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getOrderDetailId(): ?int
    {
        return $this->orderDetailId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<OrderProductForViewing>
     */
    public function getPackItems(): array
    {
        return $this->packItems;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    /**
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
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
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
    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    /**
     * @return string
     */
    public function getTotalPrice(): string
    {
        return $this->totalPrice;
    }

    /**
     * @return int
     */
    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }

    /**
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * @return float
     */
    public function getUnitPriceTaxExclRaw(): float
    {
        return $this->unitPriceTaxExclRaw;
    }

    /**
     * @return float
     */
    public function getUnitPriceTaxInclRaw(): float
    {
        return $this->unitPriceTaxInclRaw;
    }
}
