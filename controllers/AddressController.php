<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if(Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php'))
	include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');

class AddressControllerCore extends FrontController
{
	protected $_address;

	public function __construct()
	{
		$this->auth = true;
		$this->guestAllowed = true;
		$this->php_self = 'address.php';
		$this->authRedirection = 'addresses.php';
		$this->ssl = true;
	
		parent::__construct();
	}
	
	public function preProcess()
	{
		parent::preProcess();
		
		if ($back = Tools::getValue('back'))
			self::$smarty->assign('back', Tools::safeOutput($back));
		if ($mod = Tools::getValue('mod'))
			self::$smarty->assign('mod', Tools::safeOutput($mod));
		
		if (Tools::isSubmit('ajax') AND Tools::isSubmit('type'))
		{
			if (Tools::getValue('type') == 'delivery')
				$id_address = isset(self::$cart->id_address_delivery) ? (int)self::$cart->id_address_delivery : 0;
			elseif (Tools::getValue('type') == 'invoice')
				$id_address = (isset(self::$cart->id_address_invoice) AND self::$cart->id_address_invoice != self::$cart->id_address_delivery) ? (int)self::$cart->id_address_invoice : 0;
			else
				exit;
		}
		else
			$id_address = (int)Tools::getValue('id_address', 0);
		
		if ($id_address)
		{
			$this->_address = new Address((int)$id_address);
			if (Validate::isLoadedObject($this->_address) AND Customer::customerHasAddress((int)(self::$cookie->id_customer), (int)($id_address)))
			{
				if (Tools::isSubmit('delete'))
				{
					if (self::$cart->id_address_invoice == $this->_address->id)
						unset(self::$cart->id_address_invoice);
					if (self::$cart->id_address_delivery == $this->_address->id)
						unset(self::$cart->id_address_delivery);
					if ($this->_address->delete())
						Tools::redirect('addresses.php');
					$this->errors[] = Tools::displayError('This address cannot be deleted.');
				}
				self::$smarty->assign(array('address' => $this->_address, 'id_address' => (int)$id_address));
			}
			elseif (Tools::isSubmit('ajax'))
				exit;
			else
				Tools::redirect('addresses.php');
		}
		if (Tools::isSubmit('submitAddress'))
		{
			$address = new Address();
			$this->errors = $address->validateControler();
			$address->id_customer = (int)(self::$cookie->id_customer);

			if (!Tools::getValue('phone') AND !Tools::getValue('phone_mobile'))
				$this->errors[] = Tools::displayError('You must register at least one phone number');
			if (!$country = new Country((int)$address->id_country) OR !Validate::isLoadedObject($country))
				die(Tools::displayError());

			/* US customer: normalize the address */
			if($address->id_country == Country::getByIso('US'))
			{
				include_once(_PS_TAASC_PATH_.'AddressStandardizationSolution.php');
				$normalize = new AddressStandardizationSolution;
				$address->address1 = $normalize->AddressLineStandardization($address->address1);
				$address->address2 = $normalize->AddressLineStandardization($address->address2);
			}

			$zip_code_format = $country->zip_code_format;
			if ($country->need_zip_code)
			{
				if (($postcode = Tools::getValue('postcode')) AND $zip_code_format)
				{
					$zip_regexp = '/^'.$zip_code_format.'$/ui';
					$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
					$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
					$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
					$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
					$zip_regexp = str_replace('C', $country->iso_code, $zip_regexp);
					if (!preg_match($zip_regexp, $postcode))
						$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.').'<br />'.Tools::displayError('Must be typed as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
				}
				elseif ($zip_code_format)
					$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is required.');
				elseif ($postcode AND !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
						$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.').'<br />'.Tools::displayError('Must be typed as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
			}
			if ($country->isNeedDni() AND !Tools::getValue('dni') AND !Validate::isDniLite(Tools::getValue('dni')))
				$this->errors[] = Tools::displayError('Identification number is incorrect or has already been used.');
			elseif (!$country->isNeedDni())
				$address->dni = NULL;
			if (Configuration::get('PS_TOKEN_ENABLE') == 1 AND
				strcmp(Tools::getToken(false), Tools::getValue('token')) AND
				self::$cookie->isLogged(true) === true)
				$this->errors[] = Tools::displayError('Invalid token');

			if ((int)($country->contains_states) AND !(int)($address->id_state))
				$this->errors[] = Tools::displayError('This country requires a state selection.');
			
			if (!sizeof($this->errors))
			{
				if (isset($id_address))
				{
					$country = new Country((int)($address->id_country));
					if (Validate::isLoadedObject($country) AND !$country->contains_states)
						$address->id_state = 0;
					$address_old = new Address((int)$id_address);
					if (Validate::isLoadedObject($address_old) AND Customer::customerHasAddress((int)self::$cookie->id_customer, (int)$address_old->id))
					{
						if (!Tools::isSubmit('ajax'))
						{
							if (self::$cart->id_address_invoice == $address_old->id)
								unset(self::$cart->id_address_invoice);
							if (self::$cart->id_address_delivery == $address_old->id)
								unset(self::$cart->id_address_delivery);
						}
						
						if ($address_old->isUsed())
							$address_old->delete();
						else
						{
							$address->id = (int)($address_old->id);
							$address->date_add = $address_old->date_add;
						}
					}
				}
				elseif (self::$cookie->is_guest)
					Tools::redirect('addresses.php');
				
				if ($result = $address->save())
				{
					if ((bool)(Tools::getValue('select_address', false)) == true OR (Tools::isSubmit('ajax') AND Tools::getValue('type') == 'invoice'))
					{
						/* This new adress is for invoice_adress, select it */
						self::$cart->id_address_invoice = (int)($address->id);
						self::$cart->update();
					}
					if (Tools::isSubmit('ajax'))
					{
						$return = array(
							'hasError' => !empty($this->errors), 
							'errors' => $this->errors,
							'id_address_delivery' => self::$cart->id_address_delivery,
							'id_address_invoice' => self::$cart->id_address_invoice
						);
						die(Tools::jsonEncode($return));
					}
					Tools::redirect($back ? ($mod ? $back.'&back='.$mod : $back) : 'addresses.php');
				}
				$this->errors[] = Tools::displayError('An error occurred while updating your address.');
			}
		}
		elseif (!$id_address)
		{
			$customer = new Customer((int)(self::$cookie->id_customer));
			if (Validate::isLoadedObject($customer))
			{
				$_POST['firstname'] = $customer->firstname;
				$_POST['lastname'] = $customer->lastname;
			}
		}
		if (Tools::isSubmit('ajax') AND sizeof($this->errors))
		{
			$return = array(
				'hasError' => !empty($this->errors), 
				'errors' => $this->errors
			);
			die(Tools::jsonEncode($return));
		}
	}
	
	public function setMedia()
	{
		parent::setMedia();
		Tools::addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
	}
	
	public function process()
	{
		parent::process();

		/* Secure restriction for guest */
		if (self::$cookie->is_guest)
			Tools::redirect('addresses.php');

		if (Tools::isSubmit('id_country') AND Tools::getValue('id_country') != NULL AND is_numeric(Tools::getValue('id_country')))
			$selectedCountry = (int)Tools::getValue('id_country');
		elseif (isset($this->_address) AND isset($this->_address->id_country) AND !empty($this->_address->id_country) AND is_numeric($this->_address->id_country))
			$selectedCountry = (int)$this->_address->id_country;
		elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$array = preg_split('/,|-/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			if (!Validate::isLanguageIsoCode($array[0]) OR !($selectedCountry = Country::getByIso($array[0])))
				$selectedCountry = (int)Configuration::get('PS_COUNTRY_DEFAULT');
		}
		else
			$selectedCountry = (int)Configuration::get('PS_COUNTRY_DEFAULT');
			
		$countries = Country::getCountries((int)self::$cookie->id_lang, true);
		$countriesList = '';
		foreach ($countries AS $country)
			$countriesList .= '<option value="'.(int)($country['id_country']).'" '.($country['id_country'] == $selectedCountry ? 'selected="selected"' : '').'>'.htmlentities($country['name'], ENT_COMPAT, 'UTF-8').'</option>';

		if ((Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php')) && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT')))
			self::$smarty->assign('vat_display', 2);
		else if(Configuration::get('VATNUMBER_MANAGEMENT'))
			self::$smarty->assign('vat_display', 1);
		else
			self::$smarty->assign('vat_display', 0);

		self::$smarty->assign('ajaxurl', _MODULE_DIR_);
		self::$smarty->assign(array(
			'countries_list' => $countriesList,
			'countries' => $countries,
			'errors' => $this->errors,
			'token' => Tools::getToken(false),
			'select_address' => (int)(Tools::getValue('select_address'))
		));
	}
	
	public function displayHeader()
	{
		if (Tools::getValue('ajax') != 'true')
			parent::displayHeader();
	}
	
	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'address.tpl');
	}
	
	public function displayFooter()
	{
		if (Tools::getValue('ajax') != 'true')
			parent::displayFooter();
	}
}

