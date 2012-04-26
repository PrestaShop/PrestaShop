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

class ShipwireApi
{
	protected $_configVars = array(
		'SHIPWIRE_API_SERVER' => '',
		'SHIPWIRE_API_USER' => '',
		'SHIPWIRE_API_PASSWD' => '',
		'SHIPWIRE_API_CONNECTED' => '',
		'SHIPWIRE_API_MODE' => '',
		'SHIPWIRE_WAREHOUSE' => '',
		'SHIPWIRE_ACCOUNT_NAME' => '',
		'SHIPWIRE_TRACKING_LAST_DATE' => '',
		'SHIPWIRE_COMMIT_ID' => '',
		'SHIPWIRE_SENT_ID' => '',
		'SHIPWIRE_DELIVERED_ID' => '',
	);

	/** @var _xml Array with header body and footer of the final xml */
	public $_xml = array();

	/** @var _mode Sets the connection mode: Test | Production */
	protected $_mode = 'Production';

	/** @var _urlConn URL to connect to at Shipwire's end */

	protected $_url = array(
		'inventoryUpdate' => array(
			'Test' => 'https://api.beta.shipwire.com/exec/InventoryServices.php',
			'Production' => 'https://api.shipwire.com/exec/InventoryServices.php',
			'prefix' => 'InventoryUpdateXML',
		),
		'fulfillmentServices' => array(
			'Test' => 'https://api.beta.shipwire.com/exec/FulfillmentServices.php',
			'Production' => 'https://api.shipwire.com/exec/FulfillmentServices.php',
			'prefix' => 'OrderListXML',
		),
		'trackingUpdate' => array(
			'Test' => 'https://api.beta.shipwire.com/exec/TrackingServices.php',
			'Production' => 'https://api.shipwire.com/exec/TrackingServices.php',
			'prefix' => 'TrackingUpdateXML',
		),
	);
	
	// protected $_url = array(
		// 'inventoryUpdate' => array(
			// 'Test' => 'https://api.beta.shipwire.com/exec/InventoryServices.php',
			// 'Production' => 'https://api.beta.shipwire.com/exec/InventoryServices.php',
			// 'prefix' => 'InventoryUpdateXML',
		// ),
		// 'fulfillmentServices' => array(
			// 'Test' => 'https://api.beta.shipwire.com/exec/FulfillmentServices.php',
			// 'Production' => 'https://api.beta.shipwire.com/exec/FulfillmentServices.php',
			// 'prefix' => 'OrderListXML',
		// ),
		// 'trackingUpdate' => array(
			// 'Test' => 'https://api.beta.shipwire.com/exec/TrackingServices.php',
			// 'Production' => 'https://api.beta.shipwire.com/exec/TrackingServices.php',
			// 'prefix' => 'TrackingUpdateXML',
		// ),
	// );

	public function __construct()
	{
		foreach ($this->_configVars as $key => $v)
			$this->_configVars[$key] = Configuration::get($key);

		if (Configuration::get('PS_CIPHER_ALGORITHM'))
			$this->_cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		else
			$this->_cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);

		$this->_configVars['SHIPWIRE_API_PASSWD'] = Tools::safeOutput($this->_cipherTool->decrypt($this->_configVars['SHIPWIRE_API_PASSWD']));
		$this->_configVars['SHIPWIRE_API_MODE'] = $this->_mode;
	}

	public function sendData($xmlTagFields = array())
	{
		$xmlDocument = $this->_buildXMLDoc();

		$urlConn = new SWCurl($this->_url[$this->_apiType][$this->_mode], false);
		$urlConn->setCustomPost($this->_url[$this->_apiType]['prefix'].'='.urlencode($xmlDocument));

		$action = $urlConn->send();

		$xml = simplexml_load_string($urlConn->getLastResponse());
		$json = json_encode($xml);

		return json_decode($json,TRUE);
	}

	private function _buildXMLDoc()
	{
		$xmlDocument = '';
		foreach ($this->_xml['header'] as $header)
			$xmlDocument .= $header;
		foreach ($this->_xml['body'] as $body)
			$xmlDocument .= $body;
		foreach ($this->_xml['footer'] as $footer)
			$xmlDocument .= $footer;

		return $xmlDocument;
	}
}
