<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function migrate_data_from_store_to_store_lang_and_clean_store()
{
    $res = TRUE;
    $stores = Db::getInstance()
        ->executeS("SELECT `id_store`, `name`, `address1`, `address2`, `hours`, `note` FROM `" . _DB_PREFIX_ . "store` ");
    $langs = Db::getInstance()
        ->executeS("SELECT `id_lang` FROM `" . _DB_PREFIX_ . "lang` ");

    foreach ($stores as $store) {
        foreach ($langs as $lang) {
            $values = "(" . $store['id_store'] . "," . $lang['id_lang'] . ",'" . $store['name'] . "','" . $store['address1'] . "','" . $store['address2'] . "','" . $store['hours'] . "','" . $store['note'] . "')";
            $res &= Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "store_lang` (`id_store`, `id_lang`, `name`, `address1`, `address2`, `hours`, `note`) VALUES" . $values);
        }
    }

    /** clean store */
    DB::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'store` DROP `name`, DROP `address1`, DROP `address2`, DROP `hours`, DROP `note`');

}
