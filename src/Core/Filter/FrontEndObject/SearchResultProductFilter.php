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
