<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject;

/**
 * Defines the priority order for discount application
 *
 * Lower priority value = applied first
 * Priority order: 1. Catalog (product_level), 2. Cart (cart_level), 3. Shipping (free_shipping), 4. Free gift (free_gift)
 */
class DiscountPriority
{
    // Priority values - lower number = higher priority (applied first)
    public const PRODUCT_LEVEL_PRIORITY = 1; // Catalog discounts
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
            DiscountType::PRODUCT_LEVEL => self::PRODUCT_LEVEL_PRIORITY,
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
     * @param array $discount1 Discount data with 'discount_type', 'priority', 'date_add' keys
     * @param array $discount2 Discount data with 'discount_type', 'priority', 'date_add' keys
     */
    public static function compareDiscounts(array $discount1, array $discount2): int
    {
        // 1. Compare by type priority
        $typePriority1 = self::getPriorityForType($discount1['discount_type'] ?? '');
        $typePriority2 = self::getPriorityForType($discount2['discount_type'] ?? '');

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
