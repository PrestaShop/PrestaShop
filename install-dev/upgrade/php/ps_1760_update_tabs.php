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
 * File copied from ps_1750_update_module_tabs.php and modified to add new roles
 */
function ps_1760_update_tabs()
{
    // STEP 1: Add new sub menus for modules (tab may exist but we need authorization roles to be added as well)
    $moduleTabsToBeAdded = array(
        'AdminMailThemeParent' => [
            'translations' => 'en:Email Themes',
            'parent' => 'AdminParentThemes',
        ],
        'AdminMailTheme' => [
            'translations' => 'en:Email Themes',
            'parent' => 'AdminMailThemeParent',
        ],
        'AdminModulesUpdates' => array(
            'translations' => 'en:Updates|fr:Mises à jour|es:Actualizaciones|de:Aktualisierung|it:Aggiornamenti',
            'parent' => 'AdminModulesSf',
        ),
        'AdminModulesNotifications' => array(
            'translations' => 'en:Updates|fr:Mises à jour|es:Actualizaciones|de:Aktualisierung|it:Aggiornamenti',
            'parent' => 'AdminModulesSf',
        ),
    );

    include_once 'add_new_tab.php';
    foreach ($moduleTabsToBeAdded as $className => $tabDetails) {
        add_new_tab_17($className, $tabDetails['translations'], 0, false, $tabDetails['parent']);
        Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'tab` SET `active`= 1 WHERE `class_name` = "' . pSQL($className) . '"'
        );
    }
}
