<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function add_new_tab($className, $name, $id_parent, $returnId = false, $parentTab = null, $module = '')
{
    if (!is_null($parentTab) && !empty($parentTab) && strtolower(trim($parentTab)) !== 'null') {
        $id_parent = (int)Db::getInstance()->getValue('SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \''.pSQL($parentTab).'\'');
    }

    $array = array();
    foreach (explode('|', $name) as $item) {
        $temp = explode(':', $item);
        $array[$temp[0]] = $temp[1];
    }

    if (!(int)Db::getInstance()->getValue('SELECT count(id_tab) FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \''.pSQL($className).'\' ')) {
        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'tab` (`id_parent`, `class_name`, `module`, `position`) VALUES ('.(int)$id_parent.', \''.pSQL($className).'\', \''.pSQL($module).'\',
									(SELECT IFNULL(MAX(t.position),0)+ 1 FROM `'._DB_PREFIX_.'tab` t WHERE t.id_parent = '.(int)$id_parent.'))');
    }

    $languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`');
    foreach ($languages as $lang) {
        Db::getInstance()->execute('
		INSERT IGNORE INTO `'._DB_PREFIX_.'tab_lang` (`id_lang`, `id_tab`, `name`)
		VALUES ('.(int)$lang['id_lang'].', (
				SELECT `id_tab`
				FROM `'._DB_PREFIX_.'tab`
				WHERE `class_name` = \''.pSQL($className).'\' LIMIT 0,1
			), \''.pSQL(isset($array[$lang['iso_code']]) ? $array[$lang['iso_code']] : $array['en']).'\')
		');
    }

    if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
        Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`)
								(SELECT `id_profile`, (
								SELECT `id_tab`
								FROM `'._DB_PREFIX_.'tab`
								WHERE `class_name` = \''.pSQL($className).'\' LIMIT 0,1
								), 1, 1, 1, 1 FROM `'._DB_PREFIX_.'profile` )');
    } else {
        // Preliminary - Get Parent class name for slug generation
        $parentClassName = null;
        if ($id_parent) {
            $parentClassName = Db::getInstance()->getValue('
                SELECT `class_name`
                FROM `'._DB_PREFIX_.'tab`
                WHERE `id_tab` = "'.(int) $id_parent.'"
            ');
        }

        foreach (array('CREATE', 'READ', 'UPDATE', 'DELETE') as $role) {
            // 1- Add role
            $roleToAdd = 'ROLE_MOD_TAB_'.strtoupper($className).'_'.$role;
            Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'authorization_role` (`slug`)
		VALUES ("'.$roleToAdd.'")');
            $newID = Db::getInstance()->Insert_ID();

            // 2- Copy access from the parent
            if (!empty($parentClassName) && !empty($newID)) {
                $parentRole = 'ROLE_MOD_TAB_'.strtoupper(pSQL($parentClassName)).'_'.$role;
                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_authorization_role`) 
                    SELECT a.`id_profile`, '. (int)$newID .' as `id_authorization_role`
                    FROM `'._DB_PREFIX_.'access` a join `'._DB_PREFIX_.'authorization_role` ar on a.`id_authorization_role` = ar.`id_authorization_role`
                    WHERE ar.`slug` = "'.pSQL($parentRole).'"'
                );
            }
        }
    }

    if ($returnId && strtolower(trim($returnId)) !== 'false') {
        return (int)Db::getInstance()->getValue('SELECT `id_tab`
								FROM `'._DB_PREFIX_.'tab`
								WHERE `class_name` = \''.pSQL($className).'\'');
    }
}
