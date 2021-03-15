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

function ps_1780_update_tabs()
{
    include_once 'add_new_tab.php';

    add_new_tab_17('AdminParentSecurity', 'en:Security', 0, false, 'AdminAdvancedParameters', '', 'admin_security');
    add_new_tab_17('AdminSecurity', 'en:Security', 0, false, 'AdminParentSecurity', '', 'admin_security');
    add_new_tab_17('AdminSecuritySessionEmployee', 'en:Employee Sessions', 0, false, 'AdminParentSecurity', '', 'admin_security_sessions_employee_list');
    add_new_tab_17('AdminSecuritySessionCustomer', 'en:Customer Sessions', 0, false, 'AdminParentSecurity', '', 'admin_security_sessions_customer_list');

    foreach (['AdminSecurity', 'AdminSecuritySessionEmployee', 'AdminSecuritySessionCustomer'] as $className) {
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'tab` SET `active`= false, `enabled`= true WHERE `class_name` = "' . pSQL($className) . '"'
        );
    }
}
