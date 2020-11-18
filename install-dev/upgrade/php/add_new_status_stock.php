<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function add_new_status_stock()
{
    $translator = Context::getContext()->getTranslator();
    $languages  = Language::getLanguages();

    // insert ps_tab AdminStockManagement
    $count = (int)Db::getInstance()->getValue(
        'SELECT count(id_tab) FROM `' . _DB_PREFIX_ . 'tab` 
        WHERE `class_name` = \'AdminStockManagement\'
        AND `id_parent` = 9'
    );
    if (!$count) {
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'tab`
            (`id_tab`, `id_parent`, `position`, `module`, `class_name`, `active`, `hide_host_mode`, `icon`)
            VALUES (null, 9, 7, NULL, \'AdminStockManagement\', 1, 0, \'\')'
        );
        $lastIdTab = (int)Db::getInstance()->Insert_ID();

        // ps_tab_lang
        foreach ($languages as $lang) {
            $idLang    = (int)$lang['id_lang'];
            $stockName = pSQL(
                $translator->trans(
                    'Stock',
                    array(),
                    'Admin.Navigation.Menu',
                    $lang['locale']
                )
            );
            Db::getInstance()->execute(
                "INSERT INTO `" . _DB_PREFIX_ . "tab_lang` (`id_tab`, `id_lang`, `name`) 
                VALUES (
                  " . $lastIdTab . ", 
                  " . $idLang . ", 
                  '" . $stockName . "'
                )"
            );
        }
    }

    // Stock movements
    $data = array(
        array(
            'name' => 'Customer Order',
            'sign' => 1,
            'conf' => 'PS_STOCK_CUSTOMER_ORDER_CANCEL_REASON',
        ),
        array(
            'name' => 'Product Return',
            'sign' => 1,
            'conf' => 'PS_STOCK_CUSTOMER_RETURN_REASON',
        ),
        array(
            'name' => 'Employee Edition',
            'sign' => 1,
            'conf' => 'PS_STOCK_MVT_INC_EMPLOYEE_EDITION',
        ),
        array(
            'name' => 'Employee Edition',
            'sign' => -1,
            'conf' => 'PS_STOCK_MVT_DEC_EMPLOYEE_EDITION',
        ),
    );

    foreach ($data as $d) {
        // We don't want duplicated data
        if (configuration_exists($d['conf'])) {
            continue;
        }

        // ps_stock_mvt_reason
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'stock_mvt_reason` (`sign`, `date_add`, `date_upd`, `deleted`)
            VALUES (' . $d['sign'] . ', NOW(), NOW(), "0")'
        );

        // ps_configuration
        $lastInsertedId = Db::getInstance()->Insert_ID();
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'configuration` (`name`, `value`, `date_add`, `date_upd`)
            VALUES ("' . $d['conf'] . '", ' . (int)$lastInsertedId . ', NOW(), NOW())'
        );

        // ps_stock_mvt_reason_lang
        foreach ($languages as $lang) {
            $mvtName = pSQL(
                $translator->trans(
                    $d['name'],
                    array(),
                    'Admin.Catalog.Feature',
                    $lang['locale']
                )
            );
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'stock_mvt_reason_lang` (`id_stock_mvt_reason`, `id_lang`, `name`)
                VALUES (' . (int)$lastInsertedId . ', ' . (int)$lang['id_lang'] . ', "' . $mvtName . '")'
            );
        }
    }

    // sync all stock
    $shops = Shop::getShops();
    foreach ($shops as $shop) {
        (new \PrestaShop\PrestaShop\Adapter\StockManager())->updatePhysicalProductQuantity(
            (int)$shop['id_shop'],
            (int)Configuration::get('PS_OS_ERROR'),
            (int)Configuration::get('PS_OS_CANCELED')
        );
    }
}

function configuration_exists($confName)
{
    $count = (int)Db::getInstance()->getValue(
        'SELECT count(id_configuration)
        FROM `' . _DB_PREFIX_ . 'configuration` 
        WHERE `name` = \'' . $confName . '\''
    );

    return $count > 0;
}
