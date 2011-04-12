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

include('../../config/config.inc.php');
include('../../init.php');

global $cookie;

$validReturn = array('infoexterne','token','etat','envoi');

$return = array();
foreach ($_GET AS $key => $val)
	if (in_array(strtolower($key),$validReturn))
		$return[strtolower($key)] = utf8_encode(urldecode(stripslashes($val)));
		
if (isset($return['infoexterne']) AND isset($return['token']) AND isset($return['etat']))
{	
	$id_order = str_replace(str_replace('.','_',str_replace('www.','',$_SERVER['HTTP_HOST'])).'_','',$return['infoexterne']);
	
	$order = new Order((int)($id_order));
	$customer = new Customer((int)($order->id_customer));
	$confs = Configuration::getMultiple(array('EMC_SEND_STATE', 'EMC_ORDER_PAST_STATE', 'EMC_DELIVERY_STATE'));
	
	if ($customer->secure_key != $return['token'])
		d(Tools::displayError('Hack attempt'));
	else
	{
		switch($return['etat'])
		{
			//commande pass�e
			case 'CMD' :
				$history = new OrderHistory();
				$history->id_order = (int)($id_order);
				$history->changeIdOrderState((int)($confs['EMC_ORDER_PAST_STATE']), (int)($history->id_order));
				$history->id_employee = (int)($cookie->id_employee);
				$history->addWithemail();
				
				$db = Db::getInstance();
				$db->ExecuteS('SELECT * FROM '._DB_PREFIX_.'envoimoinscher WHERE id_order = '.(int)($id_order));
				$numRows = (int)($db->NumRows());
				if ($numRows == 0)
				{
					if (Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'envoimoinscher VALUES (\''.(int)($id_order).'\', \''.$return['envoi'].'\');'));
				}
				else
				{
					if (Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'envoimoinscher SET shipping_number=\''.$return['envoi'].'\' WHERE id_order=\''.(int)($id_order).'\' '));	
				}
			
			break;
			//colis (ou autre objet) envoy�
			case 'ENV' :
				$history = new OrderHistory();
				$history->id_order = (int)($id_order);
				$history->changeIdOrderState((int)($confs['EMC_SEND_STATE']), (int)($history->id_order));
				$history->id_employee = (int)($cookie->id_employee);
				$history->addWithemail();
			break;
			//envoi annul�
			case 'ANN' :
				$message = new Message();
				$texte = 'Envoi Moins cher : envoi annul�';
				$message->message = htmlentities($texte, ENT_COMPAT, 'UTF-8');
				$message->id_order = (int)($id_order);
				$message->private = 1;
				$message->add();
			break;
			//objet livr� (pas g�r� actuellement)
				case 'LIV' :
				$history = new OrderHistory();
				$history->id_order = (int)($id_order);
				$history->changeIdOrderState((int)($confs['EMC_DELIVERY_STATE']), (int)($history->id_order));
				$history->id_employee = (int)($cookie->id_employee);
				$history->addWithemail();
			break;
	
		
		}	
	
	
	}
}
else
d(Tools::displayError('Hack attempt'));

