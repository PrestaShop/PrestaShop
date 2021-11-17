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

use PrestaShopBundle\Security\Voter\PageVoter;

function copy_tab_rights($fromTabName, $toTabName)
{
    if (empty($fromTabName) || empty($toTabName)) {
        return;
    }
    foreach (array(PageVoter::CREATE, PageVoter::READ, PageVoter::UPDATE, PageVoter::DELETE) as $role) {
        // 1- Add role
        $roleToAdd = strtoupper('ROLE_MOD_TAB_' . $toTabName . '_' . $role);
        Db::getInstance()->execute('INSERT IGNORE INTO `' . _DB_PREFIX_ . 'authorization_role` (`slug`)
            VALUES ("' . pSQL($roleToAdd) . '")');
        $newID = Db::getInstance()->Insert_ID();
        if (!$newID) {
            $newID = Db::getInstance()->getValue('
                SELECT `id_authorization_role`
                FROM `' . _DB_PREFIX_ . 'authorization_role`
                WHERE `slug` = "' . pSQL($roleToAdd) . '"
            ');
        }

        // 2- Copy access
        if (!empty($newID)) {
            $parentRole = strtoupper('ROLE_MOD_TAB_' . pSQL($fromTabName) . '_' . $role);
            Db::getInstance()->execute(
                'INSERT IGNORE INTO `' . _DB_PREFIX_ . 'access` (`id_profile`, `id_authorization_role`)
                SELECT a.`id_profile`, ' . (int) $newID . ' as `id_authorization_role`
                FROM `' . _DB_PREFIX_ . 'access` a join `' . _DB_PREFIX_ . 'authorization_role` ar on a.`id_authorization_role` = ar.`id_authorization_role`
                WHERE ar.`slug` = "' . pSQL($parentRole) . '"'
            );
        }
    }
}
