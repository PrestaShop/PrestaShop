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

namespace PrestaShop\PrestaShop\Core\Domain\SpecificPrice\QueryResult;

use DateTimeInterface;
use PrestaShop\Decimal\Number;

/**
 * Transfers data for editing SpecificPrice
 */
class SpecificPriceForEditing
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $reductionType;

    /**
     * @var Number
     */
    private $reductionValue;

    /**
     * @var bool
     */
    private $includeTax;

    /**
     * @var string
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
    private $combinationId;

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
     * @var string|null
     */
    private $dateTimeFrom;

    /**
     * @var string|null
     */
    private $dateTimeTo;

    /**
     * @param int $productId
     * @param string $reductionType
     * @param Number $reductionValue
     * @param bool $includeTax
     * @param Number $price
     * @param int $fromQuantity
     * @param int $shopGroupId
     * @param int $shopId
     * @param int $cartId
     * @param int $combinationId
     * @param int $currencyId
     * @param int $catalogPriceRuleId
     * @param int $countryId
     * @param int $groupId
     * @param int $customerId
     * @param DateTimeInterface $dateTimeFrom
     * @param DateTimeInterface $dateTimeTo
     */
    public function __construct(
        int $productId,
        string $reductionType,
        Number $reductionValue,
        bool $includeTax,
        Number $price,
        int $fromQuantity,
        int $shopGroupId,
        int $shopId,
        int $cartId,
        int $combinationId,
        int $currencyId,
        int $catalogPriceRuleId,
        int $countryId,
        int $groupId,
        int $customerId,
        DateTimeInterface $dateTimeFrom,
        DateTimeInterface $dateTimeTo
    ) {
        $this->productId = $productId;
        $this->reductionType = $reductionType;
        $this->reductionValue = $reductionValue;
        $this->includeTax = $includeTax;
        $this->price = $price;
        $this->fromQuantity = $fromQuantity;
        $this->shopGroupId = $shopGroupId;
        $this->shopId = $shopId;
        $this->cartId = $cartId;
        $this->combinationId = $combinationId;
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
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getReductionType(): string
    {
        return $this->reductionType;
    }

    /**
     * @return Number
     */
    public function getReductionValue(): Number
    {
        return $this->reductionValue;
    }

    /**
     * @return bool
     */
    public function isIncludeTax(): bool
    {
        return $this->includeTax;
    }

    /**
     * @return string
     */
    public function getPrice(): string
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
    public function getCombinationId(): ?int
    {
        return $this->combinationId;
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
     * @return string|null
     */
    public function getDateTimeFrom(): ?string
    {
        return $this->dateTimeFrom;
    }

    /**
     * @return string|null
     */
    public function getDateTimeTo(): ?string
    {
        return $this->dateTimeTo;
    }
}
