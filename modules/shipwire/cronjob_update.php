<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__).'/../../config/config.inc.php');

if (isset($_GET['secure_key']) || isset($_ARGV[1]))
{
	$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
	if (!empty($secureKey) && ($secureKey == $_GET['secure_key'] || (isset($_ARGV[1]) && $secureKey == $_ARGV[1])))
	{
		// Clean logs
		$logDeleteDate = date('Y-m-d', mktime(0, 0, 0, date('n'), date('j') - Configuration::get('SHIPWIRE_LOG_LIFE'), date('Y'))).' 00:00:00';
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'shipwire_log` WHERE `date_added` < \''.pSQL($logDeleteDate).'\'');

		if (!class_exists('Shipwire'))
			require(dirname(__FILE__).'/shipwire.php');

		require(dirname(__FILE__).'/lib/ShipwireApi.php');
		require(dirname(__FILE__).'/lib/ShipwireTracking.php');

		$m = new Shipwire();
		$d = Db::getInstance()->ExecuteS('SELECT `id_order` FROM `'._DB_PREFIX_.'shipwire_order` WHERE `transaction_ref` IS NULL OR `transaction_ref` = \'\'');
		foreach ($d as $line)
			$m->updateOrderStatus($line['id_order']);

		ShipwireTracking::updateTracking(); // This will be called directly thru the browser or cronjob
	}

	die('Invalid key.');
}