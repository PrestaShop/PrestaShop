<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function add_order_state($conf_name, $name, $invoice, $send_email, $color, $unremovable, $logable, $delivery, $template = null)
{
	$name_lang = array();
	$template_lang = array();
	foreach (explode('|', $name) AS $item)
	{
		$temp = explode(':', $item);
		$name_lang[$temp[0]] = $temp[1];
	}
	
	if ($template)
		foreach (explode('|', $template) AS $item)
		{
			$temp = explode(':', $item);
			$template_lang[$temp[0]] = $temp[1];
		}
	
	Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'order_state` (`invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`) 
		VALUES ('.(int)$invoice.', '.(int)$send_email.', \''.pSQL($color).'\', '.(int)$unremovable.', '.(int)$logable.', '.(int)$delivery.')');
	
	$id_order_state = Db::getInstance()->getValue('
		SELECT MAX(`id_order_state`)
		FROM `'._DB_PREFIX_.'order_state`
	');
	
	foreach (Language::getLanguages() AS $lang)
	{
		Db::getInstance()->Execute('
		INSERT IGNORE INTO `'._DB_PREFIX_.'order_state_lang` (`id_lang`, `id_order_state`, `name`, `template`) 
		VALUES ('.(int)$lang['id_lang'].', '.(int)$id_order_state.', \''.pSQL(isset($name_lang[$lang['iso_code']]) ? $name_lang[$lang['iso_code']] : $name_lang['en']).'\', \''.pSQL(isset($template_lang[$lang['iso_code']]) ? $template_lang[$lang['iso_code']] : (isset($template_lang['en']) ? $template_lang['en'] : '')).'\')
		');
	}
	
	Configuration::updateValue($conf_name, $id_order_state);
}