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
function ps_1763_update_tabs()
{
    include_once 'add_new_tab.php';
    include_once 'copy_tab_rights.php';

    add_new_tab_17('AdminParentMailTheme', 'en:Email Themes', 0, false, 'AdminParentThemes');
    Db::getInstance()->execute(
        'UPDATE `' . _DB_PREFIX_ . 'tab` SET `active`= 1, `position`= 2 WHERE `class_name` = "AdminParentMailTheme"'
    );

    // Move AdminMailTheme's parent from AdminMailThemeParent to AdminParentMailTheme
    $toParentTabId = Db::getInstance()->getValue(
        'SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "AdminParentMailTheme"'
    );
    Db::getInstance()->execute(
        'UPDATE `' . _DB_PREFIX_ . 'tab` SET `id_parent` = ' . $toParentTabId . ' WHERE class_name = "AdminMailTheme"'
    );

    copy_tab_rights('AdminMailTheme', 'AdminParentMailTheme');
    Db::getInstance()->execute(
        'DELETE FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "AdminMailThemeParent"'
    );
}
