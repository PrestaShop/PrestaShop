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
