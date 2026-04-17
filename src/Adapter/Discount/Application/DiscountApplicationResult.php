<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Discount\Application;

/**
 * Result of discount application validation and priority ordering
 *
 * This is the single source of truth for which discounts should be applied to a cart
 * and in what order they should be applied.
 */
class DiscountApplicationResult
{
    /**
     * @param array<int> $discountsToApply List of discount IDs to apply, ordered by priority
     * @param array<int> $discountsToRemove List of discount IDs that should be removed from cart
     */
    public function __construct(
        private readonly bool $canApply,
        private readonly array $discountsToApply,
        private readonly array $discountsToRemove = [],
        private readonly ?string $rejectionReason = null
    ) {
    }

    /**
     * Check if the new discount can be applied to the cart
     */
    public function canApply(): bool
    {
        return $this->canApply;
    }

    /**
     * Get the ordered list of discount IDs that should be applied to the cart
     * The order reflects the priority of application (first = applied first)
     *
     * @return array<int>
     */
    public function getDiscountsToApply(): array
    {
        return $this->discountsToApply;
    }

    /**
     * Get the list of discount IDs that need to be removed from the cart
     *
     * @return array<int>
     */
    public function getDiscountsToRemove(): array
    {
        return $this->discountsToRemove;
    }

    /**
     * Get the reason why the discount was rejected (if applicable)
     */
    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    /**
     * Check if any discounts need to be removed
     */
    public function hasDiscountsToRemove(): bool
    {
        return !empty($this->discountsToRemove);
    }
}
