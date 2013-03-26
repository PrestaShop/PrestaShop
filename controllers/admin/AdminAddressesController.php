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

class AdminAddressesControllerCore extends AdminController
{
	/** @var array countries list */
	protected $countries_array = array();

	public function __construct()
	{
		$this->required_database = true;
		$this->required_fields = array('company','address2', 'postcode', 'other', 'phone', 'phone_mobile', 'vat_number', 'dni');
	 	$this->table = 'address';
	 	$this->className = 'Address';
	 	$this->lang = false;
		$this->addressType = 'customer';
		$this->explicitSelect = true;
		$this->context = Context::getContext();

		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->allow_export = true;

		if (!Tools::getValue('realedit'))
			$this->deleted = true;

		$countries = Country::getCountries($this->context->language->id);
		foreach ($countries as $country)
			$this->countries_array[$country['id_country']] = $country['name'];

		$this->fields_list = array(
			'id_address' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'firstname' => array('title' => $this->l('First Name'), 'width' => 120, 'filter_key' => 'a!firstname'),
			'lastname' => array('title' => $this->l('Last Name'), 'width' => 140, 'filter_key' => 'a!lastname'),
			'address1' => array('title' => $this->l('Address')),
			'postcode' => array('title' => $this->l('Zip/Postal Code'), 'align' => 'right', 'width' => 80),
			'city' => array('title' => $this->l('City'), 'width' => 150),
			'country' => array('title' => $this->l('Country'), 'width' => 100, 'type' => 'select', 'list' => $this->countries_array, 'filter_key' => 'cl!id_country'));

		parent::__construct();

		$this->_select = 'cl.`name` as country';
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON a.id_customer = c.id_customer
		';
		$this->_where = 'AND a.id_customer != 0 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c');
	}

	public function initToolbar()
	{
		parent::initToolbar();
		if (!$this->display)
			$this->toolbar_btn['import'] = array(
				'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type='.$this->table,
				'desc' => $this->l('Import')
			);
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Addresses'),
				'image' => '../img/admin/contact.gif'
			),
			'input' => array(
				array(
					'type' => 'text_customer',
					'label' => $this->l('Customer'),
					'name' => 'id_customer',
					'size' => 33,
					'required' => false,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Identification Number'),
					'name' => 'dni',
					'size' => 30,
					'required' => false,
					'desc' => $this->l('DNI / NIF / NIE')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address alias'),
					'name' => 'alias',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Home phone'),
					'name' => 'phone',
					'size' => 33,
					'required' => false,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Mobile phone'),
					'name' => 'phone_mobile',
					'size' => 33,
					'required' => false,
					'desc' => Configuration::get('PS_ONE_PHONE_AT_LEAST')? sprintf($this->l('You must register at least one phone number %s'), '<sup>*</sup>') : ''
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Other'),
					'name' => 'other',
					'cols' => 36,
					'rows' => 4,
					'required' => false,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span>'
				),
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);
		$id_customer = (int)Tools::getValue('id_customer');
		if (!$id_customer && Validate::isLoadedObject($this->object))
			$id_customer = $this->object->id_customer;
		if ($id_customer)
		{
			$customer = new Customer((int)$id_customer);
			$token_customer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id);
		}

		// @todo in 1.4, this include was done before the class declaration
		// We should use a hook now
		if (Configuration::get('VATNUMBER_MANAGEMENT') && file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php'))
			include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
		if (Configuration::get('VATNUMBER_MANAGEMENT'))
			if (file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php') && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT')))
				$vat = 'is_applicable';
			else
				$vat = 'management';

		$this->tpl_form_vars = array(
			'vat' => isset($vat) ? $vat : null,
			'customer' => isset($customer) ? $customer : null,
			'tokenCustomer' => isset ($token_customer) ? $token_customer : null
		);

		// Order address fields depending on country format
		$addresses_fields = $this->processAddressFormat();
		// we use  delivery address
		$addresses_fields = $addresses_fields['dlv_all_fields'];

		$temp_fields = array();

		foreach ($addresses_fields as $addr_field_item)
		{
			if ($addr_field_item == 'company')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Company'),
					'name' => 'company',
					'size' => 33,
					'required' => false,
					'hint' => $this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span>'
				);
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('VAT number'),
					'name' => 'vat_number',
					'size' => 33,
				);
			}
			else if ($addr_field_item == 'lastname')
			{
				if (isset($customer) &&
					!Tools::isSubmit('submit'.strtoupper($this->table)) &&
					Validate::isLoadedObject($customer) &&
					!Validate::isLoadedObject($this->object))
					$default_value = $customer->lastname;
				else
					$default_value = '';

				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Last Name'),
					'name' => 'lastname',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span>',
					'default_value' => $default_value,
				);
			}
			else if ($addr_field_item == 'firstname')
			{
				if (isset($customer) &&
					!Tools::isSubmit('submit'.strtoupper($this->table)) &&
					Validate::isLoadedObject($customer) &&
					!Validate::isLoadedObject($this->object))
					$default_value = $customer->firstname;
 	 	 	 	else
 	 	 	 		$default_value = '';

				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('First Name'),
					'name' => 'firstname',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span>',
					'default_value' => $default_value,
				);
			}
			else if ($addr_field_item == 'address1')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Address'),
					'name' => 'address1',
					'size' => 33,
					'required' => true,
				);
			}
			else if ($addr_field_item == 'address2')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Address').' (2)',
					'name' => 'address2',
					'size' => 33,
					'required' => false,
				);
			}
			elseif ($addr_field_item == 'postcode')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Zip/Postal Code'),
					'name' => 'postcode',
					'size' => 33,
					'required' => true,
				);
			}
			else if ($addr_field_item == 'city')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('City'),
					'name' => 'city',
					'size' => 33,
					'required' => true,
				);
			}
			else if ($addr_field_item == 'country' || $addr_field_item == 'Country:name')
			{
				$temp_fields[] = array(
					'type' => 'select',
					'label' => $this->l('Country'),
					'name' => 'id_country',
					'required' => false,
					'default_value' => (int)$this->context->country->id,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id),
						'id' => 'id_country',
						'name' => 'name',
					)
				);
				$temp_fields[] = array(
					'type' => 'select',
					'label' => $this->l('State'),
					'name' => 'id_state',
					'required' => false,
					'options' => array(
						'query' => array(),
						'id' => 'id_state',
						'name' => 'name',
					)
				);
			}
		}

		// merge address format with the rest of the form
		array_splice($this->fields_form['input'], 3, 0, $temp_fields);

		return parent::renderForm();
	}

	public function processSave()
	{
		// Transform e-mail in id_customer for parent processing
		if (Validate::isEmail(Tools::getValue('email')))
		{
			$customer = new Customer();
			$customer->getByEmail(Tools::getValue('email'), null, false);
			if (Validate::isLoadedObject($customer))
				$_POST['id_customer'] = $customer->id;
			else
				$this->errors[] = Tools::displayError('This email address is not registered.');
		}
		else if ($id_customer = Tools::getValue('id_customer'))
		{
			$customer = new Customer((int)$id_customer);
			if (Validate::isLoadedObject($customer))
				$_POST['id_customer'] = $customer->id;
			else
				$this->errors[] = Tools::displayError('Unknown customer');
		}
		else
			$this->errors[] = Tools::displayError('Unknown customer');
		if (Country::isNeedDniByCountryId(Tools::getValue('id_country')) && !Tools::getValue('dni'))
			$this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');

		/* If the selected country does not contain states */
		$id_state = (int)Tools::getValue('id_state');
		$id_country = (int)Tools::getValue('id_country');
		$country = new Country((int)$id_country);
		if ($country && !(int)$country->contains_states && $id_state)
			$this->errors[] = Tools::displayError('You\'ve selected a state for a country that does not contain states.');

		/* If the selected country contains states, then a state have to be selected */
		if ((int)$country->contains_states && !$id_state)
			$this->errors[] = Tools::displayError('An address located in a country containing states must have a state selected.');

		$postcode = Tools::getValue('postcode');		
		/* Check zip code format */
		if ($country->zip_code_format && !$country->checkZipCode($postcode))
			$this->errors[] = Tools::displayError('Your Postal / Zip Code is incorrect.').'<br />'.Tools::displayError('It must be entered as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)));
		elseif(empty($postcode) && $country->need_zip_code)
			$this->errors[] = Tools::displayError('A Zip / Postal code is required.');
		elseif ($postcode && !Validate::isPostCode($postcode))
			$this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');

		if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !Tools::getValue('phone') && !Tools::getValue('phone_mobile'))		
			$this->errors[] = Tools::displayError('You must register at least one phone number.');

		/* If this address come from order's edition and is the same as the other one (invoice or delivery one)
		** we delete its id_address to force the creation of a new one */
		if ((int)Tools::getValue('id_order'))
		{
			$this->_redirect = false;
			if (isset($_POST['address_type']))
				$_POST['id_address'] = '';
		}

		// Check the requires fields which are settings in the BO
		$address = new Address();
		$this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

		if (empty($this->errors))
			return parent::processSave();
		else
			// if we have errors, we stay on the form instead of going back to the list
			$this->display = 'edit';

		/* Reassignation of the order's new (invoice or delivery) address */
		$address_type = ((int)Tools::getValue('address_type') == 2 ? 'invoice' : ((int)Tools::getValue('address_type') == 1 ? 'delivery' : ''));
		if ($this->action == 'save' && ($id_order = (int)Tools::getValue('id_order')) && !count($this->errors) && !empty($address_type))
		{
			if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'orders SET `id_address_'.$address_type.'` = '.Db::getInstance()->Insert_ID().' WHERE `id_order` = '.$id_order))
				$this->errors[] = Tools::displayError('An error occurred while linking this address to its order.');
			else
				Tools::redirectAdmin(Tools::getValue('back').'&conf=4');
		}
	}

	public function processAdd()
	{
		if (Tools::getValue('submitFormAjax'))
			$this->redirect_after = false;

		return parent::processAdd();
	}

	/**
	 * Get Address formats used by the country where the address id retrieved from POST/GET is.
	 *
	 * @return array address formats
	 */
	protected function processAddressFormat()
	{
		$tmp_addr = new Address((int)Tools::getValue('id_address'));

		$selected_country = ($tmp_addr && $tmp_addr->id_country) ? $tmp_addr->id_country : (int)Configuration::get('PS_COUNTRY_DEFAULT');

		$inv_adr_fields = AddressFormat::getOrderedAddressFields($selected_country, false, true);
		$dlv_adr_fields = AddressFormat::getOrderedAddressFields($selected_country, false, true);

		$inv_all_fields = array();
		$dlv_all_fields = array();

		$out = array();

		foreach (array('inv','dlv') as $adr_type)
		{
			foreach (${$adr_type.'_adr_fields'} as $fields_line)
				foreach (explode(' ', $fields_line) as $field_item)
					${$adr_type.'_all_fields'}[] = trim($field_item);

			$out[$adr_type.'_adr_fields'] = ${$adr_type.'_adr_fields'};
			$out[$adr_type.'_all_fields'] = ${$adr_type.'_all_fields'};
		}

		return $out;
	}

	/**
	 * Method called when an ajax request is made
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcess()
	{
		if (Tools::isSubmit('email'))
		{
			$email = pSQL(Tools::getValue('email'));
			$customer = Customer::searchByName($email);
			if (!empty($customer))
			{
				$customer = $customer['0'];
				echo Tools::jsonEncode(array('infos' => pSQL($customer['firstname']).'_'.pSQL($customer['lastname']).'_'.pSQL($customer['company'])));
			}
		}
		die;
	}
}
