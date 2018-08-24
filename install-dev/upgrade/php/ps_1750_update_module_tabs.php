<?php
/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * File copied from ps_update_tabs.php and modified for only adding modules related tabs
 */
function ps_1750_update_module_tabs()
{
    // STEP 1: Add new sub menus for modules
    $moduleTabsToBeAdded = array(
        'AdminModulesUpdates' => 'en:Updates|fr:Mises à jour|es:Actualizaciones|de:Aktualisierung|it:Aggiornamenti',
    );

    include_once 'add_new_tab.php';
    foreach ($moduleTabsToBeAdded as $className => $translations) {
        add_new_tab_17($className, $translations, 0, false, 'AdminModulesSf');
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'tab` SET `active`= 1 WHERE `class_name` = "' . $className . '"'
        );
    }


    // STEP 2: Rename Notifications as Alerts
    include_once 'clean_tabs_15.php';
    $adminModulesNotificationsTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = "AdminModulesNotifications"'
    );
    if (!empty($adminModulesNotificationsTabId)) {
        renameTab(
            $adminModulesNotificationsTabId,
            [
                'fr' => 'Alertes',
                'es' => 'Alertas',
                'en' => 'Alerts',
                'gb' => 'Alerts',
                'de' => 'Benachrichtigungen',
                'it' => 'Avvisi',
            ]
        );
    }

    $adminModulesCatalogTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = "AdminModulesCatalog"'
    );
    if (!empty($adminModulesCatalogTabId)) {
        renameTab(
            $adminModulesCatalogTabId,
            [
                'fr' => 'Catalogue',
                'es' => 'Catálogo',
                'en' => 'Catalog',
                'gb' => 'Catalog',
                'de' => 'Catalogus',
                'it' => 'Catalogo',
            ]
        );
    }

    $adminModulesManageTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = "AdminModulesManage"'
    );
    if (!empty($adminModulesManageTabId)) {
        renameTab(
            $adminModulesManageTabId,
            [
                'fr' => 'Modules',
                'es' => 'módulos',
                'en' => 'Modules',
                'gb' => 'Modules',
                'de' => 'Modules',
                'it' => 'Moduli',
            ]
        );
    }
}
