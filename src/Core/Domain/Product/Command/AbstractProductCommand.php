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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\RedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\RetailPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\TypedRedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Price\UnitPrice;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Visibility;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\Exception\TaxRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Price;

/**
 * Holds the abstraction of common product data.
 */
abstract class AbstractProductCommand
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
     * @var RetailPrice
     */
    private $retailPrice;

    /**
     * @var UnitPrice
     */
    private $unitPrice;

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
     * @var RedirectionPageInterface|TypedRedirectionPageInterface
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
     * @param string[] $localizedProductNames
     *
     * @throws ProductConstraintException
     */
    public function __construct(array $localizedProductNames)
    {
        $this->setLocalizedProductNames($localizedProductNames);
    }

    /**
     * @return string[]
     */
    public function getLocalizedProductNames(): array
    {
        return $this->localizedProductNames;
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
     * @return RetailPrice
     */
    public function getRetailPrice(): RetailPrice
    {
        return $this->retailPrice;
    }

    /**
     * @param float $priceWithoutTax
     * @param int $taxRuleId
     * @param bool $displayOnSaleFlag
     *
     * @return self
     *
     * @throws ProductConstraintException
     * @throws TaxRuleConstraintException
     */
    public function setRetailPrice(float $priceWithoutTax, int $taxRuleId, bool $displayOnSaleFlag): self
    {
        $this->retailPrice = new RetailPrice(
            $priceWithoutTax,
            $taxRuleId,
            $displayOnSaleFlag
        );

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
     * @param float $price
     * @param string $unit
     *
     * @return self
     *
     * @throws ProductConstraintException
     */
    public function setUnitPrice(float $price, string $unit): self
    {
        $this->unitPrice = new UnitPrice($price, $unit);

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
     * @param array $productNames
     * @return AbstractProductCommand
     *
     * @throws ProductConstraintException
     */
    private function setLocalizedProductNames(array $productNames): self
    {
        $this->localizedProductNames = $productNames;

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
