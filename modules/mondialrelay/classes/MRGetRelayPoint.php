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
*  @version  Release: $Revision: 7086 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/*
 * Interface
 */
require_once(dirname(__FILE__).'/IMondialRelayWSMethod.php');

/*
 * Allow to fetch relay point - 'WSI2_RecherchePointRelais'
 */
class MRGetRelayPoint implements IMondialRelayWSMethod
{
	// Params is required if you use a pointer function
	private $_fields = array(
		'list' => array(
			'Enseigne'			=> array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#'),
			'Pays'					=> array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'Ville'					=> array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[A-Z_\-\' ]{2,25}$#'),
			'CP'						=> array(
						'required'				=> false,
						'value'						=> '',
						'params'					=> array(),
						'methodValidation' => 'checkZipcodeByCountry'),
			'Taille'				=> array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(XS|S|M|L|XL|XXL|3XL)$#'),
			'Poids'					=> array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9]{1,6}$#'),
			'Action'				=> array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(REL|24R|ESP|DRI)$#'),			
			'Security'			=> array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z]{32}$#')));
	
	private $_id_address_delivery = 0;
	private $_weight = 0;
	private $_webServiceKey = '';
	private $_mondialRelay = NULL;
	private $_id_carrier = 0;
	private $_id_delivery_country = 0;
	
	private $_resultList = array(
		'error' => array(),
		'success' => array());
	
	private $_webserviceURL = 'http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL';
	
	public function __construct($params)	
	{	
		$this->_id_address_delivery = (int)($params['id_address_delivery']);
		$this->_id_carrier = (int)($params['id_carrier']);
		$this->_weight = (float)($params['weight']);	
		$this->_webServiceKey = Configuration::get('MR_KEY_WEBSERVICE');
	}
	
	public function __destruct()
	{
		 unset($this->_mondialRelay);
	}
	
	public function init()
	{	
		$this->_mondialRelay = new MondialRelay();
		$address = new Address($this->_id_address_delivery);
		
		if (!$address)
			throw new Exception($this->_mondialrelay->l('Customer address can\'t be found'));
		
		$this->_fields['list']['Enseigne']['value'] = Configuration::get('MR_ENSEIGNE_WEBSERVICE');
		$this->_fields['list']['Poids']['value'] = Configuration::get('MR_WEIGHT_COEF') * $this->_weight;
		$this->_fields['list']['Pays']['value'] = Country::getIsoById($address->id_country);
		$this->_fields['list']['Ville']['value'] = $address->city;
		$this->_fields['list']['CP']['value'] = $address->postcode;
		$this->_fields['list']['CP']['params']['id_country'] = $address->id_country;
		
		$this->_generateMD5SecurityKey();
		unset($address);
	}
	
	/*
	 * Generate the MD5 key for each param list
	 */
	private function _generateMD5SecurityKey()
	{
		$concatenationValue = '';
		foreach($this->_fields['list'] as $paramName => &$valueDetailed)
			if ($paramName != 'Texte' && $paramName != 'Security')
			{
				$valueDetailed['value'] = strtoupper($valueDetailed['value']);
				// Call a pointer function if exist to do different test
				if (isset($valueDetailed['methodValidation']) &&
						method_exists('MRTools', $valueDetailed['methodValidation']) && 
						isset($valueDetailed['params']) && 
						MRTools::$valueDetailed['methodValidation']($valueDetailed['value'], $valueDetailed['params']))
					$concatenationValue .= $valueDetailed['value'];
				// Use simple Regex test given by MondialRelay
				else if (isset($valueDetailed['regexValidation']) &&
						preg_match($valueDetailed['regexValidation'], $valueDetailed['value'], $matches))
					$concatenationValue .= $valueDetailed['value'];
				// If the key is required, we set an error, else it's skipped 
				else if ((!strlen($valueDetailed['value']) && $valueDetailed['required']) || strlen($valueDetailed['value']))
				{
					$error = $this->_mondialRelay->l('This key').' ['.$paramName.'] '.
						$this->_mondialRelay->l('hasn\'t a valide value format').' : '.$valueDetailed['value'];
					$this->_resultList['error'][] = $error;
				}
			}
			$concatenationValue .= $this->_webServiceKey;
			$this->_fields['list']['Security']['value'	] = strtoupper(md5($concatenationValue));	
	}

	/*
	 * Get the values with associated fields name
	 * @fields : array containing multiple values information
	 */
	private function _getSimpleParamArray($fields)
	{
		$params = array();
		
		foreach($fields as $keyName => $valueDetailed)
			$params[$keyName] = $valueDetailed['value'];
		return $params;
	}
	
	/*
	 * Manage the return value of the webservice, handle the errors or build the
	 * succeed message
	 */
	private function _parseResult($client, $result, $params)
	{
		$errors = array();
		$success = array();
		
		if ($client->fault)
			$errors[$errorTotal++] = $this->_mondialRelay->l('It seems the request isn\'t valid:').
				$result;
		
		$result = $result['WSI2_RecherchePointRelaisResult'];
		if (($errorNumber = $result['STAT']) != 0)
		{
			$errors[] = $this->_mondialRelay->l('There is an error number : ').$errorNumber;
			$errors[] = $this->_mondialRelay->l('Details : ').
			$this->_mondialRelay->getErrorCodeDetail($errorNumber);
		}
		else
		{
			unset($result['STAT']);
			
			// Clean Content
			foreach($result as &$relayPoint)
				foreach ($relayPoint as $key => &$value)
 					$value = trim($value);
			$success = $result;
		}
		$this->_resultList['error'] = $errors;
		$this->_resultList['success'] = $success;
	}
	
	/*
	* Send one or multiple request to the webservice
	*/
	public function send()
	{
		if ($client = new nusoap_client($this->_webserviceURL, true))
		{
			$client->soap_defencoding = 'UTF-8';
			$client->decode_utf8 = false;
			
			$params = $this->_getSimpleParamArray($this->_fields['list']);
			$result = $client->call(
				'WSI2_RecherchePointRelais', 
				$params, 
				'http://www.mondialrelay.fr/webservice/', 
				'http://www.mondialrelay.fr/webservice/WSI2_RecherchePointRelais');
				
			$this->_parseResult($client, $result, $params);
			unset($client);
		}
		else
			throw new Exception($this->_mondialRelay->l('The Mondial Relay webservice isn\'t currently reliable'));
	}
	
	/*
	 * Get the values with associated fields name
	 */
	public function getFieldsList()
	{
		return $this->_fieldsList['list'];
	}
	
	/*
	 * Get the result of one or multiple send request
	 */
	public function getResult()
	{
		return $this->_resultList;
	}
}