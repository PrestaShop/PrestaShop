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

namespace PrestaShop\PrestaShop\Adapter\Discount\Compatibility;

use CartRule;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;

/**
 * Service for validating discount compatibility
 */
class DiscountCompatibilityService
{
    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository
    ) {
    }

    /**
     * Check if a new discount is compatible with existing discounts in the cart
     *
     * @param int $newDiscountId The ID of the discount to be added
     * @param array $existingDiscountIds Array of existing discount IDs in the cart
     *
     * @return DiscountCompatibilityResult
     */
    public function validateCompatibility(int $newDiscountId, array $existingDiscountIds): DiscountCompatibilityResult
    {
        $conflictingDiscounts = [];
        $canApply = true;

        // Load the new discount to check its priority and type
        $newDiscount = new CartRule($newDiscountId);
        if (!$newDiscount->id) {
            return new DiscountCompatibilityResult(false, []);
        }

        foreach ($existingDiscountIds as $existingDiscountId) {
            $existingDiscount = new CartRule($existingDiscountId);
            if (!$existingDiscount->id) {
                continue;
            }

            // Check type compatibility in both directions
            $areTypesCompatible = $this->discountTypeRepository->areDiscountsCompatible($newDiscountId, $existingDiscountId)
                && $this->discountTypeRepository->areDiscountsCompatible($existingDiscountId, $newDiscountId);

            if (!$areTypesCompatible) {
                // Types are incompatible
                // Check if the new discount should replace the existing one by priority
                if ($this->shouldReplaceByPriority($newDiscount, $existingDiscount)) {
                    // Higher priority same-type discount can replace lower priority one
                    $conflictingDiscounts[] = $existingDiscountId;
                } else {
                    // Lower or equal priority - cannot add
                    $canApply = false;
                }
                continue;
            }

            // Types are compatible - check if priority-based replacement should still happen
            // (for same-type discounts that are made explicitly compatible)
            if ($this->shouldReplaceByPriority($newDiscount, $existingDiscount)) {
                $conflictingDiscounts[] = $existingDiscountId;
            }
        }

        return new DiscountCompatibilityResult(
            $canApply,
            $conflictingDiscounts
        );
    }

    private function shouldReplaceByPriority(CartRule $newDiscount, CartRule $existingDiscount): bool
    {
        // Get types of both discounts
        $newType = $newDiscount->getType();
        $existingType = $existingDiscount->getType();

        // If both have no type defined, don't replace based on priority
        if ($newType === null && $existingType === null) {
            return false;
        }

        // If types are different, don't replace based on priority
        // (they can coexist if type-compatible)
        if ($newType !== $existingType) {
            return false;
        }

        // Both are the same type - check priority
        // Lower priority number = higher priority
        return $newDiscount->priority < $existingDiscount->priority;
    }
}
