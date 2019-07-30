<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Attachment\ValueObject\Attachment;
use PrestaShop\PrestaShop\Core\Domain\Product\CustomizationField\ValueObject\CustomizationFieldInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Feature\DTO\FeatureCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\DTO\ImageCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Category;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Condition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\CostPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\FriendlyUrl;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaDescription;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaKeywords;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData\MetaTitle;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductName;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\RedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\RetailPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\TypedRedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\UnitPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference\Upc;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Visibility;

/**
 * Holds the abstraction of common product data which does not depend from product type.
 */
abstract class AbstractProductCommand
{
    /**
     * @todo: I need defaultLanguage validation in handler
     * @var ProductName[]
     */
    private $localizedProductNames;

    /**
     * @todo: maybe put it out of common product command due to this part will be added in asynch way.
     * @var ImageCollection- can be mixed as well.
     */
    private $images;

    /**
     * @var CostPrice
     */
    private $costPrice;

    /**
     * @var RetailPrice
     */
    private $retailPrice;

    /**
     * @var UnitPrice
     */
    private $unitPrice;

    /**
     * @todo: I need cleanHtml validation in handler
     * @var array|string[]
     */
    private $localizedSummary;

    /**
     * @todo: I need cleanHtml validation in handler
     *
     * @var array|string[]
     */
    private $localizedDescription;

    /**
     * @var FeatureCollection
     */
    private $features;

    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @var ProductId[]
     */
    private $relatedProductIds;

    /**
     * @var Category[]
     */
    private $categories;

    /**
     * @var MetaTitle[]
     */
    private $metaTitle;

    /**
     * @var MetaDescription[]
     */
    private $metaDescription;

    /**
     * @var MetaKeywords[]
     */
    private $metaKeywords;

    /**
     * @todo: check if I am required in default language or I am set by product name etc... If so validate
     * @todo: I need link rewrite validation
     *
     * @var FriendlyUrl[]
     */
    private $friendlyUrls;

    /**
     * @var RedirectionPageInterface|TypedRedirectionPageInterface
     */
    private $redirectionPage;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * @var Condition
     */
    private $condition;

    /**
     * @var Reference
     */
    private $reference;

    /**
     * @var Isbn
     */
    private $isbn;

    /**
     * @var Ean13
     */
    private $ean13;

    /**
     * @var Upc
     */
    private $upc;

    /**
     * @todo: I need label for default language validation.
     *
     * @var CustomizationFieldInterface[]
     */
    private $customizationFields;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @param string[] $localizedProductNames
     *
     * @throws ProductConstraintException
     */
    public function __construct(array $localizedProductNames)
    {
        $this->setLocalizedProductNames($localizedProductNames);
    }

    /**
     * @return ProductName[]
     */
    public function getLocalizedProductNames(): array
    {
        return $this->localizedProductNames;
    }

    /**
     * @return ImageCollection
     */
    public function getImages(): ?ImageCollection
    {
        return $this->images;
    }

    /**
     * @param ImageCollection $images
     *
     * @return self
     */
    public function setImages(ImageCollection $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return CostPrice
     */
    public function getCostPrice(): CostPrice
    {
        return $this->costPrice;
    }

    /**
     * @param CostPrice $costPrice
     *
     * @return self
     */
    public function setCostPrice(CostPrice $costPrice): self
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * @return RetailPrice
     */
    public function getRetailPrice(): RetailPrice
    {
        return $this->retailPrice;
    }

    /**
     * @param RetailPrice $retailPrice
     *
     * @return self
     */
    public function setRetailPrice(RetailPrice $retailPrice): self
    {
        $this->retailPrice = $retailPrice;

        return $this;
    }

    /**
     * @return UnitPrice
     */
    public function getUnitPrice(): UnitPrice
    {
        return $this->unitPrice;
    }

    /**
     * @param UnitPrice $unitPrice
     *
     * @return self
     */
    public function setUnitPrice(UnitPrice $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getLocalizedSummary(): ?array
    {
        return $this->localizedSummary;
    }

    /**
     * @param array|string[] $localizedSummary
     *
     * @return self
     */
    public function setLocalizedSummary($localizedSummary): self
    {
        $this->localizedSummary = $localizedSummary;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getLocalizedDescription(): array
    {
        return $this->localizedDescription;
    }

    /**
     * @param array|string[] $localizedDescription
     *
     * @return self
     */
    public function setLocalizedDescription($localizedDescription): self
    {
        $this->localizedDescription = $localizedDescription;

        return $this;
    }

    /**
     * @return FeatureCollection
     */
    public function getFeatures(): FeatureCollection
    {
        return $this->features;
    }

    /**
     * @param FeatureCollection $features
     *
     * @return self
     */
    public function setFeatures(FeatureCollection $features): void
    {
        $this->features = $features;

        return $this;
    }

    /**
     * @return ManufacturerId
     */
    public function getManufacturerId(): ManufacturerId
    {
        return $this->manufacturerId;
    }

    /**
     * @param int $manufacturerId
     *
     * @return self
     *
     * @throws ManufacturerConstraintException
     */
    public function setManufacturerId(int $manufacturerId): self
    {
        $this->manufacturerId = new ManufacturerId($manufacturerId);

        return $this;
    }

    /**
     * @return ProductId[]
     */
    public function getRelatedProductIds(): ?array
    {
        return $this->relatedProductIds;
    }

    /**
     * @param array|int[] $relatedProductIds
     *
     * @return self
     */
    public function setRelatedProductIds($relatedProductIds): self
    {
        $this->relatedProductIds = array_map(
            static function ($item) { return new ProductId($item); },
            $relatedProductIds
        );

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param Category[] $categories
     *
     * @return self
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return MetaTitle[]
     */
    public function getMetaTitle(): ?array
    {
        return $this->metaTitle;
    }

    /**
     * @param string[] $metaTitle
     *
     * @return self
     */
    public function setMetaTitle(array $metaTitle): self
    {
        foreach ($metaTitle as $languageId => $value) {
            $this->metaTitle[$languageId] = new MetaTitle($value);
        }

        return $this;
    }

    /**
     * @return MetaDescription[]
     */
    public function getMetaDescription(): ?array
    {
        return $this->metaDescription;
    }

    /**
     * @param array $metaDescription
     *
     * @return self
     */
    public function setMetaDescription(array $metaDescription): self
    {
        foreach ($metaDescription as $languageId => $value) {
            $this->metaDescription[$languageId] = new MetaDescription($value);
        }

        return $this;
    }

    /**
     * @return MetaKeywords[]
     */
    public function getMetaKeywords(): ?array
    {
        return $this->metaKeywords;
    }

    /**
     * @param string[] $metaKeywords
     *
     * @return self
     */
    public function setMetaKeywords(array $metaKeywords): self
    {
        foreach ($metaKeywords as $languageId => $value) {
            $this->metaKeywords[$languageId] = new MetaKeywords($value);
        }

        return $this;
    }

    /**
     * @return FriendlyUrl[]
     */
    public function getFriendlyUrls(): ?array
    {
        return $this->friendlyUrls;
    }

    /**
     * @param string[] $friendlyUrls
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setFriendlyUrls(array $friendlyUrls): self
    {
        foreach ($friendlyUrls as $languageId => $value) {
            $this->friendlyUrls[$languageId] = new FriendlyUrl($value);
        }

        return $this;
    }

    /**
     * @return RedirectionPageInterface|TypedRedirectionPageInterface
     */
    public function getRedirectionPage(): ?RedirectionPageInterface
    {
        return $this->redirectionPage;
    }

    /**
     * @param RedirectionPageInterface|TypedRedirectionPageInterface $redirectionPage
     *
     * @return self
     */
    public function setRedirectionPage(RedirectionPageInterface $redirectionPage): self
    {
        $this->redirectionPage = $redirectionPage;

        return $this;
    }

    /**
     * @return Visibility
     */
    public function getVisibility(): ?Visibility
    {
        return $this->visibility;
    }

    /**
     * @param Visibility $visibility
     *
     * @return self
     */
    public function setVisibility(Visibility $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return Condition
     */
    public function getCondition(): ?Condition
    {
        return $this->condition;
    }

    /**
     * @param Condition $condition
     *
     * @return self
     */
    public function setCondition(Condition $condition): self
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return Reference
     */
    public function getReference(): Reference
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setReference(string $reference): self
    {
        $this->reference = new Reference($reference);

        return $this;
    }

    /**
     * @return CustomizationFieldInterface[]
     */
    public function getCustomizationFields(): ?array
    {
        return $this->customizationFields;
    }

    /**
     * @param CustomizationFieldInterface[] $customizationFields
     *
     * @return self
     */
    public function setCustomizationFields(array $customizationFields): void
    {
        $this->customizationFields = $customizationFields;
        return $this;
    }

    /**
     * @param array $productNames
     * @return AbstractProductCommand
     *
     * @throws ProductConstraintException
     */
    private function setLocalizedProductNames(array $productNames): self
    {
        foreach ($productNames as $productName) {
            $this->localizedProductNames[] = new ProductName($productName);
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation(): ?array
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     */
    public function setShopAssociation(array $shopAssociation): self
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @return Isbn
     */
    public function getIsbn(): ?Isbn
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setIsbn(string $isbn): self
    {
        $this->isbn = new Isbn($isbn);

        return $this;
    }

    /**
     * @return Ean13
     */
    public function getEan13(): ?Ean13
    {
        return $this->ean13;
    }

    /**
     * @param string $ean13
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setEan13(string $ean13): self
    {
        $this->ean13 = new Ean13($ean13);

        return $this;
    }

    /**
     * @return Upc
     */
    public function getUpc(): ?Upc
    {
        return $this->upc;
    }

    /**
     * @param string $upc
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setUpc(string $upc): self
    {
        $this->upc = new Upc($upc);

        return $this;
    }
}
