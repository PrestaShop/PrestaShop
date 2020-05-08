<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

use DateTime;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Provides data for editing CatalogPriceRule
 */
class EditableCartRule
{
    /**
     * @var CartRuleId
     */
    private $cartRuleId;

    /**
     * @var array
     */
    private $localizedNames;

    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var DateTime|null
     */
    private $dateFrom;

    /**
     * @var DateTime|null
     */
    private $dateTo;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var int
     */
    private $quantityPerUser;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $partialUse;

    /**
     * @var string
     */
    private $code;

    /**
     * @var Number
     */
    private $minimumAmount;

    /**
     * @var bool
     */
    private $minimumAmountTax;

    /**
     * @var CurrencyId
     */
    private $minimumAmountCurrencyId;

    /**
     * @var bool
     */
    private $minimumAmountShipping;

    /**
     * @var bool
     */
    private $countryRestriction;

    /**
     * @var bool
     */
    private $carrierRestriction;

    /**
     * @var bool
     */
    private $groupRestriction;

    /**
     * @var bool
     */
    private $cartRuleRestriction;

    /**
     * @var bool
     */
    private $productRestriction;

    /**
     * @var bool
     */
    private $shopRestriction;

    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @var Number
     */
    private $reductionPercent;

    /**
     * @var Number
     */
    private $reductionAmount;

    /**
     * @var bool
     */
    private $reductionTax;

    /**
     * @var CurrencyId
     */
    private $reductionCurrencyId;

    /**
     * @var ProductId
     */
    private $reductionProductId;

    /**
     * @var bool
     */
    private $reductionExcludeSpecial;

    /**
     * @var ProductId
     */
    private $giftProductId;

    /**
     * @var CombinationId
     */
    private $giftCombinationId;

    /**
     * @var bool
     */
    private $highlight;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var DateTime|null
     */
    private $dateAdd;

    /**
     * @var DateTime|null
     */
    private $dateUpd;

    public function __construct(
        CartRuleId $cartRuleId,
        array $localizedNames,
        ?CustomerId $customerId,
        ?DateTime $dateFrom,
        ?DateTime $dateTo,
        string $description,
        int $quantity,
        int $quantityPerUser,
        int $priority,
        bool $partialUse,
        string $code,
        Number $minimumAmount,
        bool $minimumAmountTax,
        CurrencyId $minimumAmountCurrencyId,
        bool $minimumAmountShipping,
        bool $countryRestriction,
        bool $carrierRestriction,
        bool $groupRestriction,
        bool $cartRuleRestriction,
        bool $productRestriction,
        bool $shopRestriction,
        bool $freeShipping,
        Number $reductionPercent,
        Number $reductionAmount,
        bool $reductionTax,
        ?CurrencyId $reductionCurrencyId,
        ?ProductId $reductionProductId,
        bool $reductionExcludeSpecial,
        ?ProductId $giftProductId,
        ?CombinationId $giftCombinationId,
        bool $highlight,
        bool $enabled,
        ?DateTime $dateAdd,
        ?DateTime $dateUpd
    ) {
        $this->cartRuleId = $cartRuleId;
        $this->localizedNames = $localizedNames;
        $this->customerId = $customerId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->quantityPerUser = $quantityPerUser;
        $this->priority = $priority;
        $this->partialUse = $partialUse;
        $this->code = $code;
        $this->minimumAmount = $minimumAmount;
        $this->minimumAmountTax = $minimumAmountTax;
        $this->minimumAmountCurrencyId = $minimumAmountCurrencyId;
        $this->minimumAmountShipping = $minimumAmountShipping;
        $this->countryRestriction = $countryRestriction;
        $this->carrierRestriction = $carrierRestriction;
        $this->groupRestriction = $groupRestriction;
        $this->cartRuleRestriction = $cartRuleRestriction;
        $this->productRestriction = $productRestriction;
        $this->shopRestriction = $shopRestriction;
        $this->freeShipping = $freeShipping;
        $this->reductionPercent = $reductionPercent;
        $this->reductionAmount = $reductionAmount;
        $this->reductionTax = $reductionTax;
        $this->reductionCurrencyId = $reductionCurrencyId;
        $this->reductionProductId = $reductionProductId;
        $this->reductionExcludeSpecial = $reductionExcludeSpecial;
        $this->giftProductId = $giftProductId;
        $this->giftCombinationId = $giftCombinationId;
        $this->highlight = $highlight;
        $this->enabled = $enabled;
        $this->dateAdd = $dateAdd;
        $this->dateUpd = $dateUpd;
    }

    /**
     * @return CartRuleId
     */
    public function getCartRuleId(): CartRuleId
    {
        return $this->cartRuleId;
    }

    /**
     * @return array
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return CustomerId|null
     */
    public function getCustomerId(): ?CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return DateTime|null
     */
    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTo(): ?DateTime
    {
        return $this->dateTo;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getQuantityPerUser(): int
    {
        return $this->quantityPerUser;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return bool
     */
    public function isPartialUse(): bool
    {
        return $this->partialUse;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Number
     */
    public function getMinimumAmount(): Number
    {
        return $this->minimumAmount;
    }

    /**
     * @return bool
     */
    public function isMinimumAmountTax(): bool
    {
        return $this->minimumAmountTax;
    }

    /**
     * @return CurrencyId|null
     */
    public function getMinimumAmountCurrencyId(): ?CurrencyId
    {
        return $this->minimumAmountCurrencyId;
    }

    /**
     * @return bool
     */
    public function isMinimumAmountShipping(): bool
    {
        return $this->minimumAmountShipping;
    }

    /**
     * @return bool
     */
    public function isCountryRestriction(): bool
    {
        return $this->countryRestriction;
    }

    /**
     * @return bool
     */
    public function isCarrierRestriction(): bool
    {
        return $this->carrierRestriction;
    }

    /**
     * @return bool
     */
    public function isGroupRestriction(): bool
    {
        return $this->groupRestriction;
    }

    /**
     * @return bool
     */
    public function isCartRuleRestriction(): bool
    {
        return $this->cartRuleRestriction;
    }

    /**
     * @return bool
     */
    public function isProductRestriction(): bool
    {
        return $this->productRestriction;
    }

    /**
     * @return bool
     */
    public function isShopRestriction(): bool
    {
        return $this->shopRestriction;
    }

    /**
     * @return bool
     */
    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    /**
     * @return Number
     */
    public function getReductionPercent(): Number
    {
        return $this->reductionPercent;
    }

    /**
     * @return Number
     */
    public function getReductionAmount(): Number
    {
        return $this->reductionAmount;
    }

    /**
     * @return bool
     */
    public function isReductionTax(): bool
    {
        return $this->reductionTax;
    }

    /**
     * @return CurrencyId|null
     */
    public function getReductionCurrencyId(): ?CurrencyId
    {
        return $this->reductionCurrencyId;
    }

    /**
     * @return ProductId|null
     */
    public function getReductionProductId(): ?ProductId
    {
        return $this->reductionProductId;
    }

    /**
     * @return bool
     */
    public function isReductionExcludeSpecial(): bool
    {
        return $this->reductionExcludeSpecial;
    }

    /**
     * @return ProductId|null
     */
    public function getGiftProductId(): ?ProductId
    {
        return $this->giftProductId;
    }

    /**
     * @return CombinationId|null
     */
    public function getGiftCombinationId(): ?CombinationId
    {
        return $this->giftCombinationId;
    }

    /**
     * @return bool
     */
    public function isHighlight(): bool
    {
        return $this->highlight;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return DateTime|null
     */
    public function getDateAdd(): ?DateTime
    {
        return $this->dateAdd;
    }

    /**
     * @return DateTime|null
     */
    public function getDateUpd(): ?DateTime
    {
        return $this->dateUpd;
    }
}
