<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AuthControllerCore extends FrontController
{
	public $ssl = true;
	public $php_self = 'authentication';

	/**
	 * @var bool create_account
	 */
	protected $create_account;

	/**
	 * Initialize auth controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if (!Tools::getIsset('step') && $this->context->customer->isLogged() && !$this->ajax)
			Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? url_encode($this->authRedirection) : 'my-account'));

		if (Tools::getValue('create_account'))
			$this->create_account = true;
	}

	/**
	 * Set default medias for this controller
	 * @see FrontController::setMedia()
	 */
	public function setMedia()
	{
		parent::setMedia();
		if (Context::getContext()->getMobileDevice() === false)
			$this->addCSS(_THEME_CSS_DIR_.'authentication.css');
		$this->addJqueryPlugin('typewatch');
		$this->addJS(_THEME_JS_DIR_.'tools/statesManagement.js');
	}

	/**
	 * Run ajax process
	 * @see FrontController::displayAjax()
	 */
	public function displayAjax()
	{
		$this->display();
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign('genders', Gender::getGenders());

		$this->assignDate();

		$this->assignCountries();

		$active_module_newsletter = false;
		if ($module_newsletter = Module::getInstanceByName('blocknewsletter'))
			$active_module_newsletter = $module_newsletter->active;

		$this->context->smarty->assign('newsletter', (int)$active_module_newsletter);

		$back = Tools::getValue('back');
		$key = Tools::safeOutput(Tools::getValue('key'));
		if (!empty($key))
			$back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
		if (!empty($back))
			$this->context->smarty->assign('back', Tools::safeOutput($back));
	
		if (Tools::getValue('display_guest_checkout'))
		{
			if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
				$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
			else
				$countries = Country::getCountries($this->context->language->id, true);
			
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				$array = preg_split('/,|-/', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
				if (!Validate::isLanguageIsoCode($array[0]) || !($sl_country = Country::getByIso($array[0])))
					$sl_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
			}
			else
				$sl_country = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));
			
			$this->context->smarty->assign(array(
					'inOrderProcess' => true,
					'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
					'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
					'sl_country' => (int)$sl_country,
					'countries' => $countries
				));
		}

		if (Tools::getValue('create_account'))
			$this->context->smarty->assign('email_create', 1);

		if (Tools::getValue('multi-shipping') == 1)
			$this->context->smarty->assign('multi_shipping', true);
		else
			$this->context->smarty->assign('multi_shipping', false);
		
		$this->assignAddressFormat();

		// Call a hook to display more information on form
		$this->context->smarty->assign(array(
				'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
				'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
			));
		
		if ($this->ajax)
		{
			// Call a hook to display more information on form
			$this->context->smarty->assign(array(
					'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
					'genders' => Gender::getGenders()
				));

			$return = array(
				'hasError' => !empty($this->errors),
				'errors' => $this->errors,
				'page' => $this->context->smarty->fetch(_PS_THEME_DIR_.'authentication.tpl'),
				'token' => Tools::getToken(false)
			);
			die(Tools::jsonEncode($return));
		}
		$this->setTemplate(_PS_THEME_DIR_.'authentication.tpl');
	}

	/**
	 * Assign date var to smarty
	 */
	protected function assignDate()
	{
		// Generate years, months and days
		if (isset($_POST['years']) && is_numeric($_POST['years']))
			$selectedYears = (int)($_POST['years']);
		$years = Tools::dateYears();
		if (isset($_POST['months']) && is_numeric($_POST['months']))
			$selectedMonths = (int)($_POST['months']);
		$months = Tools::dateMonths();

		if (isset($_POST['days']) && is_numeric($_POST['days']))
			$selectedDays = (int)($_POST['days']);
		$days = Tools::dateDays();

		$this->context->smarty->assign(array(
				'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
				'onr_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'), //retro compat
				'years' => $years,
				'sl_year' => (isset($selectedYears) ? $selectedYears : 0),
				'months' => $months,
				'sl_month' => (isset($selectedMonths) ? $selectedMonths : 0),
				'days' => $days,
				'sl_day' => (isset($selectedDays) ? $selectedDays : 0)
			));
	}

	/**
	 * Assign countries var to smarty
	 */
	protected function assignCountries()
	{
		if (isset($this->create_account))
		{
			// Select the most appropriate country
			if (isset($_POST['id_country']) && is_numeric($_POST['id_country']))
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
					'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
					'sl_country' => (isset($selectedCountry) ? $selectedCountry : 0),
					'vat_management' => Configuration::get('VATNUMBER_MANAGEMENT')
				));
		}
	}

	/**
	 * Assign address var to smarty
	 */
	protected function assignAddressFormat()
	{
		$addressItems = array();
		$addressFormat = AddressFormat::getOrderedAddressFields(Configuration::get('PS_COUNTRY_DEFAULT'), false, true);
		$requireFormFieldsList = AddressFormat::$requireFormFieldsList;

		foreach ($addressFormat as $addressline)
			foreach (explode(' ', $addressline) as $addressItem)
			$addressItems[] = trim($addressItem);

		// Add missing require fields for a new user susbscription form
		foreach ($requireFormFieldsList as $fieldName)
			if (!in_array($fieldName, $addressItems))
				$addressItems[] = trim($fieldName);

		foreach (array('inv', 'dlv') as $addressType)
			$this->context->smarty->assign(array($addressType.'_adr_fields' => $addressFormat, $addressType.'_all_fields' => $addressItems));
	}

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('SubmitCreate'))
			$this->processSubmitCreate();

		if (Tools::isSubmit('submitAccount') || Tools::isSubmit('submitGuestAccount'))
			$this->processSubmitAccount();

		if (Tools::isSubmit('SubmitLogin'))
			$this->processSubmitLogin();
	}

	/**
	 * Process login
	 */
	protected function processSubmitLogin()
	{
		Hook::exec('actionBeforeAuthentication');
		$passwd = trim(Tools::getValue('passwd'));
		$email = trim(Tools::getValue('email'));
		if (empty($email))
			$this->errors[] = Tools::displayError('An email address required.');
		elseif (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Invalid email address.');
		elseif (empty($passwd))
			$this->errors[] = Tools::displayError('Password is required.');
		elseif (!Validate::isPasswd($passwd))
			$this->errors[] = Tools::displayError('Invalid password.');
		else
		{
			$customer = new Customer();
			$authentication = $customer->getByEmail(trim($email), trim($passwd));
			if (!$authentication || !$customer->id)
				$this->errors[] = Tools::displayError('Authentication failed.');
			else
			{
				$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
				$this->context->cookie->id_customer = (int)($customer->id);
				$this->context->cookie->customer_lastname = $customer->lastname;
				$this->context->cookie->customer_firstname = $customer->firstname;
				$this->context->cookie->logged = 1;
				$customer->logged = 1;
				$this->context->cookie->is_guest = $customer->isGuest();
				$this->context->cookie->passwd = $customer->passwd;
				$this->context->cookie->email = $customer->email;
				
				// Add customer to the context
				$this->context->customer = $customer;
				
				if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
					$this->context->cart = new Cart($id_cart);
				else
				{
					$this->context->cart->id_carrier = 0;
					$this->context->cart->setDeliveryOption(null);
					$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
					$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
				}
				$this->context->cart->id_customer = (int)$customer->id;
				$this->context->cart->secure_key = $customer->secure_key;
				$this->context->cart->save();
				$this->context->cookie->id_cart = (int)$this->context->cart->id;
				$this->context->cookie->write();
				$this->context->cart->autosetProductAddress();

				Hook::exec('actionAuthentication');

				// Login information have changed, so we check if the cart rules still apply
				CartRule::autoRemoveFromCart($this->context);
				CartRule::autoAddToCart($this->context);

				if (!$this->ajax)
				{
					if ($back = Tools::getValue('back'))
						Tools::redirect(html_entity_decode($back));
					Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? url_encode($this->authRedirection) : 'my-account'));
				}
			}
		}
		if ($this->ajax)
		{
			$return = array(
				'hasError' => !empty($this->errors),
				'errors' => $this->errors,
				'token' => Tools::getToken(false)
			);
			die(Tools::jsonEncode($return));
		}
		else
			$this->context->smarty->assign('authentification_error', $this->errors);
	}

	/**
	 * Process the newsletter settings and set the customer infos.
	 *
	 * @param Customer $customer Reference on the customer Object.
	 *
	 * @note At this point, the email has been validated.
	 */
	protected function processCustomerNewsletter(&$customer)
	{
		if (Tools::getValue('newsletter'))
		{
			$customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
			$customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));

			if ($module_newsletter = Module::getInstanceByName('blocknewsletter'))
				if ($module_newsletter->active)
					$module_newsletter->confirmSubscription(Tools::getValue('email'));
		}
	}

	/**
	 * Process submit on an account
	 */
	protected function processSubmitAccount()
	{
		Hook::exec('actionBeforeSubmitAccount');
		$this->create_account = true;
		if (Tools::isSubmit('submitAccount'))
			$this->context->smarty->assign('email_create', 1);
		// New Guest customer
		if (!Tools::getValue('is_new_customer', 1) && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED'))
			$this->errors[] = Tools::displayError('You cannot create a guest account..');
		if (!Tools::getValue('is_new_customer', 1))
			$_POST['passwd'] = md5(time()._COOKIE_KEY_);
		if (isset($_POST['guest_email']) && $_POST['guest_email'])
			$_POST['email'] = $_POST['guest_email'];
		// Checked the user address in case he changed his email address
		if (Validate::isEmail($email = Tools::getValue('email')) && !empty($email))
			if (Customer::customerExists($email))
				$this->errors[] = Tools::displayError('An account using this email address has already been registered.', false);
		// Preparing customer
		$customer = new Customer();
		$lastnameAddress = Tools::getValue('lastname');
		$firstnameAddress = Tools::getValue('firstname');		
		$_POST['lastname'] = Tools::getValue('customer_lastname');
		$_POST['firstname'] = Tools::getValue('customer_firstname');
		
		$error_phone = false;
		if (Configuration::get('PS_ONE_PHONE_AT_LEAST'))
		{
			if (Tools::isSubmit('submitGuestAccount') || !Tools::getValue('is_new_customer'))
			{
				if (!Tools::getValue('phone') && !Tools::getValue('phone_mobile'))
					$error_phone = true;
			}
			elseif (((Configuration::get('PS_REGISTRATION_PROCESS_TYPE') || Configuration::get('PS_ORDER_PROCESS_TYPE')) 
					&& (Configuration::get('PS_ORDER_PROCESS_TYPE') && !Tools::getValue('email_create')))
					&& (!Tools::getValue('phone') && !Tools::getValue('phone_mobile')))
				$error_phone = true;
			elseif (((Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && Configuration::get('PS_ORDER_PROCESS_TYPE') && Tools::getValue('email_create')))
					&& (!Tools::getValue('phone') && !Tools::getValue('phone_mobile')))
				$error_phone = true;
		}

		if ($error_phone)
			$this->errors[] = Tools::displayError('You must register at least one phone number.');
		
		$this->errors = array_unique(array_merge($this->errors, $customer->validateController()));

		// Check the requires fields which are settings in the BO
		$this->errors = array_merge($this->errors, $customer->validateFieldsRequiredDatabase());

		if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && !$this->ajax && !Tools::isSubmit('submitGuestAccount'))
		{
			if (!count($this->errors))
			{
				if (Tools::isSubmit('newsletter'))
					$this->processCustomerNewsletter($customer);

				$customer->birthday = (empty($_POST['years']) ? '' : (int)$_POST['years'].'-'.(int)$_POST['months'].'-'.(int)$_POST['days']);
				if (!Validate::isBirthDate($customer->birthday))
					$this->errors[] = Tools::displayError('Invalid date of birth.');

				// New Guest customer
				$customer->is_guest = (Tools::isSubmit('is_new_customer') ? !Tools::getValue('is_new_customer', 1) : 0);
				$customer->active = 1;

				if (!count($this->errors))
				{
					if ($customer->add())
					{
						if (!$customer->is_guest)
							if (!$this->sendConfirmationMail($customer))
								$this->errors[] = Tools::displayError('The email cannot be sent.');

						$this->updateContext($customer);

						$this->context->cart->update();
						Hook::exec('actionCustomerAccountAdd', array(
								'_POST' => $_POST,
								'newCustomer' => $customer
							));
						if ($this->ajax)
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
							Tools::redirect(html_entity_decode($back));
						// redirection: if cart is not empty : redirection to the cart
						if (count($this->context->cart->getProducts(true)) > 0)
							Tools::redirect('index.php?controller=order&multi-shipping='.(int)Tools::getValue('multi-shipping'));
						// else : redirection to the account
						else
							Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? url_encode($this->authRedirection) : 'my-account'));
					}
					else
						$this->errors[] = Tools::displayError('An error occurred while creating your account..');
				}
			}

		}
		else // if registration type is in one step, we save the address
		{
			// Preparing address
			$address = new Address();
			$_POST['lastname'] = $lastnameAddress;
			$_POST['firstname'] = $firstnameAddress;
			$address->id_customer = 1;
			$this->errors = array_unique(array_merge($this->errors, $address->validateController()));

			// US customer: normalize the address
			if ($address->id_country == Country::getByIso('US'))
			{
				include_once(_PS_TAASC_PATH_.'AddressStandardizationSolution.php');
				$normalize = new AddressStandardizationSolution;
				$address->address1 = $normalize->AddressLineStandardization($address->address1);
				$address->address2 = $normalize->AddressLineStandardization($address->address2);
			}

			if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country))
				$this->errors[] = Tools::displayError('Country cannot be loaded with address->id_country');
			$postcode = Tools::getValue('postcode');		
			/* Check zip code format */
			if ($country->zip_code_format && !$country->checkZipCode($postcode))
				$this->errors[] = sprintf(Tools::displayError('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
			elseif(empty($postcode) && $country->need_zip_code)
				$this->errors[] = Tools::displayError('A Zip / Postal code is required.');
			elseif ($postcode && !Validate::isPostCode($postcode))
				$this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');

			if ($country->need_identification_number && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))))
				$this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
			elseif (!$country->need_identification_number)
				$address->dni = null;
		}

		if (!@checkdate(Tools::getValue('months'), Tools::getValue('days'), Tools::getValue('years')) && !(Tools::getValue('months') == '' && Tools::getValue('days') == '' && Tools::getValue('years') == ''))
			$this->errors[] = Tools::displayError('Invalid date of birth');

		if (!count($this->errors))
		{
			if (Customer::customerExists(Tools::getValue('email')))
				$this->errors[] = Tools::displayError('An account using this email address has already been registered. Please enter a valid password or request a new one. ', false);
			if (Tools::isSubmit('newsletter'))
				$this->processCustomerNewsletter($customer);

			$customer->birthday = (empty($_POST['years']) ? '' : (int)$_POST['years'].'-'.(int)$_POST['months'].'-'.(int)$_POST['days']);
			if (!Validate::isBirthDate($customer->birthday))
					$this->errors[] = Tools::displayError('Invalid date of birth');

			if (!count($this->errors))
			{
				// if registration type is in one step, we save the address
				if (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') || Tools::isSubmit('submitGuestAccount'))
					if (!($country = new Country($address->id_country, Configuration::get('PS_LANG_DEFAULT'))) || !Validate::isLoadedObject($country))
						die(Tools::displayError());
				$contains_state = isset($country) && is_object($country) ? (int)$country->contains_states: 0;
				$id_state = isset($address) && is_object($address) ? (int)$address->id_state: 0;
				if (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && $contains_state && !$id_state)
					$this->errors[] = Tools::displayError('This country requires you to chose a State.');
				else
				{
					$customer->active = 1;
					// New Guest customer
					if (Tools::isSubmit('is_new_customer'))
						$customer->is_guest = !Tools::getValue('is_new_customer', 1);
					else
						$customer->is_guest = 0;
					if (!$customer->add())
						$this->errors[] = Tools::displayError('An error occurred while creating your account..');
					else
					{
						$address->id_customer = (int)$customer->id;
						$this->errors = array_unique(array_merge($this->errors, $address->validateController()));
						if (!count($this->errors) && (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') || $this->ajax || Tools::isSubmit('submitGuestAccount')) && !$address->add())
							$this->errors[] = Tools::displayError('An error occurred while creating your address.');
						else
						{
							if (!$customer->is_guest)
							{
								$this->context->customer = $customer;
								$customer->cleanGroups();
								// we add the guest customer in the default customer group
								$customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
								if (!$this->sendConfirmationMail($customer))
									$this->errors[] = Tools::displayError('The email cannot be sent.');
							}
							else
							{
								$customer->cleanGroups();
								// we add the guest customer in the guest customer group
								$customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
							}
							$this->updateContext($customer);
							$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)$customer->id);
							$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)$customer->id);

							// If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
							$this->context->cart->update();

							// Avoid articles without delivery address on the cart
							$this->context->cart->autosetProductAddress();

							Hook::exec('actionCustomerAccountAdd', array(
									'_POST' => $_POST,
									'newCustomer' => $customer
								));
							if ($this->ajax)
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
							// if registration type is in two steps, we redirect to register address
							if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE') && !$this->ajax && !Tools::isSubmit('submitGuestAccount'))
								Tools::redirect('index.php?controller=address');
								
							if ($back = Tools::getValue('back'))
								Tools::redirect(html_entity_decode($back));

							// redirection: if cart is not empty : redirection to the cart
							if (count($this->context->cart->getProducts(true)) > 0)
								Tools::redirect('index.php?controller=order&multi-shipping='.(int)Tools::getValue('multi-shipping'));
							// else : redirection to the account
							else
								Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? url_encode($this->authRedirection) : 'my-account'));
						}
					}
				}
			}
		}

		if (count($this->errors))
		{
			//for retro compatibility to display guest account creation form on authentication page
			if (Tools::getValue('submitGuestAccount'))
				$_GET['display_guest_checkout'] = 1;
			
			if (!Tools::getValue('is_new_customer'))
				unset($_POST['passwd']);
			if ($this->ajax)
			{
				$return = array(
					'hasError' => !empty($this->errors),
					'errors' => $this->errors,
					'isSaved' => false,
					'id_customer' => 0
				);
				die(Tools::jsonEncode($return));
			}
			$this->context->smarty->assign('account_error', $this->errors);
		}
	}

	/**
	 * Process submit on a creation
	 */
	protected function processSubmitCreate()
	{
		if (!Validate::isEmail($email = Tools::getValue('email_create')) || empty($email))
			$this->errors[] = Tools::displayError('Invalid email address.');
		elseif (Customer::customerExists($email))
		{
			$this->errors[] = Tools::displayError('An account using this email address has already been registered. Please enter a valid password or request a new one. ', false);
			$_POST['email'] = $_POST['email_create'];
			unset($_POST['email_create']);
		}
		else
		{
			$this->create_account = true;
			$this->context->smarty->assign('email_create', Tools::safeOutput($email));
			$_POST['email'] = $email;
		}
	}

	/**
	 * Update context after customer creation
	 * @param Customer $customer Created customer
	 */
	protected function updateContext(Customer $customer)
	{
		$this->context->customer = $customer;
		$this->context->smarty->assign('confirmation', 1);
		$this->context->cookie->id_customer = (int)$customer->id;
		$this->context->cookie->customer_lastname = $customer->lastname;
		$this->context->cookie->customer_firstname = $customer->firstname;
		$this->context->cookie->passwd = $customer->passwd;
		$this->context->cookie->logged = 1;
		// if register process is in two steps, we display a message to confirm account creation
		if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE'))
			$this->context->cookie->account_created = 1;
		$customer->logged = 1;
		$this->context->cookie->email = $customer->email;
		$this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
		// Update cart address
		$this->context->cart->secure_key = $customer->secure_key;
	}

	/**
	 * sendConfirmationMail
	 * @param Customer $customer
	 * @return bool
	 */
	protected function sendConfirmationMail(Customer $customer)
	{
		return Mail::Send(
			$this->context->language->id,
			'account',
			Mail::l('Welcome!'),
			array(
				'{firstname}' => $customer->firstname,
				'{lastname}' => $customer->lastname,
				'{email}' => $customer->email,
				'{passwd}' => Tools::getValue('passwd')),
			$customer->email,
			$customer->firstname.' '.$customer->lastname
		);
	}
}
