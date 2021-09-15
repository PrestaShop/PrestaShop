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

use DateTime;
use PrestaShop\Decimal\DecimalNumber;

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
     * @var DecimalNumber
     */
    private $price;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var string
     */
    private $shopGroup;

    /**
     * @var string
     */
    private $shop;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $customer;

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
     * @param string $reductionType
     * @param DecimalNumber $reductionValue
     * @param bool $includesTax
     * @param DecimalNumber $price
     * @param int $fromQuantity
     * @param DateTime|null $dateTimeFrom
     * @param DateTime|null $dateTimeTo
     * @param string|null $shopGroup
     * @param string|null $shop
     * @param string|null $currency
     * @param string|null $country
     * @param string|null $group
     * @param string|null $customer
     */
    public function __construct(
        int $specificPriceId,
        string $reductionType,
        DecimalNumber $reductionValue,
        bool $includesTax,
        DecimalNumber $price,
        int $fromQuantity,
        ?DateTime $dateTimeFrom,
        ?DateTime $dateTimeTo,
        ?string $shopGroup,
        ?string $shop,
        ?string $currency,
        ?string $country,
        ?string $group,
        ?string $customer
    ) {
        $this->specificPriceId = $specificPriceId;
        $this->reductionType = $reductionType;
        $this->reductionValue = $reductionValue;
        $this->includesTax = $includesTax;
        $this->price = $price;
        $this->fromQuantity = $fromQuantity;
        $this->dateTimeFrom = $dateTimeFrom;
        $this->dateTimeTo = $dateTimeTo;
        $this->shopGroup = $shopGroup;
        $this->shop = $shop;
        $this->currency = $currency;
        $this->country = $country;
        $this->group = $group;
        $this->customer = $customer;
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
     * @return DecimalNumber
     */
    public function getPrice(): DecimalNumber
    {
        return $this->price;
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
    public function getShopGroup(): ?string
    {
        return $this->shopGroup;
    }

    /**
     * @return string|null
     */
    public function getShop(): string
    {
        return $this->shop;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string|null
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return string|null
     */
    public function getCustomer(): string
    {
        return $this->customer;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeFrom(): ?DateTime
    {
        return $this->dateTimeFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTimeTo(): ?DateTime
    {
        return $this->dateTimeTo;
    }
}
