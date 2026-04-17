<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Product objects that will be sent to the client.
 */
class ProductFilter extends HashMapWhitelistFilter
{
    public function __construct()
    {
        $whitelist = [
            'add_to_cart_url',
            'allow_oosp',
            'attributes',
            'attributes_small',
            'availability',
            'availability_date',
            'availability_message',
            'available_later',
            'available_now',
            'canonical_url',
            'cart_quantity',
            'category',
            'condition',
            'cover',
            'customizable',
            'customizations',
            'description_short',
            'discount_amount',
            'discount_percentage',
            'discount_percentage_absolute',
            'discount_to_display',
            'discount_type',
            'down_quantity_url',
            'ean13',
            'ecotax',
            'ecotax_attr',
            'ecotax_rate',
            'embedded_attributes' => new EmbeddedAttributesFilter(),
            'flags',
            'has_discount',
            'id',
            'id_customization',
            'id_image',
            'id_manufacturer',
            'id_product',
            'id_product_attribute',
            'images',
            'isbn',
            'labels',
            'legend',
            'link_rewrite',
            'main_variants',
            'manufacturer_name',
            'minimal_quantity',
            'name',
            'new',
            'on_sale',
            'online_only',
            'pack',
            'price',
            'price_amount',
            'price_attribute',
            'price_with_reduction',
            'price_with_reduction_without_tax',
            'price_without_reduction',
            'price_wt',
            'quantity',
            'quantity_discounts',
            'quantity_wanted',
            'rate',
            'reduction',
            'reference',
            'reference_to_display',
            'regular_price',
            'regular_price_amount',
            'remove_from_cart_url',
            'show_availability',
            'show_price',
            'specific_prices',
            'stock_quantity',
            'tax_name',
            'total',
            'total_wt',
            'unit_price',
            'unit_price_full',
            'up_quantity_url',
            'upc',
            'update_quantity_url',
            'url',
            'weight_unit',
            'seo_availability',
        ];

        $this->whitelist($whitelist);
    }
}
