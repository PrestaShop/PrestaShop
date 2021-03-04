<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds data of cart product information
 */
class CartProduct
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $attributeId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $unitPrice;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $imageLink;

    /**
     * @var Customization|null
     */
    private $customization;

    /**
     * @var int
     */
    private $availableStock;

    /**
     * @var bool
     */
    private $availableOutOfStock;

    /**
     * @var bool
     */
    private $isGift;

    /**
     * CartProduct constructor.
     *
     * @param int $productId
     * @param int $attributeId
     * @param string $name
     * @param string $attribute
     * @param string $reference
     * @param string $unitPrice
     * @param int $quantity
     * @param string $price
     * @param string $imageLink
     * @param Customization|null $customization
     * @param int $availableStock
     * @param bool $availableOutOfStock
     * @param bool $isGift
     */
    public function __construct(
        int $productId,
        int $attributeId,
        string $name,
        string $attribute,
        string $reference,
        string $unitPrice,
        int $quantity,
        string $price,
        string $imageLink,
        ?Customization $customization,
        int $availableStock,
        bool $availableOutOfStock,
        bool $isGift = false
    ) {
        $this->productId = $productId;
        $this->attributeId = $attributeId;
        $this->name = $name;
        $this->attribute = $attribute;
        $this->reference = $reference;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->imageLink = $imageLink;
        $this->customization = $customization;
        $this->availableStock = $availableStock;
        $this->availableOutOfStock = $availableOutOfStock;
        $this->isGift = $isGift;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
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
    public function getUnitPrice(): string
    {
        return $this->unitPrice;
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
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getImageLink(): string
    {
        return $this->imageLink;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->attribute;
    }

    /**
     * @return Customization|null
     */
    public function getCustomization(): ?Customization
    {
        return $this->customization;
    }

    /**
     * @return int
     */
    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }

    /**
     * @return bool
     */
    public function isAvailableOutOfStock(): bool
    {
        return $this->availableOutOfStock;
    }

    /**
     * @return bool
     */
    public function isGift(): bool
    {
        return $this->isGift;
    }
}
