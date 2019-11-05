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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;

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
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

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
        ?Customization $customization
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
}
