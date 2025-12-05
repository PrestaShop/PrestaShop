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

namespace PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject;

/**
 * Defines the priority order for discount application
 *
 * Lower priority value = applied first
 * Priority order: 1. Catalog (catalog_level), 2. Cart (cart_level), 3. Shipping (free_shipping), 4. Free gift (free_gift)
 */
class DiscountPriority
{
    // Priority values - lower number = higher priority (applied first)
    public const CATALOG_LEVEL_PRIORITY = 1; // Catalog discounts
    public const CART_LEVEL_PRIORITY = 2;    // Cart discounts
    public const ORDER_LEVEL_PRIORITY = 2;   // Order discounts (same as cart)
    public const FREE_SHIPPING_PRIORITY = 3; // Shipping discounts
    public const FREE_GIFT_PRIORITY = 4;     // Free gift discounts

    /**
     * Get priority value for a discount type
     */
    public static function getPriorityForType(string $discountType): int
    {
        return match ($discountType) {
            DiscountType::CATALOG_LEVEL => self::CATALOG_LEVEL_PRIORITY,
            DiscountType::CART_LEVEL => self::CART_LEVEL_PRIORITY,
            DiscountType::ORDER_LEVEL => self::ORDER_LEVEL_PRIORITY,
            DiscountType::FREE_SHIPPING => self::FREE_SHIPPING_PRIORITY,
            DiscountType::FREE_GIFT => self::FREE_GIFT_PRIORITY,
            default => 999, // Unknown types go last
        };
    }

    /**
     * Compare two discount types and return which has higher priority
     */
    public static function compare(string $type1, string $type2): int
    {
        return self::getPriorityForType($type1) <=> self::getPriorityForType($type2);
    }

    /**
     * Compare two discounts using full priority logic:
     * 1. Type priority (product > cart > shipping > gift)
     * 2. Priority field (lower number = higher priority)
     * 3. Creation date (older = higher priority)
     *
     * @param array $discount1 Discount data with 'type', 'priority', 'date_add' keys
     * @param array $discount2 Discount data with 'type', 'priority', 'date_add' keys
     */
    public static function compareDiscounts(array $discount1, array $discount2): int
    {
        // 1. Compare by type priority
        $typePriority1 = self::getPriorityForType($discount1['type'] ?? '');
        $typePriority2 = self::getPriorityForType($discount2['type'] ?? '');

        if ($typePriority1 !== $typePriority2) {
            return $typePriority1 <=> $typePriority2;
        }

        // 2. Compare by priority field (lower number = higher priority)
        $priority1 = $discount1['priority'] ?? 1;
        $priority2 = $discount2['priority'] ?? 1;

        if ($priority1 !== $priority2) {
            return $priority1 <=> $priority2;
        }

        // 3. Compare by creation date (older = higher priority)
        $date1 = $discount1['date_add'] ?? '';
        $date2 = $discount2['date_add'] ?? '';

        return strcmp($date1, $date2);
    }

    /**
     * Sort an array of discounts by their full priority (type, priority field, date)
     *
     * @param array $discounts Array of discount data with 'type', 'priority', 'date_add' keys
     *
     * @return array Sorted array of discounts
     */
    public static function sortByPriority(array $discounts): array
    {
        usort($discounts, function ($a, $b) {
            return self::compareDiscounts($a, $b);
        });

        return $discounts;
    }
}
