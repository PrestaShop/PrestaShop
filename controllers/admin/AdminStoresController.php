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
		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldImageSettings = array(
			'name' => 'image',
			'dir' => 'st'
		);

		$this->fieldsDisplay = array(
			'id_store' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'country' => array(
				'title' => $this->l('Country'),
				'width' => 100,
				'filter_key' => 'cl!name'
			),
			'state' => array(
				'title' => $this->l('State'),
				'width' => 100,
				'filter_key' => 'st!name'
			),
			'city' => array('title' => $this->l('City'), 'width' => 100),
			'postcode' => array('title' => $this->l('Zip code'), 'width' => 50),
			'name' => array('title' => $this->l('Name'), 'width' => 120, 'filter_key' => 'a!name'),
			'phone' => array('title' => $this->l('Phone'), 'width' => 70),
			'fax' => array('title' => $this->l('Fax'), 'width' => 70),
			'active' => array('title' => $this->l('Enabled'), 'width' => 70, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Parameters'),
				'fields' =>	array(
					'PS_STORES_DISPLAY_FOOTER' => array(
						'title' => $this->l('Display in the footer:'),
						'desc' => $this->l('Display a link to the store locator in the footer'),
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_STORES_DISPLAY_SITEMAP' => array(
						'title' => $this->l('Display in the sitemap page:'),
						'desc' => $this->l('Display a link to the store locator in the sitemap page'),
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_STORES_SIMPLIFIED' => array(
						'title' => $this->l('Show a simplified store locator:'),
						'desc' => $this->l('No map, no search, only a store directory'),
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_STORES_CENTER_LAT' => array(
						'title' => $this->l('Latitude by default:'),
						'desc' => $this->l('Used for the position by default of the map'),
						'cast' => 'floatval',
						'type' => 'text',
						'size' => '10'
					),
					'PS_STORES_CENTER_LONG' => array(
						'title' => $this->l('Longitude by default:'),
						'desc' => $this->l('Used for the position by default of the map'),
						'cast' => 'floatval',
						'type' => 'text',
						'size' => '10'
					)
				),
				'submit' => array()
			)
		);

		parent::__construct();
	}

	public function renderList()
	{
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
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 33,
					'required' => false,
					'hint' => $this->l('Allowed characters: letters, spaces and').' (-)',
					'desc' => $this->l('Store name, e.g. Citycentre Mall Store')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address:'),
					'name' => 'address1',
					'size' => 33,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address (2):'),
					'name' => 'address2',
					'size' => 33
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postcode/ Zip Code:'),
					'name' => 'postcode',
					'size' => 6,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('City:'),
					'name' => 'city',
					'size' => 33,
					'class' => 'uppercase',
					'required' => true
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'id_country',
					'required' => true,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id),
						'id' => 'id_country',
						'name' => 'name',
						'preselect_country' => true,
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('State:'),
					'name' => 'id_state',
					'required' => true,
					'options' => array(
						'id' => 'id_state',
						'name' => 'name'
					)
				),
				array(
					'type' => 'latitude',
					'label' => $this->l('Latitude / Longitude:'),
					'name' => 'latitude',
					'required' => true,
					'size' => 11,
					'maxlength' => 12,
					'desc' => $this->l('Store coords, eg. 45.265469 / -47.226478')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone:'),
					'name' => 'phone',
					'size' => 33
				),
				array(
					'type' => 'text',
					'label' => $this->l('Fax:'),
					'name' => 'fax',
					'size' => 33
				),
				array(
					'type' => 'text',
					'label' => $this->l('E-mail address:'),
					'name' => 'email',
					'size' => 33
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Note:'),
					'name' => 'note',
					'cols' => 42,
					'rows' => 4
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
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
					'desc' => $this->l('Display or not this store')
				)
			),
			'rightCols' => array (
				'input' => array(
					'type' => 'file',
					'label' => $this->l('Picture:'),
					'name' => 'image',
					'desc' => $this->l('Store window picture')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

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
			$hours_unserialized = unserialize($hours);

		$this->fields_value = array(
			'latitude' => $this->getFieldValue($obj, 'latitude') ? $this->getFieldValue($obj, 'latitude') : Configuration::get('PS_STORES_CENTER_LAT'),
			'longitude' => $this->getFieldValue($obj, 'longitude') ? $this->getFieldValue($obj, 'longitude') : Configuration::get('PS_STORES_CENTER_LONG'),
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_STORE_IMG_DIR_.'/'.$obj->id.'.jpg') / 1000 : false,
			'days' => $days,
			'hours' => isset($hours_unserialized) ? $hours_unserialized : false,
			'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]))
		{
			/* Cleaning fields */
			foreach ($_POST as $kp => $vp)
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
						$this->errors[] = Tools::displayError('Your zip/postal code is incorrect.').'<br />'.Tools::displayError('Must be typed as follows:').' '.
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
					$this->errors[] = Tools::displayError('Postcode required.');
				else if ($postcode && !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
					$this->errors[] = Tools::displayError('Your zip/postal code is incorrect.');
			}

			/* Store hours */
			$_POST['hours'] = array();
			for ($i = 1; $i < 8; $i++)
				$_POST['hours'][] .= Tools::getValue('hours_'.(int)$i);
			$_POST['hours'] = serialize($_POST['hours']);
		}

		if (!count($this->errors))
			parent::postProcess();
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

}


