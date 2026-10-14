<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject;

class DiscountType
{
    public const CART_LEVEL = 'cart_level';
    public const PRODUCT_LEVEL = 'product_level';
    public const FREE_GIFT = 'free_gift';
    public const FREE_SHIPPING = 'free_shipping';
    public const ORDER_LEVEL = 'order_level';

    /**
     * Display label of each core type, in the Admin.Catalog.Feature domain.
     *
     * The name stored in cart_rule_type_lang cannot be used for these: installing a language copies
     * the rows of the default one, and no per-language seed overwrites them, so a core type reads
     * back in English whatever the employee's language is. Module-provided types are not listed here
     * and keep their stored name, which is the only source there is for them.
     */
    public const CORE_LABELS = [
        self::CART_LEVEL => 'On cart amount',
        self::PRODUCT_LEVEL => 'On catalog products',
        self::FREE_GIFT => 'On free gift',
        self::FREE_SHIPPING => 'On free shipping',
        self::ORDER_LEVEL => 'On total order',
    ];

    public function __construct(private readonly string $value)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
