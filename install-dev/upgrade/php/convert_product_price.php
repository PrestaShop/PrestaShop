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

/* Convert product prices from the PS < 1.3 wrong rounding system to the new 1.3 one */
function convert_product_price()
{
    $taxes = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'tax');
    $taxRates = array();
    foreach ($taxes as $data) {
        $taxRates[$data['id_tax']] = (float)($data['rate']) / 100;
    }
    $resource = Db::getInstance()->executeS('SELECT `id_product`, `price`, `id_tax`
		FROM `'._DB_PREFIX_.'product`', false);
    if (!$resource) {
        return array('error' => 1, 'msg' => Db::getInstance()->getMsgError());
    } // was previously die(mysql_error())

    while ($row = Db::getInstance()->nextRow($resource)) {
        if ($row['id_tax']) {
            $price = $row['price'] * (1 + $taxRates[$row['id_tax']]);
            $decimalPart = $price - (int)$price;
            if ($decimalPart < 0.000001) {
                $newPrice = (float)(number_format($price, 6, '.', ''));
                $newPrice = Tools::floorf($newPrice / (1 + $taxRates[$row['id_tax']]), 6);
                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET `price` = '.$newPrice.' WHERE `id_product` = '.(int)$row['id_product']);
            }
        }
    }
}
