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

/**
 * File copied from ps_update_tabs.php and modified for only adding modules related tabs
 */
function ps_1750_update_module_tabs()
{
    // STEP 1: Add new sub menus for modules
    $moduleTabsToBeAdded = array(
        'AdminModulesUpdates' => array(
            'translations' => 'en:Updates|fr:Mises à jour|es:Actualizaciones|de:Aktualisierung|it:Aggiornamenti',
            'parent' => 'AdminModulesSf',
        ),
        'AdminParentModulesCatalog' => array(
            'translations' => 'en:Module Catalog|fr:Catalogue de modules|es:Catálogo de módulos|de:Modulkatalog|it:Catalogo dei moduli',
            'parent' => 'AdminParentModulesSf',
        ),
    );

    include_once 'add_new_tab.php';
    foreach ($moduleTabsToBeAdded as $className => $tabDetails) {
        add_new_tab_17($className, $tabDetails['translations'], 0, false, $tabDetails['parent']);
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'tab` SET `active`= 1 WHERE `class_name` = "' . $className . '"'
        );
    }

    // STEP 2: Rename module tabs (Notifications as Alerts, Module selection as Module Catalog, Module Catalog as Module Selections)
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
                'fr' => 'Catalogue de modules',
                'es' => 'Catálogo de módulos',
                'en' => 'Module Catalog',
                'gb' => 'Module Catalog',
                'de' => 'Versanddienst',
                'it' => 'Catalogo dei moduli',
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

    $adminModulesAddonsSelectionsTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = "AdminAddonsCatalog"'
    );
    if (!empty($adminModulesAddonsSelectionsTabId)) {
        renameTab(
            $adminModulesAddonsSelectionsTabId,
            [
                'fr' => 'Sélections de modules',
                'es' => 'Selecciones de módulos',
                'en' => 'Module Selections',
                'gb' => 'Module Selections',
                'de' => 'Auswahl von Modulen',
                'it' => 'Selezioni Moduli',
            ]
        );
    }

    // STEP 3: Move the 2 module catalog controllers in the parent one
    // Get The ID of the parent
    $adminParentModuleCatalogTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = "AdminParentModulesCatalog"'
    );
    foreach (array('AdminModulesCatalog', 'AdminAddonsCatalog') as $key => $className) {
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'tab` SET `id_parent`= ' . (int) $adminParentModuleCatalogTabId . ', position = '. $key . ' WHERE `class_name` = "' . $className . '"'
        );
    }
}
