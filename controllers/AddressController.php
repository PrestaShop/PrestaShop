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
*  @version  Release: $Revision: 7095 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if(Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php'))
	include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');

class AddressControllerCore extends FrontController
{
	public $auth = true;
	public $guestAllowed = true;
	public $php_self = 'address';
	public $authRedirection = 'addresses';
	public $ssl = true;

	protected $_address;

	public function preProcess()
	{
		parent::preProcess();
		if ($back = Tools::getValue('back'))
			$this->context->smarty->assign('back', Tools::safeOutput($back));
		if ($mod = Tools::getValue('mod'))
			$this->context->smarty->assign('mod', Tools::safeOutput($mod));

		if (Tools::isSubmit('ajax') AND Tools::isSubmit('type'))
		{
			if (Tools::getValue('type') == 'delivery')
				$id_address = isset($this->context->cart->id_address_delivery) ? (int)$this->context->cart->id_address_delivery : 0;
			elseif (Tools::getValue('type') == 'invoice')
				$id_address = (isset($this->context->cart->id_address_invoice) AND $this->context->cart->id_address_invoice != $this->context->cart->id_address_delivery) ? (int)$this->context->cart->id_address_invoice : 0;
			else
				exit;
		}
		else
			$id_address = (int)Tools::getValue('id_address', 0);

		if ($id_address)
		{
			$this->_address = new Address((int)$id_address);
			if (Validate::isLoadedObject($this->_address) AND Customer::customerHasAddress($this->context->customer->id, (int)($id_address)))
			{
				if (Tools::isSubmit('delete'))
				{
					if ($this->context->cart->id_address_invoice == $this->_address->id)
						unset($this->context->cart->id_address_invoice);
					if ($this->context->cart->id_address_delivery == $this->_address->id)
						unset($this->context->cart->id_address_delivery);
					if ($this->_address->delete())
						Tools::redirect('index.php?controller=addresses');
					$this->errors[] = Tools::displayError('This address cannot be deleted.');
				}
				$this->context->smarty->assign(array('address' => $this->_address, 'id_address' => (int)$id_address));
			}
			elseif (Tools::isSubmit('ajax'))
				exit;
			else
				Tools::redirect('index.php?controller=addresses');
		}
		if (Tools::isSubmit('submitAddress'))
		{
			$address = new Address();
			$this->errors = $address->validateController();
			$address->id_customer = (int)$this->context->customer->id;

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
			if ($country->isNeedDni() AND (!Tools::getValue('dni') OR !Validate::isDniLite(Tools::getValue('dni'))))
				$this->errors[] = Tools::displayError('Identification number is incorrect or has already been used.');
			elseif (!$country->isNeedDni())
				$address->dni = NULL;
			if (Configuration::get('PS_TOKEN_ENABLE') == 1 AND
				strcmp(Tools::getToken(false), Tools::getValue('token')) AND
				$this->context->customer->isLogged(true) === true)
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
					if (Validate::isLoadedObject($address_old) AND Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id))
					{
						if ($address_old->isUsed())
						{
							$address_old->delete();
							if (!Tools::isSubmit('ajax'))
							{
								$to_update = false;
								if ($this->context->cart->id_address_invoice == $address_old->id)
								{
									$to_update = true;
									$this->context->cart->id_address_invoice = 0;
								}
								if ($this->context->cart->id_address_delivery == $address_old->id)
								{
									$to_update = true;
									$this->context->cart->id_address_delivery = 0;
								}
								if ($to_update)
									$this->context->cart->update();
							}
						}
						else
						{
							$address->id = (int)($address_old->id);
							$address->date_add = $address_old->date_add;
						}
					}
				}
				elseif ($this->context->customer->is_guest)
					Tools::redirect('index.php?controller=addresses');

				if ($result = $address->save())
				{
					/* In order to select this new address : order-address.tpl */
					if ((bool)(Tools::getValue('select_address', false)) == true OR (Tools::isSubmit('ajax') AND Tools::getValue('type') == 'invoice'))
					{
						/* This new adress is for invoice_adress, select it */
						$this->context->cart->id_address_invoice = (int)($address->id);
						$this->context->cart->update();
					}
					if (Tools::isSubmit('ajax'))
					{
						$return = array(
							'hasError' => !empty($this->errors),
							'errors' => $this->errors,
							'id_address_delivery' => $this->context->cart->id_address_delivery,
							'id_address_invoice' => $this->context->cart->id_address_invoice
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
			if (Validate::isLoadedObject($this->context->customer))
			{
				$_POST['firstname'] = $this->context->customer->firstname;
				$_POST['lastname'] = $this->context->customer->lastname;
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
		$this->addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
	}

	public function process()
	{
		parent::process();

		/* Secure restriction for guest */
		if ($this->context->customer->is_guest)
			Tools::redirect('index.php?controller=addresses');

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

		if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
			$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
		else
			$countries = Country::getCountries($this->context->language->id, true);

		$countriesList = '';
		foreach ($countries AS $country)
			$countriesList .= '<option value="'.(int)($country['id_country']).'" '.($country['id_country'] == $selectedCountry ? 'selected="selected"' : '').'>'.htmlentities($country['name'], ENT_COMPAT, 'UTF-8').'</option>';

		if ((Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php')) && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT')))
			$this->context->smarty->assign('vat_display', 2);
		else if(Configuration::get('VATNUMBER_MANAGEMENT'))
			$this->context->smarty->assign('vat_display', 1);
		else
			$this->context->smarty->assign('vat_display', 0);

		$this->context->smarty->assign('ajaxurl', _MODULE_DIR_);

		$this->context->smarty->assign('vatnumber_ajax_call', (int)file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'));

		$this->context->smarty->assign(array(
			'countries_list' => $countriesList,
			'countries' => $countries,
			'errors' => $this->errors,
			'token' => Tools::getToken(false),
			'select_address' => (int)(Tools::getValue('select_address'))
		));
	}

	protected function _processAddressFormat()
	{

		$id_country = is_null($this->_address)? 0 : (int)$this->_address->id_country;

		$dlv_adr_fields = AddressFormat::getOrderedAddressFields($id_country, true, true);
		$this->context->smarty->assign('ordered_adr_fields', $dlv_adr_fields);
	}

	public function displayHeader()
	{
		if (Tools::getValue('ajax') != 'true')
			parent::displayHeader();
	}

	public function displayContent()
	{
		parent::displayContent();

		$this->_processAddressFormat();
		$this->context->smarty->display(_PS_THEME_DIR_.'address.tpl');
	}

	public function displayFooter()
	{
		if (Tools::getValue('ajax') != 'true')
			parent::displayFooter();
	}
}

