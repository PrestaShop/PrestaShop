<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Discount\Application;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountPriority;

/**
 * Service for determining which discounts should be applied to a cart and in what order
 *
 * This is the single source of truth for discount application logic.
 * It handles:
 * - Compatibility checking between discounts
 * - Priority-based ordering of discounts
 * - Resolving conflicts when incompatible discounts are added
 */
class DiscountApplicationService
{
    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository
    ) {
    }

    /**
     * Determine which discounts should be applied when adding a new discount to the cart
     *
     * @param array<int> $existingDiscountIds Array of discount IDs currently in the cart
     */
    public function determineDiscountsToApply(int $newDiscountId, array $existingDiscountIds): DiscountApplicationResult
    {
        // If cart is empty, simply add the new discount
        if (empty($existingDiscountIds)) {
            return new DiscountApplicationResult(
                canApply: true,
                discountsToApply: [$newDiscountId],
                discountsToRemove: []
            );
        }

        // Get full discount information (type, priority, date_add) for all discounts
        $newDiscountInfo = $this->getDiscountInfo($newDiscountId);
        if (!$newDiscountInfo) {
            // If discount not found, reject it
            return new DiscountApplicationResult(
                canApply: false,
                discountsToApply: $existingDiscountIds,
                discountsToRemove: [],
                rejectionReason: 'Discount not found'
            );
        }

        $existingDiscountsWithInfo = $this->getDiscountsWithInfo($existingDiscountIds);

        // Check compatibility between new discount and existing ones
        $compatibilityResult = $this->checkAllCompatibilities(
            $newDiscountId,
            $existingDiscountsWithInfo
        );

        if (!$compatibilityResult['isCompatible']) {
            // Incompatible discounts found - resolve by priority
            return $this->resolveByPriority(
                $newDiscountInfo,
                $existingDiscountsWithInfo,
                $compatibilityResult['incompatibleDiscounts']
            );
        }

        // All discounts are compatible - return them all, sorted by priority
        $allDiscounts = array_merge(
            [$newDiscountInfo],
            $existingDiscountsWithInfo
        );

        $sortedDiscounts = $this->sortDiscountsByPriority($allDiscounts);

        return new DiscountApplicationResult(
            canApply: true,
            discountsToApply: array_column($sortedDiscounts, 'id'),
            discountsToRemove: []
        );
    }

    /**
     * Check bidirectional compatibility between new discount and all existing discounts
     *
     * @param array $existingDiscountsWithTypes
     *
     * @return array{isCompatible: bool, incompatibleDiscounts: array}
     */
    private function checkAllCompatibilities(
        int $newDiscountId,
        array $existingDiscountsWithTypes
    ): array {
        $incompatibleDiscounts = [];

        foreach ($existingDiscountsWithTypes as $existingDiscount) {
            $existingId = $existingDiscount['id'];

            // Check bidirectional compatibility
            $newToExisting = $this->discountTypeRepository->areDiscountsCompatible($newDiscountId, $existingId);
            $existingToNew = $this->discountTypeRepository->areDiscountsCompatible($existingId, $newDiscountId);

            if (!$newToExisting || !$existingToNew) {
                $incompatibleDiscounts[] = $existingDiscount;
            }
        }

        return [
            'isCompatible' => empty($incompatibleDiscounts),
            'incompatibleDiscounts' => $incompatibleDiscounts,
        ];
    }

    /**
     * Resolve conflicts by priority when incompatible discounts are found
     *
     * Higher priority discount wins and incompatible lower priority discounts are removed
     * Priority is determined by: 1) Type, 2) Priority field, 3) Creation date
     *
     * @param array $newDiscountInfo Full discount info with type, priority, date_add
     * @param array $existingDiscountsWithTypes
     * @param array $incompatibleDiscounts
     *
     * @return DiscountApplicationResult
     */
    private function resolveByPriority(
        array $newDiscountInfo,
        array $existingDiscountsWithTypes,
        array $incompatibleDiscounts
    ): DiscountApplicationResult {
        $discountsToRemove = [];
        $discountsToKeep = [];

        // Check each incompatible discount using full priority comparison
        foreach ($incompatibleDiscounts as $incompatibleDiscount) {
            $comparisonResult = DiscountPriority::compareDiscounts($newDiscountInfo, $incompatibleDiscount);

            if ($comparisonResult < 0) {
                // New discount has higher priority - remove the existing one
                $discountsToRemove[] = $incompatibleDiscount['id'];
            } elseif ($comparisonResult > 0) {
                // Existing discount has higher priority - reject the new one
                return new DiscountApplicationResult(
                    canApply: false,
                    discountsToApply: array_column($existingDiscountsWithTypes, 'id'),
                    discountsToRemove: [],
                    rejectionReason: 'A higher priority incompatible discount is already applied to the cart'
                );
            } else {
                // Exact same priority (extremely rare) - reject the new one (first come, first served)
                return new DiscountApplicationResult(
                    canApply: false,
                    discountsToApply: array_column($existingDiscountsWithTypes, 'id'),
                    discountsToRemove: [],
                    rejectionReason: 'An incompatible discount with identical priority is already applied to the cart'
                );
            }
        }

        // Keep compatible discounts
        foreach ($existingDiscountsWithTypes as $existingDiscount) {
            if (!in_array($existingDiscount['id'], $discountsToRemove)) {
                $discountsToKeep[] = $existingDiscount;
            }
        }

        // Add new discount and sort all by priority
        $allDiscountsToApply = array_merge(
            [$newDiscountInfo],
            $discountsToKeep
        );

        $sortedDiscounts = $this->sortDiscountsByPriority($allDiscountsToApply);

        return new DiscountApplicationResult(
            canApply: true,
            discountsToApply: array_column($sortedDiscounts, 'id'),
            discountsToRemove: $discountsToRemove
        );
    }

    /**
     * Get full discount information including type, priority field, and creation date
     *
     * @return array|null Array with keys: 'id', 'discount_type', 'priority', 'date_add'
     */
    private function getDiscountInfo(int $discountId): ?array
    {
        return $this->discountTypeRepository->getDiscountInfoForPriority($discountId);
    }

    /**
     * Get full discount information for multiple discounts
     *
     * @param array<int> $discountIds
     *
     * @return array Array of discount info arrays with keys: 'id', 'discount_type', 'priority', 'date_add'
     */
    private function getDiscountsWithInfo(array $discountIds): array
    {
        $discountsWithInfo = [];

        foreach ($discountIds as $discountId) {
            $info = $this->getDiscountInfo($discountId);
            if ($info) {
                $discountsWithInfo[] = $info;
            }
        }

        return $discountsWithInfo;
    }

    /**
     * Sort discounts by their full priority (type, priority field, creation date)
     *
     * @param array $discounts Array of discount data with 'discount_type', 'priority', 'date_add' keys
     *
     * @return array Sorted array of discounts
     */
    private function sortDiscountsByPriority(array $discounts): array
    {
        return DiscountPriority::sortByPriority($discounts);
    }
}
