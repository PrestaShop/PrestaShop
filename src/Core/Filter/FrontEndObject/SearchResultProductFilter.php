<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Product objects for search results.
 */
class SearchResultProductFilter extends HashMapWhitelistFilter
{
    public function __construct()
    {
        $whitelist = [
            'active',
            'add_to_cart_url',
            'canonical_url',
            'category_name',
            'cover',
            'description_short',
            'discount_amount',
            'discount_percentage',
            'discount_percentage_absolute',
            'discount_to_display',
            'discount_type',
            'has_discount',
            'id_product',
            'labels',
            'link',
            'link_rewrite',
            'main_variants',
            'manufacturer_name',
            'name',
            'position',
            'price',
            'price_amount',
            'rate',
            'reference',
            'regular_price',
            'regular_price_amount',
            'tax_name',
            'unit_price',
            'url',
        ];

        $this->whitelist($whitelist);
    }
}
