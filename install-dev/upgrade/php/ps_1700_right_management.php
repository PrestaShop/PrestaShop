<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function ps_1700_right_management()
{
    $actions = array('CREATE', 'READ', 'UPDATE', 'DELETE');

    /**
     * Add roles
     */
    foreach (array('TAB', 'MODULE') as $element) {
        foreach ($actions as $action) {
            Db::getInstance()->execute('
                INSERT IGNORE INTO `'._DB_PREFIX_.'authorization_role`
                (`slug`)
                SELECT CONCAT("ROLE_MOD_'.$element.'_", UCASE(`class_name`), "_'.$action.'")
                FROM `'._DB_PREFIX_.strtolower($element).'`
            ');
        }
    }

    /**
     * Add access
     */
    $accessObject = new Access;

    // Tabs
    $oldAccess = Db::getInstance()->executeS('SELECT t.id_tab, a.id_profile, a.view, a.add, a.edit, a.delete FROM `'._DB_PREFIX_.'tab` t LEFT JOIN `'._DB_PREFIX_.'access_old` a USING (id_tab)');
    if (empty($oldAccess)) {
        $oldAccess = array();
    }
    foreach ($oldAccess as $currOldAccess) {
        foreach (array('view', 'add', 'edit', 'delete') as $action) {
            if (array_key_exists($action, $currOldAccess) && ($currOldAccess[$action] == '1'
                    || $currOldAccess['id_profile'] == _PS_ADMIN_PROFILE_
                    || empty($currOldAccess['id_profile']))) {
                $accessObject->updateLgcAccess(
                    !empty($currOldAccess['id_profile'])?$currOldAccess['id_profile']:_PS_ADMIN_PROFILE_,
                    $currOldAccess['id_tab'],
                    $action,
                    true
                );
            }
        }
    }

    // Modules
    $oldAccess = Db::getInstance()->executeS('SELECT mo.id_module, m.id_profile, m.configure, m.view, m.uninstall FROM `'._DB_PREFIX_.'module` mo LEFT JOIN `'._DB_PREFIX_.'module_access_old` m USING (id_module)');
    if (empty($oldAccess)) {
        $oldAccess = array();
    }

    foreach ($oldAccess as $currOldAccess) {
        foreach (array('configure', 'view', 'uninstall') as $action) {
            if (array_key_exists($action, $currOldAccess) && ($currOldAccess[$action] == '1'
                    || $currOldAccess['id_profile'] == _PS_ADMIN_PROFILE_
                    || empty($currOldAccess['id_profile']))) {
                $accessObject->updateLgcModuleAccess(
                    !empty($currOldAccess['id_profile'])?$currOldAccess['id_profile']:_PS_ADMIN_PROFILE_,
                    $currOldAccess['id_module'],
                    $action,
                    true
                );
            }
        }
    }

    return true;
}
