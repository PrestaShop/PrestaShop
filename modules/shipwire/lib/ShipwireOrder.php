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

/* Security */
if (!defined('_PS_VERSION_'))
	exit;

class ShipwireOrder extends ShipwireApi
{
	protected $_apiType = 'fulfillmentServices';

	public function __construct()
	{
		parent::__construct();

		$this->_configVars['SHIPWIRE_ACCOUNT_NAME'] = '';

		$this->_xml['header'] = array(
			'<OrderList>',// StorAccountName="'.$this->_configVars['SHIPWIRE_ACCOUNT_NAME'].'">',
			'<Username>'.$this->_configVars['SHIPWIRE_API_USER'].'</Username>',
			'<Password>'.$this->_configVars['SHIPWIRE_API_PASSWD'].'</Password>',
			'<Server>'.$this->_configVars['SHIPWIRE_API_MODE'].'</Server>',
			'<AffiliateId>7403</AffiliateId>',
		);
		$this->_xml['body'][] = '';
		$this->_xml['footer'][] = '</OrderList>';
	}

	public function addOrder($values, $refresh = false)
	{
		foreach ($values as &$v)
			if (!empty($v) && !is_array($v))
				$v = pSQL($v);

		if (!$refresh)
			$this->_xml['body'] = array(
				'<Order id="'.$values['orderId'].'">',
				'<Warehouse>'.$this->_configVars['SHIPWIRE_WAREHOUSE'].'</Warehouse>',
//				'<SameDay>'.$values['sameDay'].'</SameDay>',
				'<AddressInfo type="ship">',
				'<Name><Full>'.$values['name'].'</Full></Name>',
				'<Address1>'.$values['address1'].'</Address1>',
				'<Address2>'.$values['address2'].'</Address2>',
				'<City>'.$values['city'].'</City>',
				'<Country>'.$values['country'].'</Country>',
				'<Zip>'.$values['zip'].'</Zip>',
				'<Phone>'.$values['phone'].'</Phone>',
				'<Email>'.$values['mail'].'</Email>',
				'</AddressInfo>',
				'<Shipping>'.$values['shippingType'].'</Shipping>',
			);
		else
			$this->_xml['body'] = array('<Order id="'.(int)$values['orderId'].'">');

		$i = 0;
		foreach ($values['packageList'] as $item)
		{
			$this->_xml['body'][] = '<Item num="'.($i++).'">';
			$this->_xml['body'][] = '<Code>'.$item['code'].'</Code>';
			$this->_xml['body'][] = '<Quantity>'.$item['quantity'].'</Quantity>';
			$this->_xml['body'][] = '</Item>';
		}

		$this->_xml['body'][] = '</Order>';
	}

}
