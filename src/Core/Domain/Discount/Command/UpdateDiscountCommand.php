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

namespace PrestaShop\PrestaShop\Core\Domain\Discount\Command;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\NoCustomerId;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\MinimumAmount;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Trait\DirtyTrait;

class UpdateDiscountCommand
{
    use DirtyTrait;

    private DiscountId $discountId;
    private ?array $localizedNames = null;
    private ?int $priority = null;
    private ?bool $active = null;
    private ?DateTimeImmutable $validFrom = null;
    private ?DateTimeImmutable $validTo = null;
    private ?int $totalQuantity = null;
    private ?int $quantityPerUser = null;
    private ?string $description = null;
    private ?string $code = null;
    private ?CustomerIdInterface $customerId = null;
    private ?bool $highlightInCart = null;
    private ?bool $allowPartialUse = null;
    private ?DecimalNumber $reductionPercent = null;
    private ?Money $reductionAmount = null;
    private ?ProductId $giftProductId = null;
    private ?CombinationId $giftCombinationId = null;
    private ?bool $cheapestProduct = null;
    private ?ProductId $reductionProductId = null;
    private ?int $minimumProductQuantity = null;
    /**
     * @var ProductRuleGroup[]|null
     */
    private ?array $productConditions = null;

    private ?MinimumAmount $minimumAmount = null;

    /**
     * @var CarrierId[]|null
     */
    private ?array $carrierIds = null;

    /**
     * @var CountryId[]|null
     */
    private ?array $countryIds = null;

    /**
     * @var GroupId[]|null
     */
    private ?array $customerGroupIds = null;
    /**
     * @var int[]|null
     */
    private ?array $compatibleDiscountTypeIds = null;

    public function __construct(int $discountId)
    {
        $this->discountId = new DiscountId($discountId);
    }

    public function getDiscountId(): DiscountId
    {
        return $this->discountId;
    }

    /**
     * @param array<int, string> $localizedNames
     */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;
        $this->markDirty('localizedNames');

        return $this;
    }

    /**
     * @return array<int, string>|null
     */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        $this->markDirty('active');

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

    public function setValidFrom(DateTimeImmutable $validFrom): self
    {
        $this->validFrom = $validFrom;
        $this->markDirty('validFrom');

        return $this;
    }

    public function setValidTo(DateTimeImmutable $validTo): self
    {
        $this->validTo = $validTo;
        $this->markDirty('validTo');

        return $this;
    }

    /**
     * @throws DiscountConstraintException
     */
    public function setValidityDateRange(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        if ($from > $to) {
            throw new DiscountConstraintException('Date from cannot be greater than date to.', DiscountConstraintException::DATE_FROM_GREATER_THAN_DATE_TO);
        }

        $this->validFrom = $from;
        $this->validTo = $to;
        $this->markDirty('validFrom');
        $this->markDirty('validTo');

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * @throws DiscountConstraintException
     */
    public function setPriority(int $priority): self
    {
        if (0 >= $priority) {
            throw new DiscountConstraintException(
                sprintf('Invalid discount priority "%s". Must be a positive integer.', $priority),
                DiscountConstraintException::INVALID_PRIORITY
            );
        }

        $this->priority = $priority;
        $this->markDirty('priority');

        return $this;
    }

    public function isHighlightInCart(): ?bool
    {
        return $this->highlightInCart;
    }

    public function setHighlightInCart(bool $highlightInCart): void
    {
        $this->highlightInCart = $highlightInCart;
        $this->markDirty('highlightInCart');
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    /**
     * @throws DiscountConstraintException
     */
    public function setTotalQuantity(?int $quantity): self
    {
        if ($quantity !== null && 0 > $quantity) {
            throw new DiscountConstraintException(sprintf('Quantity cannot be lower than zero, %d given', $quantity), DiscountConstraintException::INVALID_QUANTITY);
        }

        $this->totalQuantity = $quantity;
        $this->markDirty('totalQuantity');

        return $this;
    }

    public function getQuantityPerUser(): ?int
    {
        return $this->quantityPerUser;
    }

    /**
     * @throws DiscountConstraintException
     */
    public function setQuantityPerUser(?int $quantityPerUser): self
    {
        if ($quantityPerUser !== null && 0 > $quantityPerUser) {
            throw new DiscountConstraintException(sprintf('Quantity per user cannot be lower than zero, %d given', $quantityPerUser), DiscountConstraintException::INVALID_QUANTITY_PER_USER);
        }

        $this->quantityPerUser = $quantityPerUser;
        $this->markDirty('quantityPerUser');

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        $this->markDirty('description');

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        $this->markDirty('code');

        return $this;
    }

    public function allowPartialUse(): ?bool
    {
        return $this->allowPartialUse;
    }

    public function setAllowPartialUse(bool $allow): self
    {
        $this->allowPartialUse = $allow;
        $this->markDirty('allowPartialUse');

        return $this;
    }

    public function getCustomerId(): ?CustomerIdInterface
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId === NoCustomerId::NO_CUSTOMER_ID_VALUE ? new NoCustomerId() : new CustomerId($customerId);
        $this->markDirty('customerId');

        return $this;
    }

    public function getReductionPercent(): ?DecimalNumber
    {
        return $this->reductionPercent;
    }

    public function setReductionPercent(DecimalNumber $reductionPercent): self
    {
        $this->reductionPercent = $reductionPercent;
        $this->markDirty('reductionPercent');

        return $this;
    }

    public function getReductionAmount(): ?Money
    {
        return $this->reductionAmount;
    }

    /**
     * Note: the parameters names are important here for API serialization.
     */
    public function setReductionAmount(DecimalNumber $amount, int $currencyId, bool $taxIncluded): self
    {
        if ($amount->isLowerThanZero()) {
            throw new DiscountConstraintException(sprintf('Money amount cannot be lower than zero, %s given', $amount), DiscountConstraintException::INVALID_DISCOUNT_VALUE_CANNOT_BE_NEGATIVE);
        }

        $this->reductionAmount = new Money($amount, new CurrencyId($currencyId), $taxIncluded);
        $this->markDirty('reductionAmount');

        return $this;
    }

    public function getGiftProductId(): ?ProductId
    {
        return $this->giftProductId;
    }

    /**
     * @throws ProductConstraintException
     */
    public function setGiftProductId(?int $giftProductId): self
    {
        $this->giftProductId = null !== $giftProductId ? new ProductId($giftProductId) : null;
        $this->markDirty('giftProductId');

        return $this;
    }

    public function getGiftCombinationId(): ?CombinationId
    {
        return $this->giftCombinationId;
    }

    /**
     * @throws CombinationConstraintException
     */
    public function setGiftCombinationId(?int $giftCombinationId): self
    {
        $this->giftCombinationId = null !== $giftCombinationId ? new CombinationId($giftCombinationId) : null;
        $this->markDirty('giftCombinationId');

        return $this;
    }

    public function getCheapestProduct(): ?bool
    {
        return $this->cheapestProduct;
    }

    public function setCheapestProduct(bool $cheapestProduct): self
    {
        $this->cheapestProduct = $cheapestProduct;
        $this->markDirty('cheapestProduct');

        return $this;
    }

    public function getReductionProductId(): ?ProductId
    {
        return $this->reductionProductId;
    }

    /**
     * @throws ProductConstraintException
     */
    public function setReductionProductId(?int $reductionProductId): self
    {
        $this->reductionProductId = null !== $reductionProductId ? new ProductId($reductionProductId) : null;
        $this->markDirty('reductionProductId');

        return $this;
    }

    public function getMinimumProductQuantity(): ?int
    {
        return $this->minimumProductQuantity;
    }

    public function setMinimumProductQuantity(int $minimumProductQuantity): self
    {
        if ($minimumProductQuantity < 0) {
            throw new DiscountConstraintException('Minimum products quantity must be greater than 0', DiscountConstraintException::INVALID_MINIMUM_PRODUCT_QUANTITY);
        }
        $this->minimumProductQuantity = $minimumProductQuantity;
        $this->markDirty('minimumProductQuantity');

        return $this;
    }

    public function getMinimumAmount(): ?MinimumAmount
    {
        return $this->minimumAmount;
    }

    /**
     * Note: the parameters names are important here for API serialization.
     * To unset the minimum amount set the first parameter to null (the other parameters can remain empty)
     */
    public function setMinimumAmount(?DecimalNumber $amount, int $currencyId = 0, bool $taxIncluded = true, bool $shippingIncluded = true): self
    {
        if (null === $amount) {
            $this->minimumAmount = null;
        } else {
            if ($amount->isLowerThanZero()) {
                throw new DiscountConstraintException(sprintf('Money amount cannot be lower than zero, %s given', $amount), DiscountConstraintException::INVALID_DISCOUNT_VALUE_CANNOT_BE_NEGATIVE);
            }

            $this->minimumAmount = new MinimumAmount($amount, new CurrencyId($currencyId), $taxIncluded, $shippingIncluded);
        }
        $this->markDirty('minimumAmount');

        return $this;
    }

    /**
     * @return ProductRuleGroup[]|null
     */
    public function getProductConditions(): ?array
    {
        return $this->productConditions;
    }

    /**
     * @param ProductRuleGroup[]|array<int, array{quantity: int, rules: array<int, array{type: string, itemIds: int[]}>, type: string}> $productConditions
     *
     * @return self
     *
     * @throws DiscountConstraintException
     */
    public function setProductConditions(array $productConditions): self
    {
        $validProductConditions = [];
        foreach ($productConditions as $productCondition) {
            if (is_array($productCondition)) {
                $productCondition = ProductRuleGroup::fromArray($productCondition);
            }

            if (!$productCondition instanceof ProductRuleGroup) {
                throw new DiscountConstraintException(sprintf('Product conditions must be an array of %s', ProductRuleGroup::class), DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
            }
            if ($productCondition->getQuantity() <= 0) {
                throw new DiscountConstraintException('Product conditions quantity must be strictly positive', DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
            }
            if (empty($productCondition->getRules())) {
                throw new DiscountConstraintException('Product conditions rules cannot be empty', DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
            }

            foreach ($productCondition->getRules() as $rule) {
                if (empty($rule->getItemIds())) {
                    throw new DiscountConstraintException('Product conditions rule items cannot be empty', DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
                }

                foreach ($rule->getItemIds() as $itemId) {
                    if (!is_int($itemId)) {
                        throw new DiscountConstraintException('Product conditions rule item ID must be an integer', DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
                    }
                    if ((int) $itemId <= 0) {
                        throw new DiscountConstraintException('Product conditions rule item ID must be strictly positive', DiscountConstraintException::INVALID_PRODUCTS_CONDITIONS);
                    }
                }
            }
            $validProductConditions[] = $productCondition;
        }
        $this->productConditions = $validProductConditions;
        $this->markDirty('productConditions');

        return $this;
    }

    /**
     * @return CarrierId[]|null
     */
    public function getCarrierIds(): ?array
    {
        return $this->carrierIds;
    }

    /**
     * @param int[]|null $carrierIds
     *
     * @return $this
     */
    public function setCarrierIds(?array $carrierIds): self
    {
        $this->carrierIds = $carrierIds !== null ? array_map(fn (int $carrierId) => new CarrierId($carrierId), $carrierIds) : null;
        $this->markDirty('carrierIds');

        return $this;
    }

    public function getCountryIds(): ?array
    {
        return $this->countryIds;
    }

    public function setCountryIds(?array $countryIds): self
    {
        $this->countryIds = $countryIds !== null ? array_map(fn (int $countryId) => new CountryId($countryId), $countryIds) : null;
        $this->markDirty('countryIds');

        return $this;
    }

    /**
     * @return GroupId[]|null
     */
    public function getCustomerGroupIds(): ?array
    {
        return $this->customerGroupIds;
    }

    /**
     * @return $this
     */
    public function setCustomerGroupIds(?array $customerGroupIds): self
    {
        $this->customerGroupIds = $customerGroupIds !== null ? array_map(fn (int $groupId) => new GroupId($groupId), $customerGroupIds) : null;
        $this->markDirty('customerGroupIds');

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getCompatibleDiscountTypeIds(): ?array
    {
        return $this->compatibleDiscountTypeIds;
    }

    /**
     * @param int[]|null $compatibleDiscountTypeIds
     */
    public function setCompatibleDiscountTypeIds(?array $compatibleDiscountTypeIds): self
    {
        foreach ($compatibleDiscountTypeIds as $compatibleDiscountTypeId) {
            if (!is_int($compatibleDiscountTypeId) || $compatibleDiscountTypeId <= 0) {
                throw new DiscountConstraintException('Compatible discount type ID must be positive integer', DiscountConstraintException::INVALID_COMPATIBLE_TYPE_IDS);
            }
        }
        $uniqueValues = array_unique($compatibleDiscountTypeIds);
        if ($uniqueValues !== $compatibleDiscountTypeIds) {
            throw new DiscountConstraintException('Provided compatible discount type ID must be unique', DiscountConstraintException::INVALID_COMPATIBLE_TYPE_IDS);
        }

        $this->compatibleDiscountTypeIds = $compatibleDiscountTypeIds;
        $this->markDirty('compatibleDiscountTypeIds');

        return $this;
    }
}
