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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\LowStockThreshold;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Dimension;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectOption;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Contains all the data needed to handle the product update.
 *
 * @see UpdateProductHandlerInterface
 *
 * This command is only designed for the general data of product which can be persisted in one call.
 * It was not designed to handle the product relations.
 */
class UpdateProductCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var string[]|null
     */
    private $localizedNames;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedDescriptions;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedShortDescriptions;

    /**
     * @var ProductVisibility|null
     */
    private $visibility;

    /**
     * @var bool|null
     */
    private $availableForOrder;

    /**
     * @var bool|null
     */
    private $onlineOnly;

    /**
     * @var bool|null
     */
    private $showPrice;

    /**
     * @var ProductCondition|null
     */
    private $condition;

    /**
     * @var bool|null
     */
    private $showCondition;

    /**
     * @var ManufacturerIdInterface|null
     */
    private $manufacturerId;

    /**
     * @var DecimalNumber|null
     */
    private $price;

    /**
     * @var DecimalNumber|null
     */
    private $ecotax;

    /**
     * @var int|null
     */
    private $taxRulesGroupId;

    /**
     * @var bool|null
     */
    private $onSale;

    /**
     * @var DecimalNumber|null
     */
    private $wholesalePrice;

    /**
     * @var DecimalNumber|null
     */
    private $unitPrice;

    /**
     * @var string|null
     */
    private $unity;

    /**
     * @var string[]|null
     */
    private $localizedMetaTitles;

    /**
     * @var string[]|null
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]|null
     */
    private $localizedLinkRewrites;

    /**
     * @var RedirectOption|null
     */
    private $redirectOption;

    /**
     * @var Isbn|null
     */
    private $isbn;

    /**
     * @var Upc|null
     */
    private $upc;

    /**
     * @var Ean13|null
     */
    private $ean13;

    /**
     * @var string|null
     */
    private $mpn;

    /**
     * @var Reference|null
     */
    private $reference;

    /**
     * @var Dimension|null
     */
    private $width;

    /**
     * @var Dimension|null
     */
    private $height;

    /**
     * @var Dimension|null
     */
    private $depth;

    /**
     * @var Dimension|null
     */
    private $weight;

    /**
     * @var DecimalNumber|null
     */
    private $additionalShippingCost;

    /**
     * @var DeliveryTimeNoteType
     */
    private $deliveryTimeNoteType;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeInStockNotes;

    /**
     * @var string[]|null
     */
    private $localizedDeliveryTimeOutOfStockNotes;

    /**
     * @var PackStockType|null
     */
    private $packStockType;

    /**
     * @var int|null
     */
    private $minimalQuantity;

    /**
     * @var LowStockThreshold|null
     */
    private $lowStockThreshold;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @var bool|null
     */
    private $active;

    /**
     * @param int $productId
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(int $productId, ShopConstraint $shopConstraint)
    {
        $this->productId = new ProductId($productId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles key => value pairs where each key represents language id
     *
     * @return self
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): self
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions key => value pairs where each key represents language id
     *
     * @return self
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): self
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedLinkRewrites(): ?array
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites key => value pairs where each key represents language id
     *
     * @return self
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites): self
    {
        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return RedirectOption|null
     */
    public function getRedirectOption(): ?RedirectOption
    {
        return $this->redirectOption;
    }

    /**
     * @param string $redirectType
     * @param int $redirectTarget
     *
     * @return self
     */
    public function setRedirectOption(string $redirectType, int $redirectTarget): self
    {
        $this->redirectOption = new RedirectOption($redirectType, $redirectTarget);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getPrice(): ?DecimalNumber
    {
        return $this->price;
    }

    /**
     * @param string $price
     *
     * @return self
     */
    public function setPrice(string $price): self
    {
        $this->price = new DecimalNumber($price);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getEcotax(): ?DecimalNumber
    {
        return $this->ecotax;
    }

    /**
     * @param string $ecotax
     *
     * @return self
     */
    public function setEcotax(string $ecotax): self
    {
        $this->ecotax = new DecimalNumber($ecotax);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTaxRulesGroupId(): ?int
    {
        return $this->taxRulesGroupId;
    }

    /**
     * @param int $taxRulesGroupId
     *
     * @return self
     */
    public function setTaxRulesGroupId(int $taxRulesGroupId): self
    {
        $this->taxRulesGroupId = $taxRulesGroupId;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    /**
     * @param bool $onSale
     *
     * @return self
     */
    public function setOnSale(bool $onSale): self
    {
        $this->onSale = $onSale;

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getWholesalePrice(): ?DecimalNumber
    {
        return $this->wholesalePrice;
    }

    /**
     * @param string $wholesalePrice
     *
     * @return self
     */
    public function setWholesalePrice(string $wholesalePrice): self
    {
        $this->wholesalePrice = new DecimalNumber($wholesalePrice);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getUnitPrice(): ?DecimalNumber
    {
        return $this->unitPrice;
    }

    /**
     * @param string $unitPrice
     *
     * @return self
     */
    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = new DecimalNumber($unitPrice);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUnity(): ?string
    {
        return $this->unity;
    }

    /**
     * @param string $unity
     *
     * @return self
     */
    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return self
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     *
     * @return self
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): self
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedShortDescriptions(): ?array
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @param string[] $localizedShortDescriptions
     *
     * @return self
     */
    public function setLocalizedShortDescriptions(array $localizedShortDescriptions): self
    {
        $this->localizedShortDescriptions = $localizedShortDescriptions;

        return $this;
    }

    /**
     * @return ProductVisibility|null
     */
    public function getVisibility(): ?ProductVisibility
    {
        return $this->visibility;
    }

    /**
     * @return bool|null
     */
    public function isAvailableForOrder(): ?bool
    {
        return $this->availableForOrder;
    }

    /**
     * @param string $visibility
     *
     * @return self
     */
    public function setVisibility(string $visibility): self
    {
        $this->visibility = new ProductVisibility($visibility);

        return $this;
    }

    /**
     * @param bool $availableForOrder
     *
     * @return self
     */
    public function setAvailableForOrder(bool $availableForOrder): self
    {
        $this->availableForOrder = $availableForOrder;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isOnlineOnly(): ?bool
    {
        return $this->onlineOnly;
    }

    /**
     * @param bool $onlineOnly
     *
     * @return self
     */
    public function setOnlineOnly(bool $onlineOnly): self
    {
        $this->onlineOnly = $onlineOnly;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function showPrice(): ?bool
    {
        return $this->showPrice;
    }

    /**
     * @param bool $showPrice
     *
     * @return self
     */
    public function setShowPrice(bool $showPrice): self
    {
        $this->showPrice = $showPrice;

        return $this;
    }

    /**
     * @return ProductCondition|null
     */
    public function getCondition(): ?ProductCondition
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     *
     * @return self
     */
    public function setCondition(string $condition): self
    {
        $this->condition = new ProductCondition($condition);

        return $this;
    }

    /**
     * @param bool $showCondition
     *
     * @return self
     */
    public function setShowCondition(bool $showCondition): self
    {
        $this->showCondition = $showCondition;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function showCondition(): ?bool
    {
        return $this->showCondition;
    }

    /**
     * @return ManufacturerIdInterface|null
     */
    public function getManufacturerId(): ?ManufacturerIdInterface
    {
        return $this->manufacturerId;
    }

    /**
     * @param int $manufacturerId
     *
     * @throws ManufacturerConstraintException
     *
     * @return self
     */
    public function setManufacturerId(int $manufacturerId): self
    {
        $this->manufacturerId = NoManufacturerId::NO_MANUFACTURER_ID === $manufacturerId ?
            new NoManufacturerId() :
            new ManufacturerId($manufacturerId)
        ;

        return $this;
    }

    /**
     * @return Isbn|null
     */
    public function getIsbn(): ?Isbn
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     *
     * @return self
     */
    public function setIsbn(string $isbn): self
    {
        $this->isbn = new Isbn($isbn);

        return $this;
    }

    /**
     * @return Upc|null
     */
    public function getUpc(): ?Upc
    {
        return $this->upc;
    }

    /**
     * @param string $upc
     *
     * @return self
     */
    public function setUpc(string $upc): self
    {
        $this->upc = new Upc($upc);

        return $this;
    }

    /**
     * @return Ean13|null
     */
    public function getEan13(): ?Ean13
    {
        return $this->ean13;
    }

    /**
     * @param string $ean13
     *
     * @return self
     */
    public function setEan13(string $ean13): self
    {
        $this->ean13 = new Ean13($ean13);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    /**
     * @param string $mpn
     *
     * @return self
     */
    public function setMpn(string $mpn): self
    {
        $this->mpn = $mpn;

        return $this;
    }

    /**
     * @return Reference|null
     */
    public function getReference(): ?Reference
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return self
     */
    public function setReference(string $reference): self
    {
        $this->reference = new Reference($reference);

        return $this;
    }

    /**
     * @return Dimension|null
     */
    public function getWidth(): ?Dimension
    {
        return $this->width;
    }

    /**
     * @param string $width
     *
     * @return self
     */
    public function setWidth(string $width): self
    {
        $this->setDimension($width, 'width');

        return $this;
    }

    /**
     * @return Dimension|null
     */
    public function getHeight(): ?Dimension
    {
        return $this->height;
    }

    /**
     * @param string $height
     *
     * @return self
     */
    public function setHeight(string $height): self
    {
        $this->setDimension($height, 'height');

        return $this;
    }

    /**
     * @return Dimension|null
     */
    public function getDepth(): ?Dimension
    {
        return $this->depth;
    }

    /**
     * @param string $depth
     *
     * @return self
     */
    public function setDepth(string $depth): self
    {
        $this->setDimension($depth, 'depth');

        return $this;
    }

    /**
     * @return Dimension|null
     */
    public function getWeight(): ?Dimension
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     *
     * @return self
     */
    public function setWeight(string $weight): self
    {
        $this->setDimension($weight, 'weight');

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getAdditionalShippingCost(): ?DecimalNumber
    {
        return $this->additionalShippingCost;
    }

    /**
     * @param string $additionalShippingCost
     *
     * @return self
     */
    public function setAdditionalShippingCost(string $additionalShippingCost): self
    {
        $this->additionalShippingCost = new DecimalNumber($additionalShippingCost);

        return $this;
    }

    /**
     * @return DeliveryTimeNoteType|null
     */
    public function getDeliveryTimeNoteType(): ?DeliveryTimeNoteType
    {
        return $this->deliveryTimeNoteType;
    }

    /**
     * @param int $type
     *
     * @return self
     */
    public function setDeliveryTimeNoteType(int $type): self
    {
        $this->deliveryTimeNoteType = new DeliveryTimeNoteType($type);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDeliveryTimeInStockNotes(): ?array
    {
        return $this->localizedDeliveryTimeInStockNotes;
    }

    /**
     * @param string[] $localizedDeliveryTimeInStockNotes
     *
     * @return self
     */
    public function setLocalizedDeliveryTimeInStockNotes(array $localizedDeliveryTimeInStockNotes): self
    {
        $this->localizedDeliveryTimeInStockNotes = $localizedDeliveryTimeInStockNotes;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDeliveryTimeOutOfStockNotes(): ?array
    {
        return $this->localizedDeliveryTimeOutOfStockNotes;
    }

    /**
     * @param string[] $localizedDeliveryTimeOutOfStockNotes
     *
     * @return self
     */
    public function setLocalizedDeliveryTimeOutOfStockNotes(array $localizedDeliveryTimeOutOfStockNotes): self
    {
        $this->localizedDeliveryTimeOutOfStockNotes = $localizedDeliveryTimeOutOfStockNotes;

        return $this;
    }

    /**
     * @return PackStockType|null
     */
    public function getPackStockType(): ?PackStockType
    {
        return $this->packStockType;
    }

    /**
     * @param int $packStockType
     *
     * @return self
     *
     * @throws ProductPackConstraintException
     */
    public function setPackStockType(int $packStockType): self
    {
        $this->packStockType = new PackStockType($packStockType);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinimalQuantity(): ?int
    {
        return $this->minimalQuantity;
    }

    /**
     * @param int $minimalQuantity
     *
     * @return self
     */
    public function setMinimalQuantity(int $minimalQuantity): self
    {
        $this->minimalQuantity = $minimalQuantity;

        return $this;
    }

    /**
     * @return LowStockThreshold|null
     */
    public function getLowStockThreshold(): ?LowStockThreshold
    {
        return $this->lowStockThreshold;
    }

    /**
     * @param int $lowStockThreshold
     *
     * @return self
     */
    public function setLowStockThreshold(int $lowStockThreshold): self
    {
        $this->lowStockThreshold = new LowStockThreshold($lowStockThreshold);

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableNowLabels(): ?array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @param string[] $localizedAvailableNowLabels
     *
     * @return self
     */
    public function setLocalizedAvailableNowLabels(array $localizedAvailableNowLabels): self
    {
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableLaterLabels(): ?array
    {
        return $this->localizedAvailableLaterLabels;
    }

    /**
     * @param string[] $localizedAvailableLaterLabels
     *
     * @return self
     */
    public function setLocalizedAvailableLaterLabels(array $localizedAvailableLaterLabels): self
    {
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getAvailableDate(): ?DateTimeInterface
    {
        return $this->availableDate;
    }

    /**
     * @param DateTimeInterface $availableDate
     *
     * @return self
     */
    public function setAvailableDate(DateTimeInterface $availableDate): self
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return self
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param string $value
     * @param string $propertyName
     */
    private function setDimension(string $value, string $propertyName): void
    {
        $codeByDimension = [
            'width' => ProductConstraintException::INVALID_WIDTH,
            'height' => ProductConstraintException::INVALID_HEIGHT,
            'depth' => ProductConstraintException::INVALID_DEPTH,
            'weight' => ProductConstraintException::INVALID_WEIGHT,
        ];

        try {
            $this->{$propertyName} = new Dimension($value);
        } catch (DomainConstraintException $e) {
            throw new ProductConstraintException(
                sprintf('Invalid product %s.', $propertyName),
                $codeByDimension[$propertyName],
                $e
            );
        }
    }
}
