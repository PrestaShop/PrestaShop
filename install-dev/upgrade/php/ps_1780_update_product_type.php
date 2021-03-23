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

function ps_1780_update_product_type()
{
    // Update for each product the appropriate product_type based on dynamic associations
    // (combinations, pack, virtual or standard) There are actually "tricks" since default
    // combination or cached value are present in the table, this is more performant since
    // we don't to compute the associations for each products in database

    // First set all as standard
    Db::getInstance()->execute(
        'UPDATE `' . _DB_PREFIX_ . 'product` SET `product_type` = "standard"'
    );
    Db::getInstance()->execute(
        'UPDATE `' . _DB_PREFIX_ . 'product` SET `product_type` = "combinations" WHERE `cache_default_attribute` != 0'
    );
    Db::getInstance()->execute(
        'UPDATE `' . _DB_PREFIX_ . 'product` SET `product_type` = "pack" WHERE `cache_is_pack` = 1'
    );
    Db::getInstance()->execute(
        'UPDATE `' . _DB_PREFIX_ . 'product` SET `product_type` = "virtual" WHERE `is_virtual` = 1'
    );
}
