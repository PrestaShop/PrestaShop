<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Command;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;

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
    private $minimumAmount;

    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @var bool
     */
    private $hasCountryRestriction;

    /**
     * @var bool
     */
    private $hasCarrierRestriction;

    /**
     * @var bool
     */
    private $hasGroupRestriction;

    /**
     * @var bool
     */
    private $hasCartRuleRestriction;

    /**
     * @var bool
     */
    private $hasProductRestriction;

    /**
     * @var bool
     */
    private $hasShopRestriction;

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
     *
     * @throws CartRuleConstraintException
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
        CartRuleActionInterface $cartRuleAction
    ) {
        $this->assertDateRangeIsValid($validFrom, $validTo);
        $this->setLocalizedNames($localizedNames);
        $this->setPriority($priority);
        $this->setTotalQuantity($totalQuantity);
        $this->setQuantityPerUser($quantityPerUser);
        $this->highlightInCart = $highlightInCart;
        $this->allowPartialUse = $allowPartialUse;
        $this->isActive = $isActive;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->cartRuleAction = $cartRuleAction;
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
    public function getMinimumAmount(): MoneyAmountCondition
    {
        return $this->minimumAmount;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
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
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param float $amount
     * @param int $currencyId
     * @param bool $taxExcluded
     */
    public function setMinimumAmount(float $amount, int $currencyId, bool $taxExcluded): void
    {
        $this->minimumAmount = new MoneyAmountCondition($amount, $currencyId, $taxExcluded);
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = new CustomerId($customerId);
    }

    /**
     * @param bool $hasCountryRestriction
     */
    public function setHasCountryRestriction(bool $hasCountryRestriction): void
    {
        $this->hasCountryRestriction = $hasCountryRestriction;
    }

    /**
     * @param bool $hasCarrierRestriction
     */
    public function setHasCarrierRestriction(bool $hasCarrierRestriction): void
    {
        $this->hasCarrierRestriction = $hasCarrierRestriction;
    }

    /**
     * @param bool $hasGroupRestriction
     */
    public function setHasGroupRestriction(bool $hasGroupRestriction): void
    {
        $this->hasGroupRestriction = $hasGroupRestriction;
    }

    /**
     * @param bool $hasCartRuleRestriction
     */
    public function setHasCartRuleRestriction(bool $hasCartRuleRestriction): void
    {
        $this->hasCartRuleRestriction = $hasCartRuleRestriction;
    }

    /**
     * @param bool $hasProductRestriction
     */
    public function setHasProductRestriction(bool $hasProductRestriction): void
    {
        $this->hasProductRestriction = $hasProductRestriction;
    }

    /**
     * @param bool $hasShopRestriction
     */
    public function setHasShopRestriction(bool $hasShopRestriction): void
    {
        $this->hasShopRestriction = $hasShopRestriction;
    }

    /**
     * @param array $localizedNames
     *
     * @throws CartRuleConstraintException
     */
    private function setLocalizedNames(array $localizedNames): void
    {
        $this->assertAtLeastOneNameIsPresent($localizedNames);

        foreach ($localizedNames as $languageId => $name) {
            $this->localizedNames[(new LanguageId($languageId))->getValue()] = $name;
        }
    }

    /**
     * @param int $priority
     *
     * @throws CartRuleConstraintException
     */
    private function setPriority(int $priority): void
    {
        if (0 >= $priority) {
            throw new CartRuleConstraintException(
                sprintf(
                    'Invalid cart rule priority "%s". Must be a positive integer.',
                    var_export($priority, true)
                ),
                CartRuleConstraintException::INVALID_PRIORITY
            );
        }

        $this->priority = $priority;
    }

    /**
     * @param int $quantity
     *
     * @throws CartRuleConstraintException
     */
    private function setTotalQuantity(int $quantity): void
    {
        if (0 > $quantity) {
            throw new CartRuleConstraintException(
                sprintf(
                   'Quantity cannot be lower than zero, %d given',
                    $quantity
                ),
                CartRuleConstraintException::INVALID_QUANTITY
            );
        }

        $this->totalQuantity = $quantity;
    }

    /**
     * @param int $quantity
     *
     * @throws CartRuleConstraintException
     */
    private function setQuantityPerUser(int $quantity): void
    {
        if (0 > $quantity) {
            throw new CartRuleConstraintException(
                sprintf(
                    'Quantity per user cannot be lower than zero, %d given',
                    $quantity
                ),
                CartRuleConstraintException::INVALID_QUANTITY_PER_USER
            );
        }

        $this->quantityPerUser = $quantity;
    }

    /**
     * @param array $names
     *
     * @throws CartRuleConstraintException
     */
    private function assertAtLeastOneNameIsPresent(array $names): void
    {
        if (empty($names)) {
            throw new CartRuleConstraintException(
                'Cart rule name is mandatory in at least one language',
                CartRuleConstraintException::EMPTY_NAME
            );
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
            throw new CartRuleConstraintException(
                'Date from cannot be greater than date to.',
                CartRuleConstraintException::DATE_FROM_GREATER_THAN_DATE_TO
            );
        }
    }
}
