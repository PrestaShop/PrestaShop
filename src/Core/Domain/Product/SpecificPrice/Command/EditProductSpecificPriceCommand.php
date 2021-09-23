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
use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopIdInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

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
     * @var ShopIdInterface|null
     */
    private $shopId;

    /**
     * @var CombinationIdInterface|null
     */
    private $combinationId;

    /**
     * @var CurrencyIdInterface|null
     */
    private $currencyId;

    /**
     * @var CountryId|null
     */
    private $countryId;

    /**
     * @var GroupIdInterface|null
     */
    private $groupId;

    /**
     * @var CustomerId|null
     */
    private $customerId;

    /**
     * @var DateTimeInterface|null
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface|null
     *
     * @see DateTime
     * @see NullDateTime
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
    public function setReduction(string $reductionType, string $reductionValue): self
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
    public function setIncludesTax(bool $includesTax): self
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
    public function setPrice(string $price): self
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
    public function setFromQuantity(int $fromQuantity): self
    {
        $this->fromQuantity = $fromQuantity;

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
     * @param int $shopId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setShopId(int $shopId): self
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
     * @param int $combinationId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCombinationId(int $combinationId): self
    {
        $this->combinationId = NoCombinationId::NO_COMBINATION_ID === $combinationId ? new NoCombinationId() : new CombinationId($combinationId);

        return $this;
    }

    /**
     * @return CurrencyIdInterface|null
     */
    public function getCurrencyId(): ?CurrencyIdInterface
    {
        return $this->currencyId;
    }

    /**
     * @param int $currencyId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCurrencyId(int $currencyId): self
    {
        $this->currencyId = NoCurrencyId::NO_CURRENCY_ID === $currencyId ? new NoCurrencyId() : new CurrencyId($currencyId);

        return $this;
    }

    /**
     * @return CountryId|null
     */
    public function getCountryId(): ?CountryId
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = new CountryId($countryId);

        return $this;
    }

    /**
     * @return GroupIdInterface|null
     */
    public function getGroupId(): ?GroupIdInterface
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setGroupId(int $groupId): self
    {
        $this->groupId = NoGroupId::NO_GROUP_ID === $groupId ? new NoGroupId() : new GroupId($groupId);

        return $this;
    }

    /**
     * @return CustomerId|null
     */
    public function getCustomerId(): ?CustomerId
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setCustomerId(int $customerId): self
    {
        $this->customerId = new CustomerId($customerId);

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTimeFrom(): ?DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param DateTimeInterface|null $dateTimeFrom
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setDateTimeFrom(?DateTimeInterface $dateTimeFrom): self
    {
        $this->dateTimeFrom = $dateTimeFrom;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateTimeTo(): ?DateTimeInterface
    {
        return $this->dateTimeTo;
    }

    /**
     * @param DateTimeInterface|null $dateTimeTo
     *
     * @return EditProductSpecificPriceCommand
     */
    public function setDateTimeTo(?DateTimeInterface $dateTimeTo): self
    {
        $this->dateTimeTo = $dateTimeTo;

        return $this;
    }
}
