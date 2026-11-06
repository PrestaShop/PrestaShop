<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount;

use CartRule;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

/**
 * Cart rules created before the discount types were introduced (shops upgraded from a previous version, cart rules
 * created with the legacy page or by modules) have no id_cart_rule_type. This class deduces the closest discount
 * type from their legacy properties, and lists the legacy settings that this type cannot represent.
 */
class LegacyDiscountTypeResolver
{
    /** The cart rule also offers free shipping, which is not part of the deduced type */
    public const UNSUPPORTED_FREE_SHIPPING = 'free_shipping';

    /** The cart rule also offers a free gift, which is not part of the deduced type */
    public const UNSUPPORTED_FREE_GIFT = 'free_gift';

    /** The cart rule targets the cheapest product, a target the discount form does not offer */
    public const UNSUPPORTED_CHEAPEST_PRODUCT = 'cheapest_product';

    /** The cart rule targets a product segment, computed differently for a product level discount */
    public const UNSUPPORTED_PRODUCT_SEGMENT = 'product_segment';

    /** The cart rule relies on the legacy per cart rule compatibility (cart_rule_combination) */
    public const UNSUPPORTED_CART_RULE_RESTRICTION = 'cart_rule_restriction';

    public function resolveType(CartRule $cartRule): string
    {
        // Order level is never deduced: it also applies on shipping fees, which legacy cart rules never did
        if ($this->hasReduction($cartRule)) {
            return (int) $cartRule->reduction_product !== 0 ? DiscountType::PRODUCT_LEVEL : DiscountType::CART_LEVEL;
        }

        if ((int) $cartRule->gift_product > 0) {
            return DiscountType::FREE_GIFT;
        }

        if ((bool) $cartRule->free_shipping) {
            return DiscountType::FREE_SHIPPING;
        }

        return DiscountType::CART_LEVEL;
    }

    /**
     * @return string[] List of UNSUPPORTED_* constants
     */
    public function getUnsupportedSettings(CartRule $cartRule, string $discountType): array
    {
        $unsupportedSettings = [];
        if ((bool) $cartRule->free_shipping && $discountType !== DiscountType::FREE_SHIPPING) {
            $unsupportedSettings[] = self::UNSUPPORTED_FREE_SHIPPING;
        }
        if ((int) $cartRule->gift_product > 0 && $discountType !== DiscountType::FREE_GIFT) {
            $unsupportedSettings[] = self::UNSUPPORTED_FREE_GIFT;
        }
        if ($discountType === DiscountType::PRODUCT_LEVEL && (int) $cartRule->reduction_product === DiscountSettings::CHEAPEST_PRODUCT) {
            $unsupportedSettings[] = self::UNSUPPORTED_CHEAPEST_PRODUCT;
        }
        if ($discountType === DiscountType::PRODUCT_LEVEL && (int) $cartRule->reduction_product === DiscountSettings::PRODUCT_SEGMENT) {
            $unsupportedSettings[] = self::UNSUPPORTED_PRODUCT_SEGMENT;
        }
        if ((bool) $cartRule->cart_rule_restriction) {
            $unsupportedSettings[] = self::UNSUPPORTED_CART_RULE_RESTRICTION;
        }

        return $unsupportedSettings;
    }

    private function hasReduction(CartRule $cartRule): bool
    {
        return (float) $cartRule->reduction_percent > 0 || (float) $cartRule->reduction_amount > 0;
    }
}
