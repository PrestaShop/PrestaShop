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

class SpecificPriceForEditing
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
    private $reductionAmount;

    /**
     * @var bool
     */
    private $includeTax;

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
     * @var int|null
     */
    private $cartId;

    /**
     * @var int|null
     */
    private $currencyId;

    /**
     * @var int|null
     */
    private $catalogPriceRuleId;

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
     * @param string $reductionType
     * @param DecimalNumber $reductionAmount
     * @param bool $includeTax
     * @param DecimalNumber $price
     * @param int $fromQuantity
     * @param int|null $shopGroupId
     * @param int|null $shopId
     * @param int|null $cartId
     * @param int|null $currencyId
     * @param int|null $catalogPriceRuleId
     * @param int|null $countryId
     * @param int|null $groupId
     * @param int|null $customerId
     * @param DateTime|null $dateTimeFrom
     * @param DateTime|null $dateTimeTo
     */
    public function __construct(
        int $specificPriceId,
        string $reductionType,
        DecimalNumber $reductionAmount,
        bool $includeTax,
        DecimalNumber $price,
        int $fromQuantity,
        ?DateTime $dateTimeFrom,
        ?DateTime $dateTimeTo,
        ?int $shopGroupId,
        ?int $shopId,
        ?int $cartId,
        ?int $currencyId,
        ?int $catalogPriceRuleId,
        ?int $countryId,
        ?int $groupId,
        ?int $customerId
    ) {
        $this->specificPriceId = $specificPriceId;
        $this->reductionType = $reductionType;
        $this->reductionAmount = $reductionAmount;
        $this->includeTax = $includeTax;
        $this->price = $price;
        $this->fromQuantity = $fromQuantity;
        $this->shopGroupId = $shopGroupId;
        $this->shopId = $shopId;
        $this->cartId = $cartId;
        $this->currencyId = $currencyId;
        $this->catalogPriceRuleId = $catalogPriceRuleId;
        $this->countryId = $countryId;
        $this->groupId = $groupId;
        $this->customerId = $customerId;
        $this->dateTimeFrom = $dateTimeFrom;
        $this->dateTimeTo = $dateTimeTo;
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
    public function getReductionAmount(): DecimalNumber
    {
        return $this->reductionAmount;
    }

    /**
     * @return bool
     */
    public function isIncludeTax(): bool
    {
        return $this->includeTax;
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
     * @return int|null
     */
    public function getShopGroupId(): ?int
    {
        return $this->shopGroupId;
    }

    /**
     * @return int|null
     */
    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    /**
     * @return int|null
     */
    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    /**
     * @return int|null
     */
    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    /**
     * @return int|null
     */
    public function getCatalogPriceRuleId(): ?int
    {
        return $this->catalogPriceRuleId;
    }

    /**
     * @return int|null
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->customerId;
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
