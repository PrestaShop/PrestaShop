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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

class EditProductSpecificPriceCommand
{
    /**
     * //@todo: replace with Product/SpecificPriceId
     *
     * @var SpecificPriceId
     */
    private $specificPriceId;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @var bool
     */
    private $includesTax;

    /**
     * @var DecimalNumber
     */
    private $price;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var int|null
     */
    private $shopGroupId;

    /**
     * @var int|null
     */
    private $shopId;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $countryId;

    /**
     * @var int|null
     */
    private $groupId;

    /**
     * @var int|null
     */
    private $customerId;

    /**
     * @var DateTime|null
     */
    private $dateTimeFrom;

    /**
     * @var DateTime|null
     */
    private $dateTimeTo;

    /**
     * @param int $specificPriceId
     */
    public function __construct(
        int $specificPriceId
    ) {
        $this->specificPriceId = new SpecificPriceId($specificPriceId);
    }

    /**
     * @return SpecificPriceId
     */
    public function getSpecificPriceId(): SpecificPriceId
    {
        return $this->specificPriceId;
    }

    /**
     * @return Reduction
     */
    public function getReduction(): Reduction
    {
        return $this->reduction;
    }

    /**
     * @param Reduction $reduction
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setReduction(Reduction $reduction): EditProductSpecificPriceCommand
    {
        $this->reduction = $reduction;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludesTax(): bool
    {
        return $this->includesTax;
    }

    /**
     * @param bool $includesTax
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setIncludesTax(bool $includesTax): EditProductSpecificPriceCommand
    {
        $this->includesTax = $includesTax;

        return $this;
    }

    /**
     * @return DecimalNumber
     */
    public function getPrice(): DecimalNumber
    {
        return $this->price;
    }

    /**
     * @param DecimalNumber $price
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setPrice(DecimalNumber $price): EditProductSpecificPriceCommand
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    /**
     * @param int $fromQuantity
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setFromQuantity(int $fromQuantity): EditProductSpecificPriceCommand
    {
        $this->fromQuantity = $fromQuantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getShopGroupId(): ?int
    {
        return $this->shopGroupId;
    }

    /**
     * @param int|null $shopGroupId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setShopGroupId(?int $shopGroupId): EditProductSpecificPriceCommand
    {
        $this->shopGroupId = $shopGroupId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    /**
     * @param int|null $shopId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setShopId(?int $shopId): EditProductSpecificPriceCommand
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @param CombinationId|null $combinationId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCombinationId(?CombinationId $combinationId): EditProductSpecificPriceCommand
    {
        $this->combinationId = $combinationId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    /**
     * @param int|null $currencyId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCurrencyId(?int $currencyId): EditProductSpecificPriceCommand
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int|null $countryId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCountryId(?int $countryId): EditProductSpecificPriceCommand
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param int|null $groupId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setGroupId(?int $groupId): EditProductSpecificPriceCommand
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    /**
     * @param int|null $customerId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCustomerId(?int $customerId): EditProductSpecificPriceCommand
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param DateTime|null $dateTimeFrom
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setDateTimeFrom(?DateTime $dateTimeFrom): EditProductSpecificPriceCommand
    {
        $this->dateTimeFrom = $dateTimeFrom;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }

    /**
     * @param DateTime|null $dateTimeTo
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setDateTimeTo(?DateTime $dateTimeTo): EditProductSpecificPriceCommand
    {
        $this->dateTimeTo = $dateTimeTo;

        return $this;
    }
}
