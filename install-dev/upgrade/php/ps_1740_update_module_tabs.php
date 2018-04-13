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
function ps_1740_update_module_tabs()
{
    // Add new sub menus for modules
    $moduleTabsToBeAdded = array(
        'AdminModulesManage' => 'en:Installed modules|fr:Modules installés|es:Módulos instalados|de:Installierte Module|it:Moduli installati',
        'AdminModulesCatalog' => 'en:Selection|fr:Selection|es:Selección|de:Auswahl|it:Selezione',
        'AdminModulesNotifications' => 'en:Notifications|fr:Notifications|es:Notificaciones|de:Nachrichten|it:Notifiche'
    );

    include_once('add_new_tab.php');
    foreach ($moduleTabsToBeAdded as $className => $translations) {
        add_new_tab($className, $translations, 0, false, 'AdminModulesSf');
    }
}
