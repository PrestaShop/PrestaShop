<?php

/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Filter\FrontEndObject;

use PrestaShop\PrestaShop\Core\Filter\HashMapWhitelistFilter;

/**
 * Filters Product EmbeddedAttributes objects that will be sent to the client
 */
class EmbeddedAttributesFilter extends HashMapWhitelistFilter
{

    public function __construct()
    {
        $whitelist = [
            "attributes",
            "available_date",
            "available_later",
            "available_now",
            "category",
            "condition",
            "customizable",
            "description_short",
            "ecotax",
            "ecotax_rate",
            "features",
            "id_customization",
            "id_image",
            "id_manufacturer",
            "id_product",
            "id_product_attribute",
            "is_virtual",
            "link_rewrite",
            "minimal_quantity",
            "name",
            "new",
            "on_sale",
            "online_only",
            "out_of_stock",
            "pack",
            "price",
            "price_amount",
            "price_without_reduction",
            "quantity",
            "quantity_wanted",
            "rate",
            "reduction",
            "reference",
            "show_price",
            "specific_prices",
            "tax_name",
            "unit_price_ratio",
            "unity",
        ];

        $this->whitelist($whitelist);
    }
}
