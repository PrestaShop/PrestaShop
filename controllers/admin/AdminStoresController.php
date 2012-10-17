<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminStoresControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'store';
		$this->className = 'Store';
	 	$this->lang = false;
		$this->toolbar_scroll = false;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldImageSettings = array(
			'name' => 'image',
			'dir' => 'st'
		);

		$this->fields_list = array(
			'id_store' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Name'), 'width' => 120, 'filter_key' => 'a!name'),
			'address1' => array('title' => $this->l('Address'), 'width' => 120, 'filter_key' => 'a!address1'),
			'city' => array('title' => $this->l('City'), 'width' => 100),
			'postcode' => array('title' => $this->l('Zip code'), 'width' => 50),
			'state' => array('title' => $this->l('State'), 'width' => 100, 'filter_key' => 'st!name'),
			'country' => array('title' => $this->l('Country'), 'width' => 100, 'filter_key' => 'cl!name'),
			'phone' => array('title' => $this->l('Phone'), 'width' => 70),
			'fax' => array('title' => $this->l('Fax'), 'width' => 70),
			'active' => array('title' => $this->l('Enabled'), 'width' => 70, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);

	 	$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
			'enableSelection' => array('text' => $this->l('Enable selection')),
			'disableSelection' => array('text' => $this->l('Disable selection'))
			);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Parameters'),
				'fields' =>	array(
					'PS_STORES_DISPLAY_FOOTER' => array(
						'title' => $this->l('Display in the footer'),
						'desc' => $this->l('Display a link to the store locator in the footer'),
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_STORES_DISPLAY_SITEMAP' => array(
						'title' => $this->l('Display in the sitemap page'),
						'desc' => $this->l('Display a link to the store locator in the sitemap page'),
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_STORES_SIMPLIFIED' => array(
						'title' => $this->l('Show a simplified store locator'),
						'desc' => $this->l('No map, no search, only a store directory'),
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_STORES_CENTER_LAT' => array(
						'title' => $this->l('Latitude by default'),
						'desc' => $this->l('Used for the position by default of the map'),
						'cast' => 'floatval',
						'type' => 'text',
						'size' => '10'
					),
					'PS_STORES_CENTER_LONG' => array(
						'title' => $this->l('Longitude by default'),
						'desc' => $this->l('Used for the position by default of the map'),
						'cast' => 'floatval',
						'type' => 'text',
						'size' => '10'
					)
				)
			)
		);

		parent::__construct();

		$this->_buildOrderedFieldsShop($this->_getDefaultFieldsContent());
	}

	public function renderOptions()
	{
		// Set toolbar options
		$this->display = 'options';
		$this->show_toolbar = true;
		$this->toolbar_scroll = true;
		$this->initToolbar();

		return parent::renderOptions();
	}

	public function initToolbar()
	{
		parent::initToolbar();

		if ($this->display == 'options')
			unset($this->toolbar_btn['new']);
		else
			unset($this->toolbar_btn['save']);
	}

	public function renderList()
	{
		// Set toolbar options
		$this->display = null;
		$this->initToolbar();

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_select = 'cl.`name` country, st.`name` state';
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
				ON (cl.`id_country` = a.`id_country`
				AND cl.`id_lang` = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'state` st
				ON (st.`id_state` = a.`id_state`)';

		return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Stores'),
				'image' => '../img/admin/home.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'name' => 'name',
					'size' => 33,
					'required' => false,
					'hint' => sprintf($this->l('Allowed characters: letters, spaces and %s'), '().-'),
					'desc' => $this->l('Store name (e.g. Citycentre Mall Store)')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address'),
					'name' => 'address1',
					'size' => 33,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address (2)'),
					'name' => 'address2',
					'size' => 33
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postal Code/Zip Code'),
					'name' => 'postcode',
					'size' => 6,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('City'),
					'name' => 'city',
					'size' => 33,
					'class' => 'uppercase',
					'required' => true
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country'),
					'name' => 'id_country',
					'required' => true,
					'default_value' => (int)$this->context->country->id,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id),
						'id' => 'id_country',
						'name' => 'name',
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('State'),
					'name' => 'id_state',
					'required' => true,
					'options' => array(
						'id' => 'id_state',
						'name' => 'name',
                        'query' => null
					)
				),
				array(
					'type' => 'latitude',
					'label' => $this->l('Latitude / Longitude'),
					'name' => 'latitude',
					'required' => true,
					'size' => 11,
					'maxlength' => 12,
					'desc' => $this->l('Store coordinates (e.g. 45.265469/-47.226478)')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone'),
					'name' => 'phone',
					'size' => 33
				),
				array(
					'type' => 'text',
					'label' => $this->l('Fax'),
					'name' => 'fax',
					'size' => 33
				),
				array(
					'type' => 'text',
					'label' => $this->l('E-mail address'),
					'name' => 'email',
					'size' => 33
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Note'),
					'name' => 'note',
					'cols' => 42,
					'rows' => 4
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Whether or not to display this store')
				)
			),
			'rightCols' => array (
				'input' => array(
					'type' => 'file',
					'label' => $this->l('Picture'),
					'name' => 'image',
					'desc' => $this->l('Storefront picture')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);
		
		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}

		if (!($obj = $this->loadObject(true)))
			return;

		$image = ImageManager::thumbnail(_PS_STORE_IMG_DIR_.'/'.$obj->id.'.jpg', $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true);

		$days = array();
		$days[1] = $this->l('Monday');
		$days[2] = $this->l('Tuesday');
		$days[3] = $this->l('Wednesday');
		$days[4] = $this->l('Thursday');
		$days[5] = $this->l('Friday');
		$days[6] = $this->l('Saturday');
		$days[7] = $this->l('Sunday');

		$hours = $this->getFieldValue($obj, 'hours');
		if (!empty($hours))
			$hours_unserialized = Tools::unSerialize($hours);

		$this->fields_value = array(
			'latitude' => $this->getFieldValue($obj, 'latitude') ? $this->getFieldValue($obj, 'latitude') : Configuration::get('PS_STORES_CENTER_LAT'),
			'longitude' => $this->getFieldValue($obj, 'longitude') ? $this->getFieldValue($obj, 'longitude') : Configuration::get('PS_STORES_CENTER_LONG'),
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_STORE_IMG_DIR_.'/'.$obj->id.'.jpg') / 1000 : false,
			'days' => $days,
			'hours' => isset($hours_unserialized) ? $hours_unserialized : false
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]))
		{
			/* Cleaning fields */
			foreach ($_POST as $kp => $vp)
				if ($kp != 'checkBoxShopAsso_store')
					$_POST[$kp] = trim($vp);

			/* If the selected country does not contain states */
			$id_state = (int)Tools::getValue('id_state');
			$id_country = (int)Tools::getValue('id_country');
			$country = new Country((int)$id_country);

			if ($id_country && $country && !(int)$country->contains_states && $id_state)
				$this->errors[] = Tools::displayError('You have selected a state for a country that does not contain states.');

			/* If the selected country contains states, then a state have to be selected */
			if ((int)$country->contains_states && !$id_state)
				$this->errors[] = Tools::displayError('An address located in a country containing states must have a state selected.');

			$latitude = (float)Tools::getValue('latitude');
			$longitude = (float)Tools::getValue('longitude');

			if (empty($latitude) || empty($longitude))
			   $this->errors[] = Tools::displayError('Latitude and longitude are required.');

			/* Check zip code */
			if ($country->need_zip_code)
			{
				$zip_code_format = $country->zip_code_format;
				if (($postcode = Tools::getValue('postcode')) && $zip_code_format)
				{
					$zip_regexp = '/^'.$zip_code_format.'$/ui';
					$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
					$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
					$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
					$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
					$zip_regexp = str_replace('C', $country->iso_code, $zip_regexp);
					if (!preg_match($zip_regexp, $postcode))
						$this->errors[] = Tools::displayError('Your Postal Code/Zip Code is incorrect.').'<br />'.Tools::displayError('Must be typed as follows:').' '.
											str_replace(
												'C',
												$country->iso_code,
												str_replace(
													'N',
													'0',
													str_replace(
														'L',
														'A',
														$zip_code_format
													)
												)
											);
				}
				else if ($zip_code_format)
					$this->errors[] = Tools::displayError('Postal Code/Zip Code required.');
				else if ($postcode && !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
					$this->errors[] = Tools::displayError('Your Postal Code/Zip Code is incorrect.');
			}

			/* Store hours */
			$_POST['hours'] = array();
			for ($i = 1; $i < 8; $i++)
				$_POST['hours'][] .= Tools::getValue('hours_'.(int)$i);
			$_POST['hours'] = serialize($_POST['hours']);
		}

		if (!count($this->errors))
			parent::postProcess();
		else
			$this->display = 'add';
	}

	protected function postImage($id)
	{
		$ret = parent::postImage($id);
		if (($id_store = (int)Tools::getValue('id_store')) && isset($_FILES) && count($_FILES) && file_exists(_PS_STORE_IMG_DIR_.$id_store.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('stores');
			foreach ($images_types as $k => $image_type)
			{
				ImageManager::resize(_PS_STORE_IMG_DIR_.$id_store.'.jpg',
							_PS_STORE_IMG_DIR_.$id_store.'-'.stripslashes($image_type['name']).'.jpg',
							(int)$image_type['width'], (int)$image_type['height']
				);
			}
		}
		return $ret;
	}

	protected function _getDefaultFieldsContent()
	{
		$this->context = Context::getContext();
		$countryList = array();
		$countryList[] = array('id' => '0', 'name' => $this->l('Choose your country'));
		foreach (Country::getCountries($this->context->language->id) as $country)
			$countryList[] = array('id' => $country['id_country'], 'name' => $country['name']);
		$stateList = array();
		$stateList[] = array('id' => '0', 'name' => $this->l('Choose your state (if applicable)'));
		foreach (State::getStates($this->context->language->id) as $state)
			$stateList[] = array('id' => $state['id_state'], 'name' => $state['name']);

		$formFields = array(
			'PS_SHOP_NAME' => array(
				'title' => $this->l('Shop name'),
				'desc' => $this->l('Displayed in e-mails and page titles'),
				'validation' => 'isGenericName',
				'required' => true,
				'size' => 30,
				'type' => 'text'
			),
			'PS_SHOP_EMAIL' => array('title' => $this->l('Shop e-mail'),
				'desc' => $this->l('Displayed in e-mails sent to customers'),
				'validation' => 'isEmail',
				'required' => true,
				'size' => 30,
				'type' => 'text'
			),
			'PS_SHOP_DETAILS' => array(
				'title' => $this->l('Registration'),
				'desc' => $this->l('Shop registration information (e.g. SIRET or RCS)'),
				'validation' => 'isGenericName',
				'size' => 30,
				'type' => 'textarea',
				'cols' => 30,
				'rows' => 5
			),
			'PS_SHOP_ADDR1' => array(
				'title' => $this->l('Shop address line 1'),
				'validation' => 'isAddress',
				'size' => 30,
				'type' => 'text'
			),
			'PS_SHOP_ADDR2' => array(
				'title' => $this->l('Shop address line 2'),
				'validation' => 'isAddress',
				'size' => 30,
				'type' => 'text'
			),
			'PS_SHOP_CODE' => array(
				'title' => $this->l('Postal Code/Zip code'),
				'validation' => 'isGenericName',
				'size' => 6,
				'type' => 'text'
			),
			'PS_SHOP_CITY' => array(
				'title' => $this->l('City'),
				'validation' => 'isGenericName',
				'size' => 30,
				'type' => 'text'
			),
			'PS_SHOP_COUNTRY_ID' => array(
				'title' => $this->l('Country'),
				'validation' => 'isInt',
				'type' => 'select',
				'list' => $countryList,
				'identifier' => 'id',
				'cast' => 'intval',
				'defaultValue' => (int)$this->context->country->id
			),
			'PS_SHOP_STATE_ID' => array(
				'title' => $this->l('State'),
				'validation' => 'isInt',
				'type' => 'select',
				'list' => $stateList,
				'identifier' => 'id',
				'cast' => 'intval'
			),
			'PS_SHOP_PHONE' => array(
				'title' => $this->l('Phone'),
				'validation' => 'isGenericName',
				'size' => 30,
				'type' => 'text'
			),
			'PS_SHOP_FAX' => array(
				'title' => $this->l('Fax'),
				'validation' => 'isGenericName',
				'size' => 30,
				'type' => 'text'
			),
		);

		return $formFields;
	}

	protected function _buildOrderedFieldsShop($formFields)
	{
		// You cannot do that, because the fields must be sorted for the country you've selected.
		// Simple example: the current country is France, where we don't display the state. You choose "US" as a country in the form. The state is not dsplayed at the right place...
		
		// $associatedOrderKey = array(
			// 'PS_SHOP_NAME' => 'company',
			// 'PS_SHOP_ADDR1' => 'address1',
			// 'PS_SHOP_ADDR2' => 'address2',
			// 'PS_SHOP_CITY' => 'city',
			// 'PS_SHOP_STATE_ID' => 'State:name',
			// 'PS_SHOP_CODE' => 'postcode',
			// 'PS_SHOP_COUNTRY_ID' => 'Country:name',
			// 'PS_SHOP_PHONE' => 'phone');
		// $fields = array();
		// $orderedFields = AddressFormat::getOrderedAddressFields(Configuration::get('PS_SHOP_COUNTRY_ID'), false, true);
		// foreach ($orderedFields as $lineFields)
			// if (($patterns = explode(' ', $lineFields)))
				// foreach ($patterns as $pattern)
					// if (($key = array_search($pattern, $associatedOrderKey)))
						// $fields[$key] = $formFields[$key];
		// foreach ($formFields as $key => $value)
			// if (!isset($fields[$key]))
				// $fields[$key] = $formFields[$key];

		$fields = $formFields;
		$this->fields_options['contact'] = array(
			'title' =>	$this->l('Contact details'),
			'icon' =>	'tab-contact',
			'fields' =>	$fields,
			'submit' => array('title' => $this->l('   Save   '), 'class' => 'button')
		);
	}

	public function beforeUpdateOptions()
	{
		if (isset($_POST['PS_SHOP_STATE_ID']) && $_POST['PS_SHOP_STATE_ID'] != '0')
		{
			$sql = 'SELECT `active` FROM `'._DB_PREFIX_.'state`
					WHERE `id_country` = '.(int)Tools::getValue('PS_SHOP_COUNTRY_ID').'
						AND `id_state` = '.(int)Tools::getValue('PS_SHOP_STATE_ID');
			$isStateOk = Db::getInstance()->getValue($sql);
			if ($isStateOk != 1)
				$this->errors[] = Tools::displayError('This state is not in this country.');
		}
	}

	public function updateOptionPsShopCountryId($value)
	{
		if (!$this->errors && $value)
		{
			$country = new Country($value, $this->context->language->id);
			if ($country->id)
			{
				Configuration::updateValue('PS_SHOP_COUNTRY_ID', $value);
				Configuration::updateValue('PS_SHOP_COUNTRY', pSQL($country->name));
			}
		}
	}

	public function updateOptionPsShopStateId($value)
	{
		if (!$this->errors && $value)
		{
			$state = new State($value);
			if ($state->id)
			{
				Configuration::updateValue('PS_SHOP_STATE_ID', $value);
				Configuration::updateValue('PS_SHOP_STATE', pSQL($state->name));
			}
		}
	}
}