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

function p15014_add_missing_columns()
{
    $errors = array();
    $db = Db::getInstance();

    // for module statssearch
    $id_module = $db->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name="statssearch"');
    if ($id_module) {
        $list_fields = $db->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'statssearch`');
        foreach ($list_fields as $k => $field) {
            $list_fields[$k] = $field['Field'];
        }

        if (in_array('id_group_shop', $list_fields)) {
            if (!Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'statssearch`
				CHANGE `id_group_shop` `id_shop_group` INT(10) NOT NULL default "1"')) {
                $errors[] = $db->getMsgError();
            }
        }
    }

    if (count($errors)) {
        return array('error' => 1, 'msg' => implode(',', $errors)) ;
    }
}
