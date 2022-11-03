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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Command;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

/**
 * Adds new cart rule
 */
class AddCartRuleCommand
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $code;

    /**
     * @var MoneyAmountCondition
     */
    private $minimumAmountCondition;

    /**
     * @var CustomerId|null
     */
    private $customerId;

    /**
     * @var bool
     */
    private $hasCountryRestriction = false;

    /**
     * @var bool
     */
    private $hasCarrierRestriction = false;

    /**
     * @var bool
     */
    private $hasGroupRestriction = false;

    /**
     * @var bool
     */
    private $hasCartRuleRestriction = false;

    /**
     * @var bool
     */
    private $hasProductRestriction = false;

    /**
     * @var bool
     */
    private $hasShopRestriction = false;

    /**
     * @var array
     */
    private $localizedNames;

    /**
     * @var bool
     */
    private $highlightInCart;

    /**
     * @var bool
     */
    private $allowPartialUse;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var DateTime
     */
    private $validFrom;

    /**
     * @var DateTime
     */
    private $validTo;

    /**
     * @var int
     */
    private $totalQuantity;

    /**
     * @var int
     */
    private $quantityPerUser;

    /**
     * @var CartRuleActionInterface
     */
    private $cartRuleAction;

    /**
     * Discount application type indicates what the discount should be applied to.
     * E.g. to whole order, to a specific product, to cheapest product.
     *
     * @var DiscountApplicationType|null
     */
    private $discountApplicationType;

    /**
     * This is the product to which discount is applied, when discount application type is "specific product".
     *
     * @var ProductId|null
     */
    private $discountProductId;

    /**
     * @param array $localizedNames
     * @param bool $highlightInCart
     * @param bool $allowPartialUse
     * @param int $priority
     * @param bool $isActive
     * @param DateTime $validFrom
     * @param DateTime $validTo
     * @param int $totalQuantity
     * @param int $quantityPerUser
     * @param CartRuleActionInterface $cartRuleAction
     * @param float $minimumAmount
     * @param int $minimumAmountCurrencyId
     * @param bool $isMinimumAmountTaxExcluded
     * @param bool $isMinimumAmountShippingExcluded
     *
     * @throws CartRuleConstraintException
     * @throws DomainConstraintException
     */
    public function __construct(
        array $localizedNames,
        bool $highlightInCart,
        bool $allowPartialUse,
        int $priority,
        bool $isActive,
        DateTime $validFrom,
        DateTime $validTo,
        int $totalQuantity,
        int $quantityPerUser,
        CartRuleActionInterface $cartRuleAction,
        float $minimumAmount,
        int $minimumAmountCurrencyId,
        bool $isMinimumAmountTaxExcluded,
        bool $isMinimumAmountShippingExcluded
    ) {
        $this->assertDateRangeIsValid($validFrom, $validTo);
        $this->setLocalizedNames($localizedNames);
        $this->setPriority($priority);
        $this->setTotalQuantity($totalQuantity);
        $this->setQuantityPerUser($quantityPerUser);
        $this->minimumAmountCondition = new MoneyAmountCondition(
            new Money(new DecimalNumber((string) $minimumAmount), new CurrencyId($minimumAmountCurrencyId)),
            $isMinimumAmountTaxExcluded,
            $isMinimumAmountShippingExcluded
        );
        $this->highlightInCart = $highlightInCart;
        $this->allowPartialUse = $allowPartialUse;
        $this->isActive = $isActive;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->cartRuleAction = $cartRuleAction;
    }

    /**
     * @return DiscountApplicationType|null
     */
    public function getDiscountApplicationType(): ?DiscountApplicationType
    {
        return $this->discountApplicationType;
    }

    /**
     * @param string $discountApplicationType
     *
     * @return AddCartRuleCommand
     *
     * @throws CartRuleConstraintException
     */
    public function setDiscountApplicationType(string $discountApplicationType): AddCartRuleCommand
    {
        $this->discountApplicationType = new DiscountApplicationType($discountApplicationType);

        return $this;
    }

    /**
     * @return ProductId|null
     */
    public function getDiscountProductId(): ?ProductId
    {
        return $this->discountProductId;
    }

    /**
     * @param int $discountProductId
     *
     * @return AddCartRuleCommand
     */
    public function setDiscountProductId(int $discountProductId): AddCartRuleCommand
    {
        $this->discountProductId = new ProductId($discountProductId);

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return MoneyAmountCondition
     */
    public function getMinimumAmountCondition(): MoneyAmountCondition
    {
        return $this->minimumAmountCondition;
    }

    /**
     * @return CustomerId|null
     */
    public function getCustomerId(): ?CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return bool
     */
    public function hasCountryRestriction(): bool
    {
        return $this->hasCountryRestriction;
    }

    /**
     * @return bool
     */
    public function hasCarrierRestriction(): bool
    {
        return $this->hasCarrierRestriction;
    }

    /**
     * @return bool
     */
    public function hasGroupRestriction(): bool
    {
        return $this->hasGroupRestriction;
    }

    /**
     * @return bool
     */
    public function hasCartRuleRestriction(): bool
    {
        return $this->hasCartRuleRestriction;
    }

    /**
     * @return bool
     */
    public function hasProductRestriction(): bool
    {
        return $this->hasProductRestriction;
    }

    /**
     * @return bool
     */
    public function hasShopRestriction(): bool
    {
        return $this->hasShopRestriction;
    }

    /**
     * @return array
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return bool
     */
    public function isHighlightInCart(): bool
    {
        return $this->highlightInCart;
    }

    /**
     * @return bool
     */
    public function isAllowPartialUse(): bool
    {
        return $this->allowPartialUse;
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
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return DateTime
     */
    public function getValidFrom(): DateTime
    {
        return $this->validFrom;
    }

    /**
     * @return DateTime
     */
    public function getValidTo(): DateTime
    {
        return $this->validTo;
    }

    /**
     * @return int
     */
    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    /**
     * @return int
     */
    public function getQuantityPerUser(): int
    {
        return $this->quantityPerUser;
    }

    /**
     * @return CartRuleActionInterface
     */
    public function getCartRuleAction(): CartRuleActionInterface
    {
        return $this->cartRuleAction;
    }

    /**
     * @param string $description
     *
     * @return AddCartRuleCommand
     */
    public function setDescription(string $description): AddCartRuleCommand
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $code
     *
     * @return AddCartRuleCommand
     */
    public function setCode(string $code): AddCartRuleCommand
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param int $customerId
     *
     * @return AddCartRuleCommand
     */
    public function setCustomerId(int $customerId): AddCartRuleCommand
    {
        $this->customerId = new CustomerId($customerId);

        return $this;
    }

    /**
     * @param bool $hasCountryRestriction
     *
     * @return AddCartRuleCommand
     */
    public function setHasCountryRestriction(bool $hasCountryRestriction): AddCartRuleCommand
    {
        $this->hasCountryRestriction = $hasCountryRestriction;

        return $this;
    }

    /**
     * @param bool $hasCarrierRestriction
     *
     * @return AddCartRuleCommand
     */
    public function setHasCarrierRestriction(bool $hasCarrierRestriction): AddCartRuleCommand
    {
        $this->hasCarrierRestriction = $hasCarrierRestriction;

        return $this;
    }

    /**
     * @param bool $hasGroupRestriction
     *
     * @return AddCartRuleCommand
     */
    public function setHasGroupRestriction(bool $hasGroupRestriction): AddCartRuleCommand
    {
        $this->hasGroupRestriction = $hasGroupRestriction;

        return $this;
    }

    /**
     * @param bool $hasCartRuleRestriction
     *
     * @return AddCartRuleCommand
     */
    public function setHasCartRuleRestriction(bool $hasCartRuleRestriction): AddCartRuleCommand
    {
        $this->hasCartRuleRestriction = $hasCartRuleRestriction;

        return $this;
    }

    /**
     * @param bool $hasProductRestriction
     *
     * @return AddCartRuleCommand
     */
    public function setHasProductRestriction(bool $hasProductRestriction): AddCartRuleCommand
    {
        $this->hasProductRestriction = $hasProductRestriction;

        return $this;
    }

    /**
     * @param bool $hasShopRestriction
     *
     * @return AddCartRuleCommand
     */
    public function setHasShopRestriction(bool $hasShopRestriction): AddCartRuleCommand
    {
        $this->hasShopRestriction = $hasShopRestriction;

        return $this;
    }

    /**
     * @param array $localizedNames
     *
     * @return AddCartRuleCommand
     *
     * @throws CartRuleConstraintException
     */
    private function setLocalizedNames(array $localizedNames): AddCartRuleCommand
    {
        $this->assertAtLeastOneNameIsPresent($localizedNames);

        foreach ($localizedNames as $languageId => $name) {
            $this->localizedNames[(new LanguageId($languageId))->getValue()] = $name;
        }

        return $this;
    }

    /**
     * @param int $priority
     *
     * @return AddCartRuleCommand
     *
     * @throws CartRuleConstraintException
     */
    private function setPriority(int $priority): AddCartRuleCommand
    {
        if (0 >= $priority) {
            throw new CartRuleConstraintException(sprintf('Invalid cart rule priority "%s". Must be a positive integer.', var_export($priority, true)), CartRuleConstraintException::INVALID_PRIORITY);
        }

        $this->priority = $priority;

        return $this;
    }

    /**
     * @param int $quantity
     *
     * @return AddCartRuleCommand
     *
     * @throws CartRuleConstraintException
     */
    private function setTotalQuantity(int $quantity): AddCartRuleCommand
    {
        if (0 > $quantity) {
            throw new CartRuleConstraintException(sprintf('Quantity cannot be lower than zero, %d given', $quantity), CartRuleConstraintException::INVALID_QUANTITY);
        }

        $this->totalQuantity = $quantity;

        return $this;
    }

    /**
     * @param int $quantity
     *
     * @return AddCartRuleCommand
     *
     * @throws CartRuleConstraintException
     */
    private function setQuantityPerUser(int $quantity): AddCartRuleCommand
    {
        if (0 > $quantity) {
            throw new CartRuleConstraintException(sprintf('Quantity per user cannot be lower than zero, %d given', $quantity), CartRuleConstraintException::INVALID_QUANTITY_PER_USER);
        }

        $this->quantityPerUser = $quantity;

        return $this;
    }

    /**
     * @param array $names
     *
     * @throws CartRuleConstraintException
     */
    private function assertAtLeastOneNameIsPresent(array $names): void
    {
        if (empty($names)) {
            throw new CartRuleConstraintException('Cart rule name is mandatory in at least one language', CartRuleConstraintException::EMPTY_NAME);
        }
    }

    /**
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     *
     * @throws CartRuleConstraintException
     */
    private function assertDateRangeIsValid(DateTime $dateFrom, DateTime $dateTo): void
    {
        if ($dateFrom > $dateTo) {
            throw new CartRuleConstraintException('Date from cannot be greater than date to.', CartRuleConstraintException::DATE_FROM_GREATER_THAN_DATE_TO);
        }
    }
}
