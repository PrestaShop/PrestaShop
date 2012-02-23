<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function add_new_groups($french, $standard)
{
	Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'group` (`id_group`, `date_add`, `date_upd`) VALUES (NULL, NOW(), NOW())');
	$last_id = Db::getInstance()->Insert_ID();
	
	$languages = Db::getInstance()->executeS('SELECT id_lang, iso_code FROM `'._DB_PREFIX_.'lang`');

	$sql = '';
	foreach ($languages as $lang)
		if (strtolower($lang['iso_code']) == 'fr')
			$sql .= '('.(int)$last_id.', '.(int)$lang['id_lang'].', "'.pSQL($french).'"),';
		else
			$sql .= '('.(int)$last_id.', '.(int)$lang['id_lang'].', "'.pSQL($standard).'"),';
	$sql = substr($sql, 0, strlen($sql) - 1);
	Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'group_lang` (`id_group`, `id_lang`, `name`) VALUES '.$sql);
	// we add the different id_group in the configuration
	if (strtolower($standard) == 'unidentified')
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_UNIDENTIFIED_GROUP", "'.(int)$last_id.'", NOW(), NOW())');
	else if (strtolower($standard) == 'guest')
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_GUEST_GROUP", "'.(int)$last_id.'", NOW(), NOW())');
	else if (strtolower($standard) == 'test')
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'configuration` (`id_configuration`, `name`, `value`, `date_add`, `date_upd`) VALUES (NULL, "PS_TEST", "'.(int)$last_id.'", NOW(), NOW())');
}
