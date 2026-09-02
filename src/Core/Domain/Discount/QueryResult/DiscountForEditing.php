<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Discount\ProductRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\Money;

class DiscountForEditing
{
    private ?Money $reductionAmount = null;
    private ?MinimumAmount $minimumAmount = null;

    public function __construct(
        private readonly int $discountId,
        private readonly array $localizedNames,
        private readonly int $priority,
        private readonly bool $active,
        private readonly DateTimeImmutable $validFrom,
        private readonly ?DateTimeImmutable $validTo,
        private readonly ?int $totalQuantity,
        private readonly ?int $remainingQuantity,
        private readonly int $quantityUsedInOrders,
        private readonly ?int $quantityPerUser,
        private readonly string $description,
        private readonly string $code,
        private readonly ?int $customerId,
        private readonly bool $highlightInCart,
        private readonly bool $allowPartialUse,
        private readonly string $type,
        private readonly ?DecimalNumber $reductionPercent,
        ?DecimalNumber $reductionAmount,
        ?int $reductionAmountCurrencyId,
        ?bool $reductionAmountTaxIncluded,
        private readonly bool $cheapestProduct,
        private readonly ?int $reductionProductId,
        private readonly ?int $giftProductId,
        private readonly ?int $giftCombinationId,
        private readonly int $minimumProductQuantity,
        private readonly array $productConditions,
        ?DecimalNumber $minimumAmount,
        ?int $minimumAmountCurrencyId,
        ?bool $minimumAmountTaxIncluded,
        ?bool $minimumAmountShippingIncluded,
        private readonly array $carrierIds,
        private readonly array $countryIds,
        private readonly array $customerGroupIds,
        private readonly array $compatibleDiscountTypeIds,
    ) {
        if ($reductionAmount !== null && $reductionAmountCurrencyId !== null && $reductionAmountTaxIncluded !== null) {
            $this->reductionAmount = new Money($reductionAmount, $reductionAmountCurrencyId, $reductionAmountTaxIncluded);
        }
        if ($minimumAmount !== null && $minimumAmountCurrencyId !== null && $minimumAmountTaxIncluded !== null && $minimumAmountShippingIncluded !== null) {
            $this->minimumAmount = new MinimumAmount($minimumAmount, $minimumAmountCurrencyId, $minimumAmountTaxIncluded, $minimumAmountShippingIncluded);
        }
    }

    public function getDiscountId(): int
    {
        return $this->discountId;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getValidFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getValidTo(): ?DateTimeImmutable
    {
        return $this->validTo;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->totalQuantity;
    }

    public function getRemainingQuantity(): ?int
    {
        return $this->remainingQuantity;
    }

    public function getQuantityUsedInOrders(): int
    {
        return $this->quantityUsedInOrders;
    }

    public function getQuantityPerUser(): ?int
    {
        return $this->quantityPerUser;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function isHighlightInCart(): bool
    {
        return $this->highlightInCart;
    }

    public function isAllowPartialUse(): bool
    {
        return $this->allowPartialUse;
    }

    public function getType(): DiscountType
    {
        return new DiscountType($this->type);
    }

    public function getReductionPercent(): ?DecimalNumber
    {
        return $this->reductionPercent;
    }

    public function getReductionAmount(): ?Money
    {
        return $this->reductionAmount;
    }

    public function getCheapestProduct(): bool
    {
        return $this->cheapestProduct;
    }

    public function getReductionProductId(): ?int
    {
        return $this->reductionProductId;
    }

    public function getGiftProductId(): ?int
    {
        return $this->giftProductId;
    }

    public function getGiftCombinationId(): ?int
    {
        return $this->giftCombinationId;
    }

    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getMinimumProductQuantity(): int
    {
        return $this->minimumProductQuantity;
    }

    /**
     * @return ProductRuleGroup[]
     */
    public function getProductConditions(): array
    {
        return $this->productConditions;
    }

    public function getMinimumAmount(): ?MinimumAmount
    {
        return $this->minimumAmount;
    }

    /**
     * @return int[]
     */
    public function getCarrierIds(): array
    {
        return $this->carrierIds;
    }

    /**
     * @return int[]
     */
    public function getCountryIds(): array
    {
        return $this->countryIds;
    }

    /**
     * @return int[]
     */
    public function getCustomerGroupIds(): array
    {
        return $this->customerGroupIds;
    }

    /**
     * @return int[]
     */
    public function getCompatibleDiscountTypeIds(): array
    {
        return $this->compatibleDiscountTypeIds;
    }
}
