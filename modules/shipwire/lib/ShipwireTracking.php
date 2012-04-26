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

if (!defined('_PS_VERSION_'))
	require(dirname(__FILE__).'/../../../config/config.inc.php');

if (!class_exists('Shipwire'))
	require(dirname(__FILE__).'/../shipwire.php');

class ShipwireTracking extends ShipwireApi
{
	protected $_apiType = 'trackingUpdate';

	public function __construct()
	{
		parent::__construct();

		$this->_configVars['SHIPWIRE_ACCOUNT_NAME'] = '';

		$this->_xml['header'] = array(
			'<TrackingUpdate>',
			'<Username>'.$this->_configVars['SHIPWIRE_API_USER'].'</Username>',
			'<Password>'.$this->_configVars['SHIPWIRE_API_PASSWD'].'</Password>',
			'<Server>'.$this->_configVars['SHIPWIRE_API_MODE'].'</Server>',
			'<AffiliateId>7403</AffiliateId>',
		);
		$this->_xml['body'] = array();
		$this->_xml['footer'][] = '</TrackingUpdate>';
	}

	public function retrieveFromTransacId($transactionId)
	{
		$this->_xml['body'][] = '<ShipwireId>'.pSQL($transactionId).'</ShipwireId>';
	}

	public function retrieveFromOrderId($orderId)
	{
		$this->_xml['body'][] = '<OrderNo>'.pSQL($orderId).'</OrderNo>';
	}

	public function retrieveFull()
	{
		$this->_xml['body'][] = '<Bookmark>1</Bookmark>';
	}

	public function retrieveNew()
	{
		$this->_xml['body'][] = '<Bookmark>3</Bookmark>';
	}

	/*
	 * @brief Updates tracking info in local database.
	 *		Declared as a static method so shipwire.php can access it thru autoload
	*/
	public static function updateTracking($static = false)
	{
		return updateTracking($static);
	}
}

function updateTracking($static = false)
{
	$api = new ShipwireTracking();
	$api->retrieveFull();
	$d = $api->sendData();

	if ($d['Status'])
		if ($static)
			return false;
		else
			die('KO');

	if ($d['TotalOrders'] > 0)
	{
		foreach ($d['Order'] as $order)
		{
			$o = array();
			if (isset($order['@attributes']))
				$o = $order['@attributes'];

			if (!isset($o['id']))
			{
				Logger::addLog('Shipwire: Order ID not defined. >>>>'.print_r($d, true).'<<<<', 4);
				continue;
			}

			$orderExists = Db::getInstance()->ExecuteS('SELECT `id_order`
				FROM `'._DB_PREFIX_.'shipwire_order`
				WHERE `id_order` = '.(int)$o['id'].' LIMIT 1');

			if (isset($orderExists[0]['id_order']) && !empty($orderExists[0]['id_order']))
			{
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'shipwire_order` SET '
				.(isset($order['TrackingNumber']) ? '`tracking_number` = \''.pSQL($order['TrackingNumber']).'\',' : '')
				.(isset($o['shipped']) ? '`shipped` = \''.pSQL($o['shipped']).'\'' : '')
				.(isset($o['shipper']) ? ',`shipper` = \''.pSQL($o['shipper']).'\'' : '')
				.(isset($o['shipDate']) ? ',`shipDate` = \''.pSQL($o['shipDate']).'\'' : '')
				.(isset($o['expectedDeliveryDate']) ? ',`expectedDeliveryDate` = \''.pSQL($o['expectedDeliveryDate']).'\'' : '')
				.(isset($o['href']) ? ',`href` = \''.pSQL($o['href']).'\'' : '')
				.(isset($o['shipperFullName']) ? ',`shipperFullName` = \''.pSQL($o['shipperFullName']).'\'' : '')
				.' WHERE `id_order` = '.(int)$o['id']);
			}
			else
			{
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'shipwire_order`
				(`id_order`, `tracking_number`, `shipped`, `shipper`, `shipDate`, `expectedDeliveryDate`, `href`, `shipperFullName`)
				VALUES (
				\''.pSQL($o['id']).'\''
				.(isset($order['TrackingNumber']) ? ',\''.pSQL($order['TrackingNumber']).'\'' : ',\'\'')
				.(isset($o['shipped']) ? ',\''.pSQL($o['shipped']).'\'' : ',\'\'')
				.(isset($o['shipper']) ? ',\''.pSQL($o['shipper']).'\'' : ',\'\'')
				.(isset($o['shipDate']) ? ',\''.pSQL($o['shipDate']).'\'' : ',\'\'')
				.(isset($o['expectedDeliveryDate']) ? ',\''.pSQL($o['expectedDeliveryDate']).'\'' : ',\'\'')
				.(isset($o['href']) ? ',\''.pSQL($o['href']).'\'' : ',\'\'')
				.(isset($o['shipperFullName']) ? ',\''.pSQL($o['shipperFullName']).'\'' : ',\'\'')
				.')');
			}

			$result = Db::getInstance()->getValue('SELECT `transaction_ref`
				FROM `'._DB_PREFIX_.'shipwire_order`
				WHERE `id_order` = '.(int)$o['id']);
			if (empty($result))
			{
				$module = new Shipwire();
				$module->updateOrderStatus((int)$o['id'], true);
			}

			if (isset($order['TrackingNumber']))
			{
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'orders`
										SET `shipping_number` = \''.pSQL($order['TrackingNumber']).'\'
										WHERE `id_order` = '.(int)$o['id']);
				if ($o['id'])
				{
					$psOrder = new Order($o['id']);
					if ($psOrder->id)
					{
						$history = new OrderHistory();
						$history->id_order = $o['id'];
						if (isset($o['shipped']) && $o['shipped'] == 'YES')
							$history->changeIdOrderState(Configuration::get('SHIPWIRE_SENT_ID'), $o['id']);

						$history->addWithemail();
					}
				}
			}
		}
	}

	if (Configuration::get('PS_CIPHER_ALGORITHM'))
		$cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
	else
		$cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);

	$shipWireInventoryUpdate = new ShipwireInventoryUpdate(Configuration::get('SHIPWIRE_API_USER'),
									$cipherTool->decrypt(Configuration::get('SHIPWIRE_API_PASSWD')));
	$shipWireInventoryUpdate->getInventory();

	if ($static)
		return true;
	else
		die('OK');

}
