<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\Resetter;

use Tests\Resources\DatabaseDump;

class ProductResetter
{
    public static function resetProducts(): void
    {
        DatabaseDump::restoreTables([
            // Product data
            'product',
            'product_attachment',
            'product_attribute',
            'product_attribute_combination',
            'product_attribute_image',
            'product_attribute_lang',
            'product_attribute_shop',
            'product_carrier',
            'product_country_tax',
            'product_download',
            'product_group_reduction_cache',
            'product_lang',
            'product_sale',
            'product_shop',
            'product_supplier',
            'product_tag',
            // Related products
            'accessory',
            // Packs
            'pack',
            // Customizations
            'customization',
            'customization_field',
            'customization_field_lang',
            'customized_data',
            // Specific prices
            'specific_price',
            'specific_price_priority',
            // Stock
            'stock_available',
            'stock_mvt',
            // Images
            'image',
            'image_lang',
            'image_shop',
            // Miscellaneous relationships
            'category_product',
            'feature_product',
        ]);
    }
}
