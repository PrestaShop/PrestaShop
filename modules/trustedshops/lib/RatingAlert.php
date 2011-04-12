<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class RatingAlert
{
	const TABLE_NAME = 'ts_rating_alert';
	const MAIL_TEMPLATE = 'rating_email';
	
	public static function save($id_order)
	{
		Db::getInstance()->AutoExecute(_DB_PREFIX_.self::TABLE_NAME, array('id_order' => (int)($id_order)), 'INSERT');
	}

	private static function _getAlertsInformations($nb_days = 10)
	{
		return Db::getInstance()->ExecuteS('
		SELECT a.id_alert, c.`email`, o.`id_order`, o.`id_lang`
		FROM `'._DB_PREFIX_.self::TABLE_NAME.'` a  
		LEFT JOIN '._DB_PREFIX_.'orders o ON (a.id_order = o.id_order)
		LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = o.id_customer)
		WHERE DATE_ADD(o.`date_add`, INTERVAL '.(int)($nb_days).' DAY) <= NOW()');
	}
	
	public static function removeAlerts($ids)
	{
		$to_remove = array();
		foreach ($ids AS $id)
			$to_remove[] = (int)($id);
		
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.self::TABLE_NAME.'` WHERE `id_alert` IN (\''.implode('\',\'', $to_remove).'\')');
	}
	
	public static function executeCronTask()
	{
		global $cookie;
		if (!Configuration::get('TS_SEND_SEPERATE_MAIL')) 
			return true;

		$to_remove = array();
		$alerts_infos = RatingAlert::_getAlertsInformations((int)(Configuration::get('TS_SEND_SEPERATE_MAIL_DELAY')));
		
		$ts_module = new TrustedShops();
		
		foreach ($alerts_infos AS $infos)
		{
			$cookie->id_lang = $infos['id_lang'];
			$subject = $ts_module->getL('title_part_1').' '.Configuration::get('PS_SHOP_NAME').$ts_module->getL('title_part_2');
			$template_vars = array('{ts_id}' => Configuration::get('TS_ID_'.(int)($infos['id_lang'])), 
								   '{button_url}' => TrustedShops::getHttpHost(true, true)._MODULE_DIR_.$ts_module->name.'/img',
								   '{rating_url}' => $ts_module->getRatingUrlWithBuyerEmail($infos['id_lang'], $infos['id_order'], $infos['email']));

			$result = Mail::Send((int)($infos['id_lang']), self::MAIL_TEMPLATE, $subject, $template_vars, $infos['email'], NULL, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), NULL, NULL, dirname(__FILE__).'/mails/');

			if ($result)
				$to_remove[] = (int)($infos['id_alert']);
		}
		
		if (sizeof($to_remove) > 0)
			self::removeAlerts($to_remove);
		
		return (sizeof($to_remove) == sizeof($alerts_infos)); 
	}
	
	public static function createTable()
	{
		return Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.self::TABLE_NAME.'` (
			`id_alert` INT NOT NULL AUTO_INCREMENT,
			`id_order` INT NOT NULL,
			PRIMARY KEY (`id_alert`),
			UNIQUE KEY `id_order` (`id_order`)
		) ENGINE = '._MYSQL_ENGINE_);
	}
	
	public static function dropTable()
	{
		return Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.self::TABLE_NAME.'`');
	}
	
	public static function truncateTable()
	{
		return Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.self::TABLE_NAME.'`');
	}
}

