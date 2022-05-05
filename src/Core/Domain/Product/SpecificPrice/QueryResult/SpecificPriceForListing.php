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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\QueryResult;

use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\FixedPriceInterface;

class SpecificPriceForListing
{
    /**
     * @var int
     */
    private $specificPriceId;

    /**
     * @var string
     */
    private $reductionType;

    /**
     * @var DecimalNumber
     */
    private $reductionValue;

    /**
     * @var bool
     */
    private $includesTax;

    /**
     * @var FixedPriceInterface
     */
    private $fixedPrice;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var string|null
     */
    private $shopName;

    /**
     * @var string|null
     */
    private $currencyName;

    /**
     * @var string|null
     */
    private $currencyISOCode;

    /**
     * @var string|null
     */
    private $countryName;

    /**
     * @var string|null
     */
    private $groupName;

    /**
     * @var string|null
     */
    private $customerName;

    /**
     * @var string|null
     */
    private $combinationName;

    /**
     * @var DateTimeInterface
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface
     */
    private $dateTimeTo;

    /**
     * @param int $specificPriceId
     * @param string $reductionType
     * @param DecimalNumber $reductionValue
     * @param bool $includesTax
     * @param FixedPriceInterface $fixedPrice
     * @param int $fromQuantity
     * @param DateTimeInterface $dateTimeFrom
     * @param DateTimeInterface $dateTimeTo
     * @param string|null $combinationName
     * @param string|null $shop
     * @param string|null $currencyName
     * @param string|null $currencyISOCode
     * @param string|null $country
     * @param string|null $group
     * @param string|null $customer
     */
    public function __construct(
        int $specificPriceId,
        string $reductionType,
        DecimalNumber $reductionValue,
        bool $includesTax,
        FixedPriceInterface $fixedPrice,
        int $fromQuantity,
        DateTimeInterface $dateTimeFrom,
        DateTimeInterface $dateTimeTo,
        ?string $combinationName,
        ?string $shop,
        ?string $currencyName,
        ?string $currencyISOCode,
        ?string $country,
        ?string $group,
        ?string $customer
    ) {
        $this->specificPriceId = $specificPriceId;
        $this->reductionType = $reductionType;
        $this->reductionValue = $reductionValue;
        $this->includesTax = $includesTax;
        $this->fixedPrice = $fixedPrice;
        $this->fromQuantity = $fromQuantity;
        $this->dateTimeFrom = $dateTimeFrom;
        $this->dateTimeTo = $dateTimeTo;
        $this->combinationName = $combinationName;
        $this->shopName = $shop;
        $this->currencyName = $currencyName;
        $this->currencyISOCode = $currencyISOCode;
        $this->countryName = $country;
        $this->groupName = $group;
        $this->customerName = $customer;
    }

    /**
     * @return int
     */
    public function getSpecificPriceId(): int
    {
        return $this->specificPriceId;
    }

    /**
     * @return string
     */
    public function getReductionType(): string
    {
        return $this->reductionType;
    }

    /**
     * @return DecimalNumber
     */
    public function getReductionValue(): DecimalNumber
    {
        return $this->reductionValue;
    }

    /**
     * @return bool
     */
    public function includesTax(): bool
    {
        return $this->includesTax;
    }

    /**
     * @return string|null
     */
    public function getCombinationName(): ?string
    {
        return $this->combinationName;
    }

    /**
     * @return FixedPriceInterface
     */
    public function getFixedPrice(): FixedPriceInterface
    {
        return $this->fixedPrice;
    }

    /**
     * @return int
     */
    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    /**
     * @return string|null
     */
    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    /**
     * @return string|null
     */
    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    /**
     * @return string|null
     */
    public function getCurrencyISOCode(): ?string
    {
        return $this->currencyISOCode;
    }

    /**
     * @return string|null
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * @return string|null
     */
    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    /**
     * @return string|null
     */
    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTimeFrom(): DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTimeTo(): DateTimeInterface
    {
        return $this->dateTimeTo;
    }
}
