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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/*
 * Interface
 */
require_once(dirname(__FILE__).'/IMondialRelayWSMethod.php');

/*
 * Allow to create tickets - 'WSI2_CreationEtiquette'
 */
class MRCreateTickets implements IMondialRelayWSMethod
{
	private $_fields	= array(
		'id_mr_selected' => 0,
		'list' => array(
			'Enseigne'			=> array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#'),
			'ModeCol' 			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^(CCC|CDR|CDS|REL)$#'),
			'ModeLiv'				=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^(LCC|LD1|LDS|24R|ESP|DRI)$#'),
			'NDossier' 			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|[0-9A-Z_ -]{0,15})$#'),
			'NClient' 			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|[0-9A-Z]{0,9})$#'),
			'Expe_Langage'	=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'Expe_Ad1'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{2,32}$#'),
			'Expe_Ad2'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{0,32}$#'),
			'Expe_Ad3'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{0,32}$#'),
			'Expe_Ad4'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#'),
			'Expe_Ville'		=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z_\-\' ]{2,26}$#'),
			'Expe_CP'				=>  array(
						'required'				=> true,
						'value'						=> '',
						'params'					=> array(),
						'methodValidation' => 'checkZipcodeByCountry'),
			'Expe_Pays'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'Expe_Tel1'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,8}$#'),
			'Expe_Tel2'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,8}$#'),
			'Expe_Mail'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[\w\-\.\@_]{0,70}$#'),
			'Dest_Langage'	=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'Dest_Ad1'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{2,32}$#'),
			'Dest_Ad2'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{2,32}$#'),
			'Dest_Ad3'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{2,32}$#'),
			'Dest_Ad4'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{0,32}$#'),
			'Dest_Ville'		=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z_\-\' ]{2,26}$#'),
			'Dest_CP'				=>  array(
						'required'				=> true,
						'value'						=> '',
						'params'					=> array(),
						'methodValidation' => 'checkZipcodeByCountry'),
			'Dest_Pays'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'Dest_Tel1'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,8}$#'),
			'Dest_Tel2'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,8}$#'),
			'Dest_Mail'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[\w\-\.\@_]{0,70}$#'),
			'Poids'					=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9]{3,7}$#'),
			'Longueur'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9]{0,3}$#'),
			'Taille'				=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(XS|S|M|L|XL|XXL|3XL)$#'),
			'NbColis'				=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9]{1,2}$#'),
			'CRT_Valeur'		=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9]{1,7}$#'),
			'CRT_Devise'		=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|EUR)$#'),
			'Exp_Valeur'		=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9]{0,7}$#'),
			'Exp_Devise'		=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|EUR)$#'),
			'COL_Rel_Pays'	=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'COL_Rel'				=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|[0-9]{6})$#'),
			'LIV_Rel_Pays'	=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[A-Z]{2}$#'),
			'LIV_Rel'				=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|[0-9]{6})$#'),
			'TAvisage'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|O|N)$#'),
			'TReprise'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|O|N)$#'),
			'Montage'				=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|[0-9]{1,3})$#'),
			'TRDV'					=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|O|N)$#'),
			'Assurance'			=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^(|[0-9A-Z]{1})$#'),
			'Instructions'	=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z_\-\'., /]{0,31}#'),
			'Security'			=>  array(
						'required'				=> true,
						'value'						=> '',
						'regexValidation' => '#^[0-9A-Z]{32}$#'),
			'Texte'					=>  array(
						'required'				=> false,
						'value'						=> '',
						'regexValidation' => '#^([^<>&\']{3,30})(\(cr\)[^<>&\']{0,30}){0,9}$#')));

	private $_orderListId = NULL;
	private $_totalOrder = 0;
	private $_weightList = NULL;
	private $_mondialRelay = NULL;
	private $_fieldsList = array();
	private $_webServiceKey =	'';
	private $_markCode = '';

	private $_resultList = array(
		'error' => array(),
		'success' => array());

	private $_webserviceURL = 'http://www.mondialrelay.fr/webservice/Web_Services.asmx?WSDL';

	public function __construct($params)
	{
		$this->_orderListId = $params['orderIdList'];
		$this->_totalOrder = $params['totalOrder'];
		$this->_weightList = $params['weightList'];
		$this->_webServiceKey = Configuration::get('MR_KEY_WEBSERVICE');
		$this->_markCode = Configuration::get('MR_CODE_MARQUE');
	}

	public function __destruct()
	{
		 unset($this->_mondialRelay);
	}

	/*
	 * Build a correct weight format (NNNNN)
	 */
	private function _weightFormat($weight)
	{
		while (strlen($weight) != 5)
			$weight = '0'.$weight;
		return $weight;
	}

	/*
	 * Set the default value to the order paramaters
	 */
	private function _setRequestDefaultValue()
	{
		$this->_fields['list']['Enseigne']['value'] = Configuration::get('MR_ENSEIGNE_WEBSERVICE');
		$this->_fields['list']['Expe_Langage']['value'] = Configuration::get('MR_LANGUAGE');
		$this->_fields['list']['Expe_Ad1']['value'] = Configuration::get('PS_SHOP_NAME');
		$this->_fields['list']['Expe_Ad3']['value'] = Configuration::get('PS_SHOP_ADDR1');
		// Deleted, cause to many failed for the process
		// $this->_fields['list']['Expe_Ad4']['value'] = Configuration::get('PS_SHOP_ADDR2');
		$this->_fields['list']['Expe_Ville']['value'] = Configuration::get('PS_SHOP_CITY');
		$this->_fields['list']['Expe_CP']['value'] = Configuration::get('PS_SHOP_CODE');
		$this->_fields['list']['Expe_CP']['params']['id_country'] = Configuration::get('PS_COUNTRY_DEFAULT');

		if (_PS_VERSION_ >= '1.4')
		$this->_fields['list']['Expe_Pays']['value'] = Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID'));
		else
			$this->_fields['list']['Expe_Pays']['value'] = substr(Configuration::get('PS_SHOP_COUNTRY'), 0, 2);

		$this->_fields['list']['Expe_Tel1']['value'] = str_replace(array('.', ' ', '-'), '', Configuration::get('PS_SHOP_PHONE'));
		$this->_fields['list']['Expe_Mail']['value'] = Configuration::get('PS_SHOP_EMAIL');
		$this->_fields['list']['NbColis']['value'] = 1;
		$this->_fields['list']['CRT_Valeur']['value'] = 0;
		$this->_fields['list']['CRT_Devise']['value'] = 'EUR';
	}

	/*
	 * Initiate the data needed to be send properly
	 * Can manage a list of data for multiple request
	 */
	public function init()
	{
		$this->_mondialRelay = new MondialRelay();

		if ($this->_totalOrder == 0)
			throw new Exception($this->_mondialRelay->l('Please select at least one order'));

		$this->_setRequestDefaultValue();
		if (count($orderListDetails = $this->_mondialRelay->getOrders($this->_orderListId)))
		{
			foreach ($orderListDetails as $orderDetail)
			{
				// Storage temporary
				$base = $this->_fields;
				$tmp = &$base['list'];

				$deliveriesAddress = new Address($orderDetail['id_address_delivery']);
				$customer = new Customer($orderDetail['id_customer']);

				// Store the weight order set by the user
				foreach($this->_weightList as $orderWeightInfos)
				{
					$detail = explode('-', $orderWeightInfos);
					if (count($detail) == 2 && $detail[1] == $orderDetail['id_order'])
						$tmp['Poids']['value'] = $this->_weightFormat($detail[0]);
				}

				$destIsoCode = Country::getIsoById($deliveriesAddress->id_country);
				$tmp['ModeCol']['value'] = $orderDetail['mr_ModeCol'];
				$tmp['ModeLiv']['value'] = $orderDetail['mr_ModeLiv'];
				$tmp['NDossier']['value'] = $orderDetail['id_order'];
				$tmp['NClient']['value'] = $orderDetail['id_customer'];
				$tmp['Dest_Langage']['value'] = 'FR'; //Language::getIsoById($orderDetail['id_lang']);
				$tmp['Dest_Ad1']['value'] = $deliveriesAddress->firstname.' '.$deliveriesAddress->lastname;
				$tmp['Dest_Ad2']['value'] = $deliveriesAddress->address2;
				$tmp['Dest_Ad3']['value'] = $deliveriesAddress->address1;
				$tmp['Dest_Ville']['value'] = $deliveriesAddress->city;
				$tmp['Dest_CP']['value'] = $deliveriesAddress->postcode;
				$tmp['Dest_CP']['params']['id_country'] = $deliveriesAddress->id_country;
				$tmp['Dest_Pays']['value'] = $destIsoCode;
				$tmp['Dest_Tel1']['value'] = $deliveriesAddress->phone;
				$tmp['Dest_Tel2']['value'] = $deliveriesAddress->phone_mobile;
				$tmp['Dest_Mail']['value'] = $customer->email;
				$tmp['Assurance']['value'] = $orderDetail['mr_ModeAss'];
				if ($orderDetail['MR_Selected_Num'] != 'LD1' && $orderDetail['MR_Selected_Num'] != 'LDS')
				{
					$tmp['LIV_Rel_Pays']['value'] = $orderDetail['MR_Selected_Pays'];
					$tmp['LIV_Rel']['value'] = $orderDetail['MR_Selected_Num'];
				}

				// Store the necessary information to the root case table
				$base['id_mr_selected'] = $orderDetail['id_mr_selected'];

				// Add the temporary values to a field list for multiple request
				$this->_fieldsList[] = $base;
				unset($deliveriesAddress);
				unset($customer);
			}
			$this->_generateMD5SecurityKey();
		}
	}

	/*
	 * Generate the MD5 key for each param list
	 */
	private function _generateMD5SecurityKey()
	{
		// RootCase is the array case where the main information are stored
		// it's an array containing id_mr_selected and an array with the necessary fields
		foreach($this->_fieldsList as &$rootCase)
		{
			$concatenationValue = '';
			foreach($rootCase['list'] as $paramName => &$valueDetailed)
				if ($paramName != 'Texte' && $paramName != 'Security')
				{
					// Mac server make an empty string instead of a cleaned string
					// TODO : test on windows and linux server
					$cleanedString = MRTools::replaceAccentedCharacters($valueDetailed['value']);
					$valueDetailed['value'] = !empty($cleanedString) ? strtoupper($cleanedString) : strtoupper($valueDetailed['value']);

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
						if (empty($valueDetailed['value']))
							$error = $this->_mondialRelay->l('This key').' ['.$paramName.'] '.$this->_mondialRelay->l('is empty and need to be filled');
						else
							$error = 'This key ['.$paramName.'] hasn\'t a valid value format : '.$valueDetailed['value'];
						$this->_resultList['error'][$rootCase['list']['NDossier']['value']][] = $error;
					}
				}
			$concatenationValue .= $this->_webServiceKey;
			$rootCase['list']['Security']['value'	] = strtoupper(md5($concatenationValue));
		}
	}

	/*
	 * Update the tables used and send mail with the order history
	 */
	private function _updateTable($params, $expeditionNum, $ticketURL, $trackingURL, $id_mr_selected)
	{
		Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'mr_selected`
			SET `MR_poids` = \''.pSQL($params['Poids']).'\',
					`exp_number` = \''.pSQL($expeditionNum).'\',
					`url_etiquette` = \''.pSQL($ticketURL).'\',
					`url_suivi` = \''.pSQL($trackingURL).'\'
			WHERE id_mr_selected = '.(int)$id_mr_selected);

		// NDossier contains the id_order
		$order = new Order($params['NDossier']);

	 	// Update the database for order and orderHistory
		$order->shipping_number = $expeditionNum;
		$order->update();

		$templateVars = array('{followup}' => $trackingURL);
		$orderState = (Configuration::get('PS_OS_SHIPPING')) ?
			Configuration::get('PS_OS_SHIPPING') :
			_PS_OS_SHIPPING_;

		$history = new OrderHistory();
		$history->id_order = (int)$params['NDossier'];
		$history->changeIdOrderState($orderState, (int)$params['NDossier']);
		$history->id_employee = (int)Context::getContext()->employee->id;
		$history->addWithemail(true, $templateVars);

		unset($order);
		unset($history);
	}

	/*
	 * Manage the return value of the webservice, handle the errors or build the
	 * succeed message
	 */
	private function _parseResult($client, $result, $params, $id_mr_selected)
	{
		$errors = &$this->_resultList['error'][$params['NDossier']];
		$success = &$this->_resultList['success'][$params['NDossier']];

		if ($client->fault)
			$errors[] = $this->_mondialRelay->l('It seems the request isn\'t valid:').
				$result;

		$result = $result['WSI2_CreationEtiquetteResult'];
		if (($errorNumber = $result['STAT']) != 0)
		{
			$errors[] = $this->_mondialRelay->l('There is an error number : ').$errorNumber;
			$errors[] = $this->_mondialRelay->l('Details : ').
				$this->_mondialRelay->getErrorCodeDetail($errorNumber);
		}
		else
		{
			$baseURL = 'http://www.mondialrelay.fr/';
			$expedition = $result['ExpeditionNum'];
			$securityKey = strtoupper(md5('<'.$params['Enseigne'].$this->_markCode.
				'>'.$expedition.'<'.$this->_webServiceKey.'>'));
			$ticketURL = $baseURL.$result['URL_Etiquette'];
			$trackingURL = $baseURL.
				'lg_fr/espaces/url/popup_exp_details.aspx?cmrq='.$params['Enseigne'].
				$this->_markCode.'&nexp='.$expedition.'&crc='.$securityKey;

			$success['displayExpedition'] = $this->_mondialRelay->l('Expedition Number : ') . $expedition;
			$success['displayTicketURL'] = $this->_mondialRelay->l('Ticket URL : ') . $ticketURL;
			$success['displayTrackingURL'] = $this->_mondialRelay->l('Tracking URL: ') . $trackingURL;
			$success['expeditionNumber'] = $expedition;

			$this->_updateTable($params, $expedition, $ticketURL, $trackingURL, $id_mr_selected);
		}
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

			foreach($this->_fieldsList as $rootCase)
			{
				$params = $this->_getSimpleParamArray($rootCase['list']);
				$result = $client->call(
					'WSI2_CreationEtiquette',
					$params,
					'http://www.mondialrelay.fr/webservice/',
					'http://www.mondialrelay.fr/webservice/WSI2_CreationEtiquette');

				$this->_parseResult($client, $result, $params, $rootCase['id_mr_selected']);
			}
			unset($client);
		}
		else
			throw new Exception($this->_mondialRelay->l('The Mondial Relay webservice isn\'t currently reliable'));
	}

	/*
	** Check if the shop parameter are currently well configured
	*/
	public function checkPreValidation()
	{
		$errorList = array('error' => array(), 'warn' => array());
		
		if (!$this->_mondialRelay)
			$this->_mondialRelay = new MondialRelay();
		
		$list = array(
			'Expe_Langage' => array(
				'value' => Configuration::get('MR_LANGUAGE'),
				'error' => $this->_mondialRelay->l('Please check your language configuration')),
			'Expe_Ad1' => array(
				'value' => Configuration::get('PS_SHOP_NAME'),
				'error' => $this->_mondialRelay->l('Please check your shop name configuration')),
			'Expe_Ad3' => array(
				'value' => Configuration::get('PS_SHOP_ADDR1'),
				'error' => $this->_mondialRelay->l('Please check your address 1 configuration')),
			'Expe_Ville' =>	array(
				'value' => Configuration::get('PS_SHOP_CITY'),
				'error' => $this->_mondialRelay->l('Please check your city configuration')),
			'Expe_CP' => array(
				'value' => Configuration::get('PS_SHOP_CODE'),
				'error' => $this->_mondialRelay->l('Please check your zipcode configuration'),
				'warn' => $this->_mondialRelay->l('It seems the layout of your zipcode country is not configured or you didn\'t set a right zipcode')),
			'Expe_Pays' => array(
				'value' => ((_PS_VERSION_ >= '1.4') ? 
					Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')) : 
					substr(Configuration::get('PS_SHOP_COUNTRY'), 0, 2)),
				'error' => $this->_mondialRelay->l('Please check your country configuration')),
			'Expe_Tel1' => array(
				'value' => str_replace(array('.', ' ', '-'), '', Configuration::get('PS_SHOP_PHONE')),
				'error' => $this->_mondialRelay->l('Please check your Phone configuration')),
			'Expe_Mail' => array(
				'value' => Configuration::get('PS_SHOP_EMAIL'),
				'error' => $this->_mondialRelay->l('Please check your mail configuration')));
		
		foreach($list as $name => $tab)
		{
			// Mac server make an empty string instead of a cleaned string
			// TODO : test on windows and linux server
			$cleanedString = MRTools::replaceAccentedCharacters($tab['value']);
			$tab['value'] = !empty($cleanedString) ? strtoupper($cleanedString) : strtoupper($tab['value']);
				
			if ($name == 'Expe_CP')
			{
				if (!($zipcodeError = MRTools::checkZipcodeByCountry($tab['value'], array(
						'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')))))
					$errorList['error'][$name] = $tab['error'];
				else if ($zipcodeError < 0)
					$errorList['warn'][$name] = $tab['warn'];
			}
			else if (isset($this->_fields['list'][$name]['regexValidation']) && 
					(!preg_match($this->_fields['list'][$name]['regexValidation'], $tab['value'], $matches)))
				$errorList['error'][$name] = $tab['error'];
		}
		return $errorList;
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
	 * Return the fields list
	 */
	public function getFieldsList()
	{
		return $this->_fieldsList['list'];
	}

	/*
	 * Return the result of one or multiple sent requests
	 */
	public function getResult()
	{
		return $this->_resultList;
	}

	/*
	 * Return which number order of the list is currently managed
	 */
	public static function getCurrentRequestUnderTraitment()
	{
		// TODO: Build a SQL Query to know how many request have been executed
	}
}
?>
