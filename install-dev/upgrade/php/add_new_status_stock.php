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

/**
 * Select all current payment modules for the carrier restriction
 */
function add_new_status_stock()
{
    $translator = Context::getContext()->getTranslator();

    $data = array(
        0 => array(
            'name' => 'Customer Order',
            'sign' => 1,
            'conf' => 'PS_STOCK_CUSTOMER_ORDER_CANCEL_REASON'
        ),
        1 => array(
            'name' => 'Product Return',
            'sign' => 1,
            'conf' => 'PS_STOCK_CUSTOMER_RETURN_REASON'
        ),
        2 => array(
            'name' => 'Employee Edition',
            'sign' => 1,
            'conf' => 'PS_STOCK_MVT_INC_EMPLOYEE_EDITION'
        ),
        3 => array(
            'name' => 'Employee Edition',
            'sign' => -1,
            'conf' => 'PS_STOCK_MVT_DEC_EMPLOYEE_EDITION'
        ),
    );

    $languages = Language::getLanguages();

    // insert ps_tab AdminStockManagement
    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'tab` (`id_tab`, `id_parent`, `position`, `module`, `class_name`, `active`, `hide_host_mode`, `icon`)
      VALUES (null, 9, 7, NULL, \'AdminStockManagement\', 1, 0, \'\')');
    $lastIdTab = Db::getInstance()->insert_id();

    // ps_tab_lang
    foreach ($languages as $lang) {
        Db::getInstance()->execute(
            'INSERT INTO `'._DB_PREFIX_.'tab_lang` (`id_tab`, `id_lang`, `name`)
                VALUES ('.(int)$lastIdTab.', '.(int)$lang['id_lang'].', "'.pSQL($translator->trans('Stocks', array(), 'Admin.Navigation.Menu', $lang['locale'])).'")');
    }

    foreach ($data as $d) {
        // ps_stock_mvt_reason
         Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'stock_mvt_reason` (`sign`, `date_add`, `date_upd`, `deleted`)
            VALUES ('.$d['sign'].', NOW(), NOW(), "0")');

        // ps_configuration
        $lastInsertedId = Db::getInstance()->insert_id();
        Db::getInstance()->execute('
            INSERT INTO `'._DB_PREFIX_.'configuration` (`name`, `value`, `date_add`, `date_upd`)
            VALUES ("'.$d['conf'].'", '.(int)$lastInsertedId.', NOW(), NOW())');

        // ps_stock_mvt_reason_lang
        foreach ($languages as $lang) {
            Db::getInstance()->execute(
                'INSERT INTO `'._DB_PREFIX_.'stock_mvt_reason_lang` (`id_stock_mvt_reason`, `id_lang`, `name`)
                VALUES ('.(int)$lastInsertedId.', '.(int)$lang['id_lang'].', "'.pSQL($translator->trans($d['name'], array(), 'Admin.Catalog.Feature', $lang['locale'])).'")');
        }
    }

    // sync all stock
    $shops = Shop::getShops();
    foreach ($shops as $shop) {
        (new \PrestaShop\PrestaShop\Adapter\StockManager())->updatePhysicalProductQuantity(
            $shop['id_shop'],
            (int)Configuration::get('PS_OS_ERROR'),
            (int)Configuration::get('PS_OS_CANCELED')
        );
    }
}
