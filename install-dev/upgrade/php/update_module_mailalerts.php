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
*  @version  Release: $Revision: 14012 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function update_module_mailalerts()
{
	if (Db::getInstance()->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name = \'mailalerts\''))
	{
		$result = Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mailalert_customer_oos_new` (
		  `id_customer` int(10) unsigned NOT NULL,
		  `customer_email` varchar(128) NOT NULL,
		  `id_product` int(10) unsigned NOT NULL,
		  `id_product_attribute` int(10) unsigned NOT NULL,
		  `id_lang` int(10) unsigned NOT NULL,
		  `date_add` datetime NOT NULL,
		  PRIMARY KEY (`id_customer`, `customer_email`, `id_product`, `id_product_attribute`, `id_lang`),
		  KEY `customer_email` (`customer_email`),
		  KEY `id_product` (`id_product`,`id_product_attribute`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		
		$default_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$current_time = date('Y-m-d H:i:s');
		$existing_alerts = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'mailalert_customer_oos`', false);
		$result &= (bool)$existing_alerts;

		if ($result)
		{
			$total_alerts = (int)Db::getInstance()->NumRows();
			while ($row = Db::getInstance()->nextRow($existing_alerts))
			{
				if (!isset($row['id_lang']))
					$row['id_lang'] = $default_language;
				if (!isset($row['date_add']))
					$row['date_add'] = $current_time;
					
				$result &= Db::getInstance()->AutoExecute(_DB_PREFIX_.'mailalert_customer_oos_new', $row, 'INSERT');
			}
			
			$total_new_alerts = (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'mailalert_customer_oos_new`');
			
			if ($result && $total_alerts == $total_new_alerts)
			{
				$result &= Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'mailalert_customer_oos');
				if ($result)
					$result &= Db::getInstance()->Execute('RENAME TABLE '._DB_PREFIX_.'mailalert_customer_oos_new TO '._DB_PREFIX_.'mailalert_customer_oos');
			}
		}
		
		return $result;
	}

	return true;
}