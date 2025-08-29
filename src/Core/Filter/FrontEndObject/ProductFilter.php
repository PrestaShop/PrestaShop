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
