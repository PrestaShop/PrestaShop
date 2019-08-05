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

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Condition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\OfflineRedirectionPage;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\RedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\TypedRedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Visibility;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Price;

/**
 * Holds product edit data.
 */
class EditProductCommand
{
    /**
     * @todo: I need defaultLanguage validation in handler
     * @var string[]
     */
    private $localizedProductNames;

    /**
     * @var Price
     */
    private $costPrice;

    /**
     * @var Price
     */
    private $retailPrice;

    /**
     * @var TaxRuleId
     */
    private $taxRuleId;

    /**
     * @var bool
     */
    private $displayOnSaleFlag;

    /**
     * @var Price
     */
    private $unitPrice;

    /**
     * @var string
     */
    private $unit;

    /**
     * @todo: I need cleanHtml validation in handler
     * @var string[]
     */
    private $localizedSummary;

    /**
     * @todo: I need cleanHtml validation in handler
     *
     * @var string[]
     */
    private $localizedDescription;

    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @var string[]
     */
    private $metaTitle;

    /**
     * @var string[]
     */
    private $metaDescription;

    /**
     * @var string[]
     */
    private $metaKeywords;

    /**
     * @todo: check if I am required in default language or I am set by product name etc... If so validate
     * @todo: I need link rewrite validation
     *
     * @var string[]
     */
    private $friendlyUrls;

    /**
     * @var OfflineRedirectionPage
     */
    private $redirectionPage;

    /**
     * @var Visibility
     */
    private $visibility;

    /**
     * @var bool
     */
    private $isAvailableForOrder;

    /**
     * @var bool
     */
    private $isWebOnly;

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
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @var bool
     */
    private $isConditionDisplayedOnProductPage;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var ProductId
     */
    private $productId;

    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @param string[] $localizedProductNames
     *
     * @return self
     */
    public function setLocalizedProductNames(array $localizedProductNames): self
    {
        $this->localizedProductNames = $localizedProductNames;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedProductNames(): array
    {
        return $this->localizedProductNames;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param bool $isEnabled
     *
     * @return self
     */
    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * @return Price
     */
    public function getCostPrice(): Price
    {
        return $this->costPrice;
    }

    /**
     * @param float $costPrice
     *
     * @return self
     *
     * @throws DomainConstraintException
     */
    public function setCostPrice(float $costPrice): self
    {
        $this->costPrice = new Price($costPrice);

        return $this;
    }

    /**
     * @return Price
     */
    public function getRetailPrice(): Price
    {
        return $this->retailPrice;
    }

    /**
     * @return TaxRuleId
     */
    public function getTaxRuleId(): TaxRuleId
    {
        return $this->taxRuleId;
    }

    /**
     * @param int $taxRuleId
     *
     * @return self
     *
     * @throws TaxRuleConstraintException
     */
    public function setTaxRuleId(int $taxRuleId): self
    {
        $this->taxRuleId = new TaxRuleId($taxRuleId);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayOnSaleFlag(): bool
    {
        return $this->displayOnSaleFlag;
    }

    /**
     * @param bool $displayOnSaleFlag
     *
     * @return self
     */
    public function setDisplayOnSaleFlag(bool $displayOnSaleFlag): self
    {
        $this->displayOnSaleFlag = $displayOnSaleFlag;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     *
     * @return self
     */
    public function setUnit(string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @param float $priceWithoutTax
     *
     * @return self
     *
     * @throws DomainConstraintException
     */
    public function setRetailPrice(float $priceWithoutTax): self
    {
        $this->retailPrice = new Price($priceWithoutTax);

        return $this;
    }

    /**
     * @return Price
     */
    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    /**
     * @param float $price
     *
     * @return self
     *
     * @throws DomainConstraintException
     */
    public function setUnitPrice(float $price): self
    {
        $this->unitPrice = new Price($price);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedSummary(): ?array
    {
        return $this->localizedSummary;
    }

    /**
     * @param string[] $localizedSummary
     *
     * @return self
     */
    public function setLocalizedSummary($localizedSummary): self
    {
        $this->localizedSummary = $localizedSummary;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescription(): array
    {
        return $this->localizedDescription;
    }

    /**
     * @param string[] $localizedDescription
     *
     * @return self
     */
    public function setLocalizedDescription($localizedDescription): self
    {
        $this->localizedDescription = $localizedDescription;

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
     * @return string[]
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
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string[]
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
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string[]
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
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFriendlyUrls(): ?array
    {
        return $this->friendlyUrls;
    }

    /**
     * @param string[] $friendlyUrls
     *
     * @return self
     */
    public function setFriendlyUrls(array $friendlyUrls): self
    {
        $this->friendlyUrls = $friendlyUrls;

        return $this;
    }

    /**
     * @return OfflineRedirectionPage
     */
    public function getRedirectionPage(): ?OfflineRedirectionPage
    {
        return $this->redirectionPage;
    }

    /**
     * @param string $redirectionType
     * @param int|null $resourceId
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setRedirectionPage(string $redirectionType, ?int $resourceId): self
    {
        $this->redirectionPage = new OfflineRedirectionPage($redirectionType, $resourceId);

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
     * @param string $visibility
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setVisibility(string $visibility): self
    {
        $this->visibility = new Visibility($visibility);

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
     * @param string $condition
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setCondition(string $condition): self
    {
        $this->condition = new Condition($condition);

        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailableForOrder(): bool
    {
        return $this->isAvailableForOrder;
    }

    /**
     * @param bool $isAvailableForOrder
     *
     * @return self
     */
    public function setIsAvailableForOrder(bool $isAvailableForOrder): self
    {
        $this->isAvailableForOrder = $isAvailableForOrder;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWebOnly(): bool
    {
        return $this->isWebOnly;
    }

    /**
     * @param bool $isWebOnly
     *
     * @return self
     */
    public function setIsWebOnly(bool $isWebOnly): self
    {
        $this->isWebOnly = $isWebOnly;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConditionDisplayedOnProductPage(): bool
    {
        return $this->isConditionDisplayedOnProductPage;
    }

    /**
     * @param bool $isConditionDisplayedOnProductPage
     *
     * @return self
     */
    public function setIsConditionDisplayedOnProductPage(bool $isConditionDisplayedOnProductPage): self
    {
        $this->isConditionDisplayedOnProductPage = $isConditionDisplayedOnProductPage;
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
