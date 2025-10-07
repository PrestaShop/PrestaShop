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
 * Defines the priority weights for different discount types
 *
 * Lower weight = higher priority in application order
 */
class DiscountTypeWeight
{
    /**
     * Product level discounts (catalog discounts) have highest priority
     */
    public const PRODUCT_LEVEL_WEIGHT = 1;

    /**
     * Free gift discounts should be applied before percentage discounts
     * to prevent percentage discounts from being applied to gift products
     */
    public const FREE_GIFT_WEIGHT = 2;

    /**
     * Cart level discounts have medium priority
     */
    public const CART_LEVEL_WEIGHT = 3;

    /**
     * Order level discounts have same priority as cart level
     */
    public const ORDER_LEVEL_WEIGHT = 3;

    /**
     * Free shipping discounts have lowest priority
     */
    public const FREE_SHIPPING_WEIGHT = 4;

    /**
     * Default weight for unknown or undefined types
     */
    public const DEFAULT_WEIGHT = 999;

    public static function getWeight(?string $discountType): int
    {
        if ($discountType === null) {
            return self::DEFAULT_WEIGHT;
        }

        $typeWeights = [
            DiscountType::PRODUCT_LEVEL => self::PRODUCT_LEVEL_WEIGHT,
            DiscountType::CART_LEVEL => self::CART_LEVEL_WEIGHT,
            DiscountType::ORDER_LEVEL => self::ORDER_LEVEL_WEIGHT,
            DiscountType::FREE_SHIPPING => self::FREE_SHIPPING_WEIGHT,
            DiscountType::FREE_GIFT => self::FREE_GIFT_WEIGHT,
        ];

        return $typeWeights[$discountType] ?? self::DEFAULT_WEIGHT;
    }
}
