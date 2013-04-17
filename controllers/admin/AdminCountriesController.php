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

class AdminCountriesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'country';
		$this->className = 'Country';
	 	$this->lang = true;
		$this->deleted = false;

		$this->explicitSelect = true;
		$this->addRowAction('edit');

		$this->context = Context::getContext();
		
		$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
			'enableSelection' => array('text' => $this->l('Enable selection')),
			'disableSelection' => array('text' => $this->l('Disable selection')),
			'affectzone' => array('text' => $this->l('Assign to a new zone'))
		);
		
		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 'st'
		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Country options'),
				'fields' =>	array(
					'PS_RESTRICT_DELIVERED_COUNTRIES' => array(
						'title' => $this->l('Restrict country selections in Front Office to those covered by active carriers'),
						'cast' => 'intval',
						'type' => 'bool',
						'default' => '0'
					)
				),
				'submit' => array()
			)
		);

		$this->fields_list = array(
			'id_country' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Country'),
				'width' => 'auto',
				'filter_key' => 'b!name'
			),
			'iso_code' => array(
				'title' => $this->l('ISO code'),
				'width' => 70,
				'align' => 'center'
			),
			'call_prefix' => array(
				'title' => $this->l('Call prefix'),
				'width' => 150,
				'align' => 'center',
				'callback' => 'displayCallPrefix'
			),
			'zone' => array(
				'title' => $this->l('Zone'),
				'width' => 100,
				'filter_key' => 'z!name'
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'a!active',
				'width' => 25
			)
		);

		parent::__construct();
	}

	/**
	 * AdminController::setMedia() override
	 * @see AdminController::setMedia()
	 */
	public function setMedia()
	{
		parent::setMedia();

		$this->addJqueryPlugin('fieldselection');
	}

	public function renderList()
	{
	 	$this->_select = 'z.`name` AS zone';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = a.`id_zone`)';

		$this->tpl_list_vars['zones'] = Zone::getZones();
	 	return parent::renderList();
	}

	public function renderForm()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$address_layout = AddressFormat::getAddressCountryFormat($obj->id);
		if ($value = Tools::getValue('address_layout'))
			$address_layout = $value;

		$default_layout = '';

		$default_layout_tab = array(
			array('firstname', 'lastname'),
			array('company'),
			array('vat_number'),
			array('address1'),
			array('address2'),
			array('postcode', 'city'),
			array('Country:name'),
			array('phone'));

		foreach ($default_layout_tab as $line)
			$default_layout .= implode(' ', $line)."\r\n";

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Countries'),
				'image' => '../img/admin/world.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Country:'),
					'name' => 'name',
					'lang' => true,
					'size' => 30,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Country name')
				),
				array(
					'type' => 'text',
					'label' => $this->l('ISO code:'),
					'name' => 'iso_code',
					'size' => 4,
					'maxlength' => 3,
					'class' => 'uppercase',
					'required' => true,
					'desc' => $this->l('Two -- or three -- letter ISO code (e.g. U.S. for United States)').'.
							<a href="http://www.iso.org/iso/country_codes/iso_3166_code_lists/country_names_and_code_elements.htm" target="_blank">'.
								$this->l('Official list here').'
							</a>.'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Call prefix:'),
					'name' => 'call_prefix',
					'size' => 4,
					'maxlength' => 3,
					'class' => 'uppercase',
					'required' => true,
					'desc' => $this->l('International call prefix, (e.g. 1 for United States)')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Default currency:'),
					'name' => 'id_currency',
					'options' => array(
						'query' => Currency::getCurrencies(),
						'id' => 'id_currency',
						'name' => 'name',
						'default' => array(
							'label' => $this->l('Default store currency'),
							'value' => 0
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Zone:'),
					'name' => 'id_zone',
					'options' => array(
						'query' => Zone::getZones(),
						'id' => 'id_zone',
						'name' => 'name'
					),
					'desc' => $this->l('Geographical region')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Need zip/postal code:'),
					'name' => 'need_zip_code',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'need_zip_code_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'need_zip_code_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Zip/post code format:'),
					'name' => 'zip_code_format',
					'class' => 'uppercase',
					'required' => true,
					'desc' => $this->l('Zip Code format (L for a letter, N for a number and C for the ISO code). For example, NNNNN for the United States. No verification if undefined.')
				),
				array(
					'type' => 'address_layout',
					'label' => $this->l('Address format:'),
					'name' => 'address_layout',
					'address_layout' => $address_layout,
					'encoding_address_layout' => urlencode($address_layout),
					'encoding_default_layout' => urlencode($default_layout),
					'display_valid_fields' => $this->displayValidFields()
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Active:'),
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
					'desc' => $this->l('Display this country to your customers (the selected country will always be displayed in the Back Office)')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Contains following  states:'),
					'name' => 'contains_states',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'contains_states_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />'.$this->l('Yes')
						),
						array(
							'id' => 'contains_states_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" />'.$this->l('No')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Do you need a tax identification number?'),
					'name' => 'need_identification_number',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'need_identification_number_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />'.$this->l('Yes')
						),
						array(
							'id' => 'need_identification_number_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" />'.$this->l('No')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Display tax label (e.g. "Tax incl."):'),
					'name' => 'display_tax_label',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'display_tax_label_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />'.$this->l('Yes')
						),
						array(
							'id' => 'display_tax_label_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" />'.$this->l('No')
						)
					)
				)
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

		$this->fields_form['submit'] = array(
			'title' => $this->l('Save   '),
			'class' => 'button'
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (!Tools::getValue('id_'.$this->table))
		{
			if (Validate::isLanguageIsoCode(Tools::getValue('iso_code')) && Country::getByIso(Tools::getValue('iso_code')))
				$this->errors[] = Tools::displayError('This ISO code already exists.You cannot create two countries with the same ISO code.');
		}
		else if (Validate::isLanguageIsoCode(Tools::getValue('iso_code')))
		{
			$id_country = Country::getByIso(Tools::getValue('iso_code'));
			if (!is_null($id_country) && $id_country != Tools::getValue('id_'.$this->table))
				$this->errors[] = Tools::displayError('This ISO code already exists.You cannot create two countries with the same ISO code.');
		}

		if (!count($this->errors))
			$res = parent::postProcess();
		else
			return false;

		if (Tools::getValue('submitAdd'.$this->table) && $res)
		{
			$id_country = ($id_country = Tools::getValue('id_country')) ? $id_country : $res['id'];
			$tmp_addr_format = new AddressFormat($id_country);

			$save_status = false;

			$is_new = is_null($tmp_addr_format->id_country);
			if ($is_new)
			{
				$tmp_addr_format = new AddressFormat();
				$tmp_addr_format->id_country = $id_country;
			}

			$tmp_addr_format->format = Tools::getValue('address_layout');

			if (strlen($tmp_addr_format->format) > 0)
			{
				if ($tmp_addr_format->checkFormatFields())
					$save_status = ($is_new) ? $tmp_addr_format->save(): $tmp_addr_format->update();
				else
				{
					$error_list = $tmp_addr_format->getErrorList();
					foreach ($error_list as $num_error => $error)
						$this->errors[] = $error;
				}

				if (!$save_status)
					$this->errors[] = Tools::displayError('Invalid address layout '.Db::getInstance()->getMsgError());
			}
			unset($tmp_addr_format);
		}

		return $res;
	}
	
	public function processStatus()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
			Country::addModuleRestrictions(array(), array(array('id_country' => $object->id)), array());
		
		parent::processStatus();
	}
	
	public function processBulkStatusSelection($way)
	{
		if (is_array($this->boxes) && !empty($this->boxes))
		{
			$countries_ids = array();
			foreach ($this->boxes as $id)
				$countries_ids[] = array('id_country' => $id);

			if (count($countries_ids))
				Country::addModuleRestrictions(array(), $countries_ids, array());
		}
		parent::processBulkStatusSelection($way);
	}


	protected function displayValidFields()
	{
		$html = '<ul>';

		$object_list = AddressFormat::getLiableClass('Address');
		$object_list['Address'] = null;

		// Get the available properties for each class
		foreach ($object_list as $class_name => &$object)
		{
			$fields = array();

			$html .= '<li>
				<a href="javascript:void(0);" onClick="displayAvailableFields(\''.$class_name.'\')">'.$class_name.'</a>';
			foreach (AddressFormat::getValidateFields($class_name) as $name)
				$fields[] = '<a style="color:#4B8;" href="javascript:void(0);" class="addPattern" id="'.($class_name == 'Address' ? $name : $class_name.':'.$name).'">
					'.$name.'</a>';
			$html .= '
				<div class="availableFieldsList" id="availableListFieldsFor_'.$class_name.'" style="width:300px;">
				'.implode(', ', $fields).'</div></li>';
			unset($object);
		}
		return $html .= '</ul>';
	}

	public static function displayCallPrefix($prefix)
	{
		return ((int)$prefix ? '+'.$prefix : '-');
	}
}


