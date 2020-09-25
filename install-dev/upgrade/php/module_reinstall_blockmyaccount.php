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

function module_reinstall_blockmyaccount()
{
    $res = true;
    $id_module = Db::getInstance()->getValue('SELECT id_module FROM '._DB_PREFIX_.'module where name="blockmyaccount"');
    if ($id_module) {
        $res &= Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'hook`
			(`name`, `title`, `description`, `position`) VALUES
			("displayMyAccountBlock", "My account block", "Display extra informations inside the \"my account\" block", 1)');
        // register left column, and header, and addmyaccountblockhook
        $hooks = array('leftColumn', 'header');
        foreach ($hooks as $hook_name) {
            // do not pSql hook_name
            $row = Db::getInstance()->getRow('SELECT h.id_hook, '.$id_module.' as id_module, MAX(hm.position)+1 as position
				FROM  `'._DB_PREFIX_.'hook_module` hm
				LEFT JOIN `'._DB_PREFIX_.'hook` h on hm.id_hook=h.id_hook
				WHERE h.name = "'.$hook_name.'" group by id_hook');
            $res &= Db::getInstance()->insert('hook_module', $row);
        }

        return $res;
    }

    return true;
}
