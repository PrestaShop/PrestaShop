<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
