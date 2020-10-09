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
function drop_module_non_unique_index()
{
    $index = Db::getInstance()->executeS('SHOW INDEX FROM `'._DB_PREFIX_.'module` WHERE Key_name = "name"');
    if (is_array($index) && count($index)) {
        Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'module` DROP INDEX `name`');
    }
    $tmp = Db::getInstance()->executeS('SHOW SESSION VARIABLES WHERE Variable_Name LIKE "old_alter_table" AND Value like "OFF"');
    if (is_array($tmp) && $tmp) {
        Db::getInstance()->execute('SET SESSION old_alter_table="ON"');
    }
    Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'module` ADD UNIQUE `name` (`name`)');
    if (is_array($tmp) && $tmp) {
        Db::getInstance()->execute('SET SESSION old_alter_table="OFF"');
    }
}
