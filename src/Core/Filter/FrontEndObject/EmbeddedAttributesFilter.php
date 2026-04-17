<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Product EmbeddedAttributes objects that will be sent to the client.
 */
class EmbeddedAttributesFilter extends HashMapWhitelistFilter
{
    public function __construct()
    {
        $whitelist = [
            'attributes',
            'available_later',
            'available_now',
            'category',
            'condition',
            'customizable',
            'description_short',
            'ecotax',
            'ecotax_rate',
            'features',
            'id_customization',
            'id_image',
            'id_manufacturer',
            'id_product',
            'id_product_attribute',
            'link_rewrite',
            'minimal_quantity',
            'name',
            'new',
            'on_sale',
            'online_only',
            'pack',
            'price',
            'price_amount',
            'price_without_reduction',
            'quantity',
            'quantity_wanted',
            'rate',
            'reduction',
            'reference',
            'specific_prices',
            'tax_name',
        ];

        $this->whitelist($whitelist);
    }
}
