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
*  @version  Release: $Revision: 7499 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AuthControllerCore extends FrontController
{
	public $ssl = true;
	public $php_self = 'authentication';

	public function init()
	{
		parent::init();

		if ($this->context->customer->isLogged() AND !Tools::isSubmit('ajax'))
			Tools::redirect('index.php?controller=my-account');

		if (Tools::getValue('create_account'))
		{
			$create_account = 1;
			$this->context->smarty->assign('email_create', 1);
		}

		if (Tools::isSubmit('SubmitCreate'))
		{
			if (!Validate::isEmail($email = Tools::getValue('email_create')) OR empty($email))
				$this->errors[] = Tools::displayError('Invalid e-mail address');
			elseif (Customer::customerExists($email))
			{
				$this->errors[] = Tools::displayError('An account is already registered with this e-mail, please fill in the password or request a new one.');
				$_POST['email'] = $_POST['email_create'];
				unset($_POST['email_create']);
			}
			else
			{
				$create_account = 1;
				$this->context->smarty->assign('email_create', Tools::safeOutput($email));
				$_POST['email'] = $email;

			}
		}

		if (Tools::isSubmit('submitAccount') OR Tools::isSubmit('submitGuestAccount'))
		{
			$create_account = 1;
			if (Tools::isSubmit('submitAccount'))
				$this->context->smarty->assign('email_create', 1);
			/* New Guest customer */
			if (!Tools::getValue('is_new_customer', 1) AND !Configuration::get('PS_GUEST_CHECKOUT_ENABLED'))
				$this->errors[] = Tools::displayError('You cannot create a guest account.');
			if (!Tools::getValue('is_new_customer', 1))
				$_POST['passwd'] = md5(time()._COOKIE_KEY_);
			if (isset($_POST['guest_email']) AND $_POST['guest_email'])
				$_POST['email'] = $_POST['guest_email'];

			/* Preparing customer */
			$customer = new Customer();
			$lastnameAddress = $_POST['lastname'];
			$firstnameAddress = $_POST['firstname'];
			$_POST['lastname'] = $_POST['customer_lastname'];
			$_POST['firstname'] = $_POST['customer_firstname'];
			if (!Tools::getValue('phone') AND !Tools::getValue('phone_mobile'))
				$this->errors[] = Tools::displayError('You must register at least one phone number');

			if (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) && !(Tools::getValue('months') == '' && Tools::getValue('days') == '' && Tools::getValue('years') == ''))
				$this->errors[] = Tools::displayError('Invalid date of birth');
			$customer->birthday = (empty($_POST['years']) ? '' : (int)$_POST['years'].'-'.(int)$_POST['months'].'-'.(int)$_POST['days']);

			$this->errors = array_unique(array_merge($this->errors, $customer->validateController()));
			/* Preparing address */
			$address = new Address();
			$_POST['lastname'] = $lastnameAddress;
			$_POST['firstname'] = $firstnameAddress;
			$address->id_customer = 1;
			$this->errors = array_unique(array_merge($this->errors, $address->validateController()));

			/* US customer: normalize the address */
			if($address->id_country == Country::getByIso('US'))
			{
				include_once(_PS_TAASC_PATH_.'AddressStandardizationSolution.php');
				$normalize = new AddressStandardizationSolution;
				$address->address1 = $normalize->AddressLineStandardization($address->address1);
				$address->address2 = $normalize->AddressLineStandardization($address->address2);
			}

			$zip_code_format = Country::getZipCodeFormat((int)(Tools::getValue('id_country')));
			if (Country::getNeedZipCode((int)(Tools::getValue('id_country'))))
			{
				if (($postcode = Tools::getValue('postcode')) AND $zip_code_format)
				{
					$zip_regexp = '/^'.$zip_code_format.'$/ui';
					$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
					$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
					$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
					$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
					$zip_regexp = str_replace('C', Country::getIsoById((int)(Tools::getValue('id_country'))), $zip_regexp);
					if (!preg_match($zip_regexp, $postcode))
						$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.').'<br />'.Tools::displayError('Must be typed as follows:').' '.str_replace('C', Country::getIsoById((int)(Tools::getValue('id_country'))), str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
				}
				elseif ($zip_code_format)
					$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is required.');
				elseif ($postcode AND !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
					$this->errors[] = '<strong>'.Tools::displayError('Zip/ Postal code').'</strong> '.Tools::displayError('is invalid.');
			}
			if (Country::isNeedDniByCountryId($address->id_country) AND (!Tools::getValue('dni') OR !Validate::isDniLite(Tools::getValue('dni'))))
				$this->errors[] = Tools::displayError('Identification number is incorrect or has already been used.');
			elseif (!Country::isNeedDniByCountryId($address->id_country))
				$address->dni = NULL;

			if (!$this->errors)
			{
				if (Customer::customerExists(Tools::getValue('email')))
					$this->errors[] = Tools::displayError('An account is already registered with this e-mail, please fill in the password or request a new one.');
				if (Tools::isSubmit('newsletter'))
				{
					$customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
					$customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));
				}

				if (!sizeof($this->errors))
				{
					if (!$country = new Country($address->id_country, Configuration::get('PS_LANG_DEFAULT')) OR !Validate::isLoadedObject($country))
						die(Tools::displayError());
					if ((int)($country->contains_states) AND !(int)($address->id_state))
						$this->errors[] = Tools::displayError('This country requires a state selection.');
					else
					{
						$customer->active = 1;
						/* New Guest customer */
						if (Tools::isSubmit('is_new_customer'))
							$customer->is_guest = !Tools::getValue('is_new_customer', 1);
						else
							$customer->is_guest = 0;
						if (!$customer->add())
							$this->errors[] = Tools::displayError('An error occurred while creating your account.');
						else
						{
							$address->id_customer = (int)($customer->id);
							if (!$address->add())
								$this->errors[] = Tools::displayError('An error occurred while creating your address.');
							else
							{
								if (!$customer->is_guest)
								{
									if (!Mail::Send($this->context->language->id, 'account', Mail::l('Welcome!'),
									array('{firstname}' => $customer->firstname, '{lastname}' => $customer->lastname, '{email}' => $customer->email, '{passwd}' => Tools::getValue('passwd')), $customer->email, $customer->firstname.' '.$customer->lastname))
										$this->errors[] = Tools::displayError('Cannot send email');
								}
								$this->context->smarty->assign('confirmation', 1);
								$this->context->cookie->id_customer = (int)($customer->id);
								$this->context->cookie->customer_lastname = $customer->lastname;
								$this->context->cookie->customer_firstname = $customer->firstname;
								$this->context->cookie->passwd = $customer->passwd;
								$this->context->cookie->logged = 1;
								$customer->logged = 1;
								$this->context->cookie->email = $customer->email;
								$this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
								/* Update cart address */
								$this->context->cart->secure_key = $customer->secure_key;
								$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
								$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));

								// If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
								$this->context->cart->secure_key = $customer->secure_key;
								$this->context->cart->update();
								Module::hookExec('createAccount', array(
									'_POST' => $_POST,
									'newCustomer' => $customer
								));
								if (Tools::isSubmit('ajax'))
								{
									$return = array(
										'hasError' => !empty($this->errors),
										'errors' => $this->errors,
										'isSaved' => true,
										'id_customer' => (int)$this->context->cookie->id_customer,
										'id_address_delivery' => $this->context->cart->id_address_delivery,
										'id_address_invoice' => $this->context->cart->id_address_invoice,
										'token' => Tools::getToken(false)
									);
									die(Tools::jsonEncode($return));
								}
								if ($back = Tools::getValue('back'))
									Tools::redirect('index.php?controller='.$back);
								Tools::redirect('index.php?controller=my-account');
							}
						}
					}
				}
			}
			if (sizeof($this->errors))
			{
				if (!Tools::getValue('is_new_customer'))
					unset($_POST['passwd']);
				if (Tools::isSubmit('ajax'))
				{
					$return = array(
						'hasError' => !empty($this->errors),
						'errors' => $this->errors,
						'isSaved' => false,
						'id_customer' => 0
					);
					die(Tools::jsonEncode($return));
				}
			}
		}

		if (Tools::isSubmit('SubmitLogin'))
		{
			Module::hookExec('beforeAuthentication');
			$passwd = trim(Tools::getValue('passwd'));
			$email = trim(Tools::getValue('email'));
			if (empty($email))
				$this->errors[] = Tools::displayError('E-mail address required');
			elseif (!Validate::isEmail($email))
				$this->errors[] = Tools::displayError('Invalid e-mail address');
			elseif (empty($passwd))
				$this->errors[] = Tools::displayError('Password is required');
			elseif (Tools::strlen($passwd) > 32)
				$this->errors[] = Tools::displayError('Password is too long');
			elseif (!Validate::isPasswd($passwd))
				$this->errors[] = Tools::displayError('Invalid password');
			else
			{
				$customer = new Customer();
				$authentication = $customer->getByEmail(trim($email), trim($passwd));
				if (!$authentication OR !$customer->id)
				{
					/* Handle brute force attacks */
					sleep(1);
					$this->errors[] = Tools::displayError('Authentication failed');
				}
				else
				{
					$this->context->cookie->id_customer = (int)($customer->id);
					$this->context->cookie->customer_lastname = $customer->lastname;
					$this->context->cookie->customer_firstname = $customer->firstname;
					$this->context->cookie->logged = 1;
					$customer->logged = 1;
					$this->context->cookie->is_guest = $customer->isGuest();
					$this->context->cookie->passwd = $customer->passwd;
					$this->context->cookie->email = $customer->email;
					if (Configuration::get('PS_CART_FOLLOWING') AND (empty($this->context->cookie->id_cart) OR Cart::getNbProducts($this->context->cookie->id_cart) == 0))
						$this->context->cookie->id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id);
					/* Update cart address */
					$this->context->cart->id_carrier = 0;
					$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
					$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
					$this->context->cart->update();
					Module::hookExec('authentication');
					if (!Tools::isSubmit('ajax'))
					{
						if ($back = Tools::getValue('back'))
							Tools::redirect($back);
						Tools::redirect('index.php?controller=my-account');
					}
				}
			}
			if (Tools::isSubmit('ajax'))
			{
				$return = array(
					'hasError' => !empty($this->errors),
					'errors' => $this->errors,
					'token' => Tools::getToken(false)
				);
				die(Tools::jsonEncode($return));
			}
		}

		if (isset($create_account))
		{
			/* Select the most appropriate country */
			if (isset($_POST['id_country']) AND is_numeric($_POST['id_country']))
				$selectedCountry = (int)($_POST['id_country']);
			/* FIXME : language iso and country iso are not similar,
			 * maybe an associative table with country an language can resolve it,
			 * But for now it's a bug !
			 * @see : bug #6968
			 * @link:http://www.prestashop.com/bug_tracker/view/6968/
			elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				$array = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
				if (Validate::isLanguageIsoCode($array[0]))
				{
					$selectedCountry = Country::getByIso($array[0]);
					if (!$selectedCountry)
						$selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));
				}
			}*/
			if (!isset($selectedCountry))
				$selectedCountry = (int)(Configuration::get('PS_COUNTRY_DEFAULT'));

			if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
				$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
			else
				$countries = Country::getCountries($this->context->language->id, true);
			$this->context->smarty->assign(array(
				'countries' => $countries,
				'sl_country' => (isset($selectedCountry) ? $selectedCountry : 0),
				'vat_management' => Configuration::get('VATNUMBER_MANAGEMENT')
			));

			/* Call a hook to display more information on form */
			$this->context->smarty->assign(array(
				'HOOK_CREATE_ACCOUNT_FORM' => Module::hookExec('createAccountForm'),
				'HOOK_CREATE_ACCOUNT_TOP' => Module::hookExec('createAccountTop')
			));
		}

		$this->context->smarty->assign('genders', Gender::getGenders());

		/* Generate years, months and days */
		if (isset($_POST['years']) AND is_numeric($_POST['years']))
			$selectedYears = (int)($_POST['years']);
		$years = Tools::dateYears();
		if (isset($_POST['months']) AND is_numeric($_POST['months']))
			$selectedMonths = (int)($_POST['months']);
		$months = Tools::dateMonths();

		if (isset($_POST['days']) AND is_numeric($_POST['days']))
			$selectedDays = (int)($_POST['days']);
		$days = Tools::dateDays();

		$this->context->smarty->assign(array(
			'years' => $years,
			'sl_year' => (isset($selectedYears) ? $selectedYears : 0),
			'months' => $months,
			'sl_month' => (isset($selectedMonths) ? $selectedMonths : 0),
			'days' => $days,
			'sl_day' => (isset($selectedDays) ? $selectedDays : 0)
		));
		$this->context->smarty->assign('newsletter', (int)Module::getInstanceByName('blocknewsletter')->active);
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'authentication.css');
		$this->addJS(array(_THEME_JS_DIR_.'tools/statesManagement.js', _PS_JS_DIR_.'jquery/jquery-typewatch.pack.js'));
	}

	public function process()
	{
		parent::process();

		$back = Tools::getValue('back');
		$key = Tools::safeOutput(Tools::getValue('key'));
		if (!empty($key))
			$back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
		if (!empty($back))
		{
			$this->context->smarty->assign('back', Tools::safeOutput($back));
			if (strpos($back, 'order') !== false)
			{
				if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
					$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
				else
					$countries = Country::getCountries($this->context->language->id, true);

				$this->context->smarty->assign(array(
					'inOrderProcess' => true,
					'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
					'sl_country' => (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT')),
					'countries' => $countries
				));
			}
		}

		$this->processAddressFormat();
		$this->setTemplate(_PS_THEME_DIR_.'authentication.tpl');
	}

	protected function processAddressFormat()
	{
		$addressItems = array();
		$addressFormat = AddressFormat::getOrderedAddressFields(Configuration::get('PS_COUNTRY_DEFAULT'), false, true);
		$requireFormFieldsList = AddressFormat::$requireFormFieldsList;

		foreach ($addressFormat as $addressline)
			foreach (explode(' ', $addressline) as $addressItem)
				$addressItems[] = trim($addressItem);

		// Add missing require fields for a new user susbscription form
		foreach($requireFormFieldsList as $fieldName)
			if (!in_array($fieldName, $addressItems))
				$addressItems[] = trim($fieldName);

		foreach (array('inv', 'dlv') as $addressType)
			$this->context->smarty->assign(array($addressType.'_adr_fields' => $addressFormat, $addressType.'_all_fields' => $addressItems));
		}
	}

