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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopIdInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

class EditProductSpecificPriceCommand
{
    /**
     * @var SpecificPriceId
     */
    private $specificPriceId;

    /**
     * @var Reduction|null
     */
    private $reduction;

    /**
     * @var bool|null
     */
    private $includesTax;

    /**
     * @var DecimalNumber|null
     */
    private $price;

    /**
     * @var int|null
     */
    private $fromQuantity;

    /**
     * @var ShopGroupIdInterface|null
     */
    private $shopGroupId;

    /**
     * @var ShopIdInterface|null
     */
    private $shopId;

    /**
     * @var CombinationIdInterface|null
     */
    private $combinationId;

    /**
     * @var int|null
     *
     * @todo: introduce CurrencyIdInterface (refer to example of ManufacturerIdInterface)
     */
    private $currencyId;

    /**
     * @var int|null
     *
     * @todo: introduce CountryIdInterface (refer to example of ManufacturerIdInterface)
     */
    private $countryId;

    /**
     * @var int|null
     *
     * @todo: introduce GroupIdInterface (refer to example of ManufacturerIdInterface). GroupId VO doesnt exist yet
     */
    private $groupId;

    /**
     * @var int|null
     *
     * @todo: introduce CustomerIdInterface (refer to example of ManufacturerIdInterface)
     */
    private $customerId;

    /**
     * @todo: its impossible to reset $from to NULL (database doesnt support null and the 0000-00-00 is actually invalid)
     *      for that we could have some "DateRange" Value object which would contain ranges OR some "no-range" value.
     *      Also this would require BC break - making datetime column nullable in specific_price table.
     *
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
    public function __construct(int $specificPriceId)
    {
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
     * @return Reduction|null
     */
    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    /**
     * @param string $reductionType
     * @param string $reductionValue
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setReduction(string $reductionType, string $reductionValue): EditProductSpecificPriceCommand
    {
        $this->reduction = new Reduction($reductionType, $reductionValue);

        return $this;
    }

    /**
     * @return bool|null
     */
    public function includesTax(): ?bool
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
     * @return DecimalNumber|null
     */
    public function getPrice(): ?DecimalNumber
    {
        return $this->price;
    }

    /**
     * @param string $price
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setPrice(string $price): EditProductSpecificPriceCommand
    {
        $this->price = new DecimalNumber($price);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFromQuantity(): ?int
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
     * @return ShopGroupIdInterface|null
     */
    public function getShopGroupId(): ?ShopGroupIdInterface
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
        $this->shopGroupId = NoShopGroupId::NO_SHOP_GROUP_ID === $shopGroupId ? new NoShopGroupId() : new ShopGroupId($shopGroupId);

        return $this;
    }

    /**
     * @return ShopIdInterface|null
     */
    public function getShopId(): ?ShopIdInterface
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
        $this->shopId = NoShopId::NO_SHOP_ID === $shopId ? new NoShopId() : new ShopId($shopId);

        return $this;
    }

    /**
     * @return CombinationIdInterface|null
     */
    public function getCombinationId(): ?CombinationIdInterface
    {
        return $this->combinationId;
    }

    /**
     * @param int|null $combinationId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCombinationId(?int $combinationId): EditProductSpecificPriceCommand
    {
        $this->combinationId = NoCombinationId::NO_COMBINATION_ID === $combinationId ? new NoCombinationId() : new CombinationId($combinationId);

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
