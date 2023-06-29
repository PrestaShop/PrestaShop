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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Command;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

/**
 * Adds new cart rule
 */
class AddCartRuleCommand
{
    /**
     * @var array<int, string>
     */
    private $localizedNames;

    /**
     * @var CartRuleAction
     */
    private $cartRuleAction;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var string
     */
    private $code = '';

    /**
     * @var Money|null
     */
    private $minimumAmount;

    /**
     * @var bool|null
     */
    private $minimumAmountShippingIncluded;

    /**
     * @var CustomerId|null
     */
    private $customerId;

    /**
     * @var bool
     */
    private $highlightInCart = false;

    /**
     * @var bool
     */
    private $allowPartialUse = true;

    /**
     * @var int
     */
    private $priority = 1;

    /**
     * @var bool
     */
    private $active = true;

    /**
     * @var DateTimeImmutable|null
     */
    private $validFrom;

    /**
     * @var DateTimeImmutable|null
     */
    private $validTo;

    /**
     * @var int
     */
    private $totalQuantity = 1;

    /**
     * @var int
     */
    private $quantityPerUser = 1;

    /**
     * @var ShopId[]
     */
    private array $associatedShopIds;

    /**
     * @param array<int, string> $localizedNames
     * @param CartRuleAction $cartRuleAction
     * @param int[] $associatedShopIds
     */
    public function __construct(
        array $localizedNames,
        CartRuleAction $cartRuleAction,
        array $associatedShopIds
    ) {
        $this->setLocalizedNames($localizedNames);
        $this->cartRuleAction = $cartRuleAction;
        $this->setAssociatedShopIds($associatedShopIds);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCustomerId(): ?CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return array<int, string>
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function isHighlightInCart(): bool
    {
        return $this->highlightInCart;
    }

    public function setHighlightInCart(bool $highlight): AddCartRuleCommand
    {
        $this->highlightInCart = $highlight;

        return $this;
    }

    public function allowPartialUse(): bool
    {
        return $this->allowPartialUse;
    }

    public function setAllowPartialUse(bool $allow): AddCartRuleCommand
    {
        $this->allowPartialUse = $allow;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return AddCartRuleCommand
     *
     * @throws CartRuleConstraintException
     */
    public function setPriority(int $priority): AddCartRuleCommand
    {
        if (0 >= $priority) {
            throw new CartRuleConstraintException(
                sprintf('Invalid cart rule priority "%s". Must be a positive integer.', $priority),
                CartRuleConstraintException::INVALID_PRIORITY
            );
        }

        $this->priority = $priority;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): AddCartRuleCommand
    {
        $this->active = $active;

        return $this;
    }

    public function getValidFrom(): ?DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getValidTo(): ?DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidityDateRange(DateTimeImmutable $from, DateTimeImmutable $to): AddCartRuleCommand
    {
        $this->assertDateRangeIsValid($from, $to);
        $this->validFrom = $from;
        $this->validTo = $to;

        return $this;
    }

    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(int $quantity): AddCartRuleCommand
    {
        if (0 > $quantity) {
            throw new CartRuleConstraintException(sprintf('Quantity cannot be lower than zero, %d given', $quantity), CartRuleConstraintException::INVALID_QUANTITY);
        }

        $this->totalQuantity = $quantity;

        return $this;
    }

    public function getQuantityPerUser(): int
    {
        return $this->quantityPerUser;
    }

    public function setQuantityPerUser(int $quantity): AddCartRuleCommand
    {
        if (0 > $quantity) {
            throw new CartRuleConstraintException(sprintf('Quantity per user cannot be lower than zero, %d given', $quantity), CartRuleConstraintException::INVALID_QUANTITY_PER_USER);
        }

        $this->quantityPerUser = $quantity;

        return $this;
    }

    public function getCartRuleAction(): CartRuleAction
    {
        return $this->cartRuleAction;
    }

    public function setDescription(string $description): AddCartRuleCommand
    {
        $this->description = $description;

        return $this;
    }

    public function setCode(string $code): AddCartRuleCommand
    {
        $this->code = $code;

        return $this;
    }

    public function setCustomerId(int $customerId): AddCartRuleCommand
    {
        $this->customerId = new CustomerId($customerId);

        return $this;
    }

    public function setMinimumAmount(
        string $minimumAmount,
        int $currencyId,
        bool $taxIncluded,
        bool $shippingIncluded
    ): AddCartRuleCommand {
        $this->minimumAmount = new Money(
            new DecimalNumber($minimumAmount),
            new CurrencyId($currencyId),
            $taxIncluded
        );
        $this->minimumAmountShippingIncluded = $shippingIncluded;

        return $this;
    }

    public function getMinimumAmount(): ?Money
    {
        return $this->minimumAmount;
    }

    public function isMinimumAmountShippingIncluded(): ?bool
    {
        return $this->minimumAmountShippingIncluded;
    }

    /**
     * @return ShopId[]
     */
    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    /**
     * @param array<int, string> $localizedNames
     *
     * @return AddCartRuleCommand
     */
    private function setLocalizedNames(array $localizedNames): AddCartRuleCommand
    {
        foreach ($localizedNames as $languageId => $name) {
            $this->localizedNames[(new LanguageId($languageId))->getValue()] = $name;
        }

        return $this;
    }

    private function assertDateRangeIsValid(DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo): void
    {
        if ($dateFrom > $dateTo) {
            throw new CartRuleConstraintException('Date from cannot be greater than date to.', CartRuleConstraintException::DATE_FROM_GREATER_THAN_DATE_TO);
        }
    }

    /**
     * @param int[] $associatedShopIds
     */
    private function setAssociatedShopIds(array $associatedShopIds): void
    {
        if (empty($associatedShopIds)) {
            throw new CartRuleConstraintException('Shop association cannot be empty', CartRuleConstraintException::INVALID_SHOP_ASSOCIATION);
        }

        $this->associatedShopIds = array_map(static function (int $shopId): ShopId {
            return new ShopId($shopId);
        }, $associatedShopIds);
    }
}
