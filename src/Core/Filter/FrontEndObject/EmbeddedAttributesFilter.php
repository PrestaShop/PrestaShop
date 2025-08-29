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
