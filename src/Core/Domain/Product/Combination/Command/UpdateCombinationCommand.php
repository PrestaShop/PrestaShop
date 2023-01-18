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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\LowStockThreshold;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Contains all the data needed to handle the command update.
 *
 * @see UpdateCombinationHandlerInterface
 *
 * This command is only designed for the general data of combination which can be persisted in one call.
 * It was not designed to handle the combination relations.
 */
class UpdateCombinationCommand
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @var bool|null
     */
    private $isDefault;

    /**
     * @var Ean13|null
     */
    private $ean13;

    /**
     * @var Isbn|null
     */
    private $isbn;

    /**
     * @var string|null
     */
    private $mpn;

    /**
     * @var Reference|null
     */
    private $reference;

    /**
     * @var Upc|null
     */
    private $upc;

    /**
     * @var DecimalNumber|null
     */
    private $impactOnWeight;

    /**
     * @var DecimalNumber|null
     */
    private $impactOnPrice;

    /**
     * @var DecimalNumber|null
     */
    private $ecoTax;

    /**
     * @var DecimalNumber|null
     */
    private $impactOnUnitPrice;

    /**
     * @var DecimalNumber|null
     */
    private $wholesalePrice;

    /**
     * @var int|null
     */
    private $minimalQuantity;

    /**
     * @var LowStockThreshold|null
     */
    private $lowStockThreshold;

    /**
     * @var DateTimeInterface|null
     */
    private $availableDate;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param int $combinationId
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        int $combinationId,
        ShopConstraint $shopConstraint
    ) {
        $this->combinationId = new CombinationId($combinationId);
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return CombinationId
     */
    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return bool|null
     */
    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool|null $isDefault
     *
     * @return static
     */
    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;

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
     * @return $this
     */
    public function setEan13(string $ean13): self
    {
        $this->ean13 = new Ean13($ean13);

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
     * @return $this
     */
    public function setIsbn(string $isbn): self
    {
        $this->isbn = new Isbn($isbn);

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
     * @return $this
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
     * @return $this
     */
    public function setReference(string $reference): self
    {
        $this->reference = new Reference($reference);

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
     * @return $this
     */
    public function setUpc(string $upc): self
    {
        $this->upc = new Upc($upc);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getImpactOnWeight(): ?DecimalNumber
    {
        return $this->impactOnWeight;
    }

    /**
     * @param string $impactOnWeight
     *
     * @return $this
     */
    public function setImpactOnWeight(string $impactOnWeight): self
    {
        $this->impactOnWeight = new DecimalNumber($impactOnWeight);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getImpactOnPrice(): ?DecimalNumber
    {
        return $this->impactOnPrice;
    }

    /**
     * @param string $impactOnPrice
     *
     * @return $this
     */
    public function setImpactOnPrice(string $impactOnPrice): self
    {
        $this->impactOnPrice = new DecimalNumber($impactOnPrice);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getEcoTax(): ?DecimalNumber
    {
        return $this->ecoTax;
    }

    /**
     * @param string $ecoTax
     *
     * @return $this
     */
    public function setEcoTax(string $ecoTax): self
    {
        $this->ecoTax = new DecimalNumber($ecoTax);

        return $this;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getImpactOnUnitPrice(): ?DecimalNumber
    {
        return $this->impactOnUnitPrice;
    }

    /**
     * @param string $impactOnUnitPrice
     *
     * @return $this
     */
    public function setImpactOnUnitPrice(string $impactOnUnitPrice): self
    {
        $this->impactOnUnitPrice = new DecimalNumber($impactOnUnitPrice);

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
     * @return $this
     */
    public function setWholesalePrice(string $wholesalePrice): self
    {
        $this->wholesalePrice = new DecimalNumber($wholesalePrice);

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
     * @return $this
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
     * @return $this
     */
    public function setLowStockThreshold(int $lowStockThreshold): self
    {
        $this->lowStockThreshold = new LowStockThreshold($lowStockThreshold);

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
     * @return $this
     */
    public function setAvailableDate(DateTimeInterface $availableDate): self
    {
        $this->availableDate = $availableDate;

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
     * @return $this
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
     * @return $this
     */
    public function setLocalizedAvailableLaterLabels(array $localizedAvailableLaterLabels): self
    {
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;

        return $this;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
