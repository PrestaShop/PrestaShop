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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

if (!$id_product = (int)(Tools::getValue('id_product')))
	die ('0');
$id_product_attribute = (int)(Tools::getValue('id_product_attribute'));

if (!$cookie->isLogged())
{
	$customer_email = trim(Tools::getValue('customer_email'));
	if (empty($customer_email) OR !Validate::isEmail($customer_email))
		die ('0');
	if ($customer_email == 'your@email.com')
		die ('0');

	$id_customer = (int)Db::getInstance()->getValue('SELECT id_customer FROM '._DB_PREFIX_.'customer WHERE email=\''.pSQL($customer_email).'\' AND is_guest=0');
	// Check if already in DB
	if (Db::getInstance()->ExecuteS('
	SELECT * 
	FROM `'._DB_PREFIX_.'mailalert_customer_oos` 
	WHERE `id_customer` = '.(int)($id_customer).'
	AND `customer_email` = \''.pSQL($customer_email).'\'
	AND `id_product` = '.(int)($id_product).'
	AND `id_product_attribute` = '.(int)($id_product_attribute)))
		die('1');
}
else
{
	$id_customer = (int)($cookie->id_customer);
	$customer_email = 0;
}

if (Db::getInstance()->Execute('
	REPLACE INTO `'._DB_PREFIX_.'mailalert_customer_oos` (`id_customer`, `customer_email`, `id_product` , `id_product_attribute`)
	VALUES ('.(int)($id_customer).', \''.pSQL($customer_email).'\', '.(int)($id_product).', '.(int)($id_product_attribute).')'))
	die ('1');

die ('0');


