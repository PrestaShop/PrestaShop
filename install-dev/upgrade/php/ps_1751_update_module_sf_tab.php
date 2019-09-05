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

/**
 * File copied from ps_update_tabs.php and modified for only adding modules related tabs
 */
function ps_1751_update_module_sf_tab()
{
    // Rename parent module tab (= Module manager)
    include_once 'clean_tabs_15.php';
    $adminModulesParentTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = "AdminModulesSf"'
    );
    if (!empty($adminModulesParentTabId)) {
        renameTab(
            $adminModulesParentTabId,
            [
                'fr' => 'Gestionnaire de modules',
                'es' => 'Gestor de módulos',
                'en' => 'Module Manager',
                'gb' => 'Module Manager',
                'de' => 'Modulmanager',
                'it' => 'Gestione moduli',
            ]
        );
    }
}
