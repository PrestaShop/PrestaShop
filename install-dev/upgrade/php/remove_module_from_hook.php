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

function remove_module_from_hook($module_name, $hook_name)
{
    $result = true;

    $id_module = Db::getInstance()->getValue(
        '
	SELECT `id_module` FROM `'._DB_PREFIX_.'module`
	WHERE `name` = \''.pSQL($module_name).'\''
    );

    if ((int)$id_module > 0) {
        $id_hook = Db::getInstance()->getValue('
		SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \''.pSQL($hook_name).'\'
		');

        if ((int)$id_hook > 0) {
            $result &= Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_module` = '.(int)$id_module.' AND `id_hook` = '.(int)$id_hook);
        }
    }

    return $result;
}
