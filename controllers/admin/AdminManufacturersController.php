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

class AdminManufacturersControllerCore extends AdminController
{
	/** @var array countries list */
	protected $countries_array = array();

	public function __construct()
	{
	 	$this->table = 'manufacturer';
		$this->className = 'Manufacturer';
	 	$this->lang = false;
	 	$this->deleted = false;
		$this->allow_export = true;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->context = Context::getContext();

		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 'm'
		);

		$this->fields_list = array(
			'id_manufacturer' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'logo' => array(
				'title' => $this->l('Logo'),
				'image' => 'm',
				'orderby' => false,
				'search' => false,
				'width' => 150,
				'align' => 'center',
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto'
			),
			'addresses' => array(
				'title' => $this->l('Addresses'),
				'width' => 20,
				'align' => 'center',
				'havingFilter' => true
			),
			'products' => array(
				'title' => $this->l('Products:'),
				'havingFilter' => true,
				'width' => 20,
				'align' => 'center',
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'width' => 70,
				'active' => 'status',
				'type' => 'bool',
				'align' => 'center',
				'orderby' => false
			)
		);

		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function initListManufacturer()
	{
		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_select = '
			COUNT(`id_product`) AS `products`, (
				SELECT COUNT(ad.`id_manufacturer`) as `addresses`
				FROM `'._DB_PREFIX_.'address` ad
				WHERE ad.`id_manufacturer` = a.`id_manufacturer`
					AND ad.`deleted` = 0
				GROUP BY ad.`id_manufacturer`) as `addresses`';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product` p ON (a.`id_manufacturer` = p.`id_manufacturer`)';
		$this->_group = 'GROUP BY a.`id_manufacturer`';

		$this->context->smarty->assign('title_list', $this->l('List of manufacturers:'));

		$this->content .= parent::renderList();
	}

	public function initListManufacturerAddresses()
	{
		$this->toolbar_title = $this->l('Addresses');
		// reset actions and query vars
		$this->actions = array();
		unset($this->fields_list, $this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

		$this->table = 'address';
		$this->identifier = 'id_address';
		$this->deleted = true;
		$this->_orderBy = null;

		$this->addRowAction('editaddresses');
		$this->addRowAction('delete');

		// test if a filter is applied for this list
		if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false)
			$this->filter = true;

		// test if a filter reset request is required for this list
		if (isset($_POST['submitReset'.$this->table]))
			$this->action = 'reset_filters';
		else
			$this->action = '';

		// Sub tab addresses
		$countries = Country::getCountries($this->context->language->id);
		foreach ($countries as $country)
			$this->countries_array[$country['id_country']] = $country['name'];

		$this->fields_list = array(
			'id_address' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'manufacturer_name' => array(
				'title' => $this->l('Manufacturer'),
				'width' => 'auto'
			),
			'firstname' => array(
				'title' => $this->l('First name'),
				'width' => 80
			),
			'lastname' => array(
				'title' => $this->l('Last name'),
				'width' => 100,
				'filter_key' => 'a!name'
			),
			'postcode' => array(
				'title' => $this->l('Zip Code/Postal Code'),
				'align' => 'right',
				'width' => 50
			),
			'city' => array(
				'title' => $this->l('City'),
				'width' => 150
			),
			'country' => array(
				'title' => $this->l('Country'),
				'width' => 100,
				'type' => 'select',
				'list' => $this->countries_array,
				'filter_key' => 'cl!id_country'
			)
		);

		$this->_select = 'cl.`name` as country, m.`name` AS manufacturer_name';
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl
				ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.') ';
		$this->_join .= '
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
				ON (a.`id_manufacturer` = m.`id_manufacturer`)';
		$this->_where = 'AND a.`id_customer` = 0 AND a.`id_supplier` = 0 AND a.`id_warehouse` = 0';

		$this->context->smarty->assign('title_list', $this->l('Manufacturers addresses:'));

		// call postProcess() for take care about actions and filters
		$this->postProcess();

		$this->initToolbar();
		$this->content .= parent::renderList();

	}

	public function renderList()
	{
		$this->initListManufacturer();
		$this->initListManufacturerAddresses();
	}

	/**
	 * Display editaddresses action link
	 * @param string $token the token to add to the link
	 * @param int $id the identifier to add to the link
	 * @return string
	 */
	public function displayEditaddressesLink($token = null, $id)
	{
		if (!array_key_exists('editaddresses', self::$cache_lang))
			self::$cache_lang['editaddresses'] = $this->l('Edit Adresses');

		$this->context->smarty->assign(array(
			'href' => self::$currentIndex.
				'&'.$this->identifier.'='.$id.
				'&editaddresses&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['editaddresses'],
		));

		return $this->context->smarty->fetch('helpers/list/list_action_edit.tpl');
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Manufacturers:'),
				'image' => '../img/admin/manufacturers.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 40,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Short description:'),
					'name' => 'short_description',
					'lang' => true,
					'cols' => 60,
					'rows' => 10,
					'class' => 'rte',
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'lang' => true,
					'cols' => 60,
					'rows' => 10,
					'class' => 'rte',
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'file',
					'label' => $this->l('Logo:'),
					'name' => 'logo',
					'display_image' => true,
					'desc' => $this->l('Upload a manufacturer logo from your computer.')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title:'),
					'name' => 'meta_title',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'meta_description',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords:'),
					'name' => 'meta_keywords',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}',
					'desc' => $this->l('To add "tags," click inside the field, write something, and then press "Enter."')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Enable:'),
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
					)
				)
			)
		);

		if (!($manufacturer = $this->loadObject(true)))
			return;

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

		$image = ImageManager::thumbnail(_PS_MANU_IMG_DIR_.'/'.$manufacturer->id.'.jpg', $this->table.'_'.(int)$manufacturer->id.'.'.$this->imageType, 350, $this->imageType, true);

		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_MANU_IMG_DIR_.'/'.$manufacturer->id.'.jpg') / 1000 : false
		);

		foreach ($this->_languages as $language)
		{
			$this->fields_value['short_description_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
				$manufacturer,
				'short_description',
				$language['id_lang']
			)), ENT_COMPAT, 'UTF-8');

			$this->fields_value['description_'.$language['id_lang']] = htmlentities(stripslashes($this->getFieldValue(
				$manufacturer,
				'description',
				$language['id_lang']
			)), ENT_COMPAT, 'UTF-8');
		}

		return parent::renderForm();
	}

	public function renderFormAddress()
	{
		// Change table and className for addresses
	 	$this->table = 'address';
	 	$this->className = 'Address';
	 	$id_address = Tools::getValue('id_address');

	 	// Create Object Address
		$address = new Address($id_address);

		$form = array(
			'legend' => array(
				'title' => $this->l('Addresses'),
				'image' => '../img/admin/contact.gif'
			)
		);

		if (!$address->id_manufacturer || !Manufacturer::manufacturerExists($address->id_manufacturer))
			$form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Choose the manufacturer:'),
				'name' => 'id_manufacturer',
				'options' => array(
					'query' => Manufacturer::getManufacturers(),
					'id' => 'id_manufacturer',
					'name' => 'name'
				)
			);
		else
		{
			$form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Manufacturer:'),
				'name' => 'name',
				'disabled' => true,
			);

			$form['input'][] = array(
				'type' => 'hidden',
				'name' => 'id_manufacturer'
			);
		}

		$form['input'][] = array(
			'type' => 'hidden',
			'name' => 'alias',
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Last name:'),
			'name' => 'lastname',
			'size' => 33,
			'required' => true,
			'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:'
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('First name:'),
			'name' => 'firstname',
			'size' => 33,
			'required' => true,
			'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:'
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Address:'),
			'name' => 'address1',
			'size' => 33,
			'required' => true,
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Address (2):'),
			'name' => 'address2',
			'size' => 33,
			'required' => false,
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Zip Code/Postal Code'),
			'name' => 'postcode',
			'size' => 33,
			'required' => false,
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('City:'),
			'name' => 'city',
			'size' => 33,
			'required' => true,
		);
		$form['input'][] = array(
			'type' => 'select',
			'label' => $this->l('Country:'),
			'name' => 'id_country',
			'required' => false,
			'default_value' => (int)$this->context->country->id,
			'options' => array(
				'query' => Country::getCountries($this->context->language->id),
				'id' => 'id_country',
				'name' => 'name',
			)
		);
		$form['input'][] = array(
			'type' => 'select',
			'label' => $this->l('State:'),
			'name' => 'id_state',
			'required' => false,
			'options' => array(
				'query' => array(),
				'id' => 'id_state',
				'name' => 'name'
			)
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Home phone:'),
			'name' => 'phone',
			'size' => 33,
			'required' => false,
		);
		$form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Mobile phone:'),
			'name' => 'phone_mobile',
			'size' => 33,
			'required' => false,
		);
		$form['input'][] = array(
			'type' => 'textarea',
			'label' => $this->l('Other:'),
			'name' => 'other',
			'cols' => 36,
			'rows' => 4,
			'required' => false,
			'hint' => $this->l('Forbidden characters:').' <>;=#{}'
		);
		$form['submit'] = array(
			'title' => $this->l('Save   '),
			'class' => 'button'
		);

		$this->fields_value = array(
			'name' => Manufacturer::getNameById($address->id_manufacturer),
			'alias' => 'manufacturer',
			'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
		);

		$this->initToolbar();
		$this->fields_form[0]['form'] = $form;
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = $this->table;
		$helper->identifier = $this->identifier;
		$helper->title = $this->l('Edit Addresses');
		$helper->id = $address->id;
		$helper->toolbar_scroll = true;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->getFieldsValue($address);
		$helper->toolbar_btn = $this->toolbar_btn;
		$this->content .= $helper->generateForm($this->fields_form);
	}

	/**
	 * AdminController::initToolbar() override
	 * @see AdminController::initToolbar()
	 *
	 */
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'editaddresses':
			case 'addaddress':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);

				// Default cancel button - like old back link
				if (!isset($this->no_back) || $this->no_back == false)
				{
					$back = Tools::safeOutput(Tools::getValue('back', ''));
					if (empty($back))
						$back = self::$currentIndex.'&token='.$this->token;

					$this->toolbar_btn['cancel'] = array(
						'href' => $back,
						'desc' => $this->l('Cancel')
					);
				}
			break;

			default:
				parent::initToolbar();
				$this->toolbar_btn['import'] = array(
					'href' => $this->context->link->getAdminLink('AdminImport', true).'&import_type='.$this->table,
					'desc' => $this->l('Import')
				);
		}
	}

	public function renderView()
	{
		if (!($manufacturer = $this->loadObject()))
			return;

		$addresses = $manufacturer->getAddresses($this->context->language->id);

		$products = $manufacturer->getProductsLite($this->context->language->id);
		$total_product = count($products);
		for ($i = 0; $i < $total_product; $i++)
		{
			$products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
			$products[$i]->loadStockData();
			/* Build attributes combinations */
			$combinations = $products[$i]->getAttributeCombinations($this->context->language->id);
			foreach ($combinations as $k => $combination)
			{
				$comb_array[$combination['id_product_attribute']]['reference'] = $combination['reference'];
				$comb_array[$combination['id_product_attribute']]['ean13'] = $combination['ean13'];
				$comb_array[$combination['id_product_attribute']]['upc'] = $combination['upc'];
				$comb_array[$combination['id_product_attribute']]['quantity'] = $combination['quantity'];
				$comb_array[$combination['id_product_attribute']]['attributes'][] = array(
					$combination['group_name'],
					$combination['attribute_name'],
					$combination['id_attribute']
				);
			}

			if (isset($comb_array))
			{
				foreach ($comb_array as $key => $product_attribute)
				{
					$list = '';
					foreach ($product_attribute['attributes'] as $attribute)
						$list .= $attribute[0].' - '.$attribute[1].', ';
					$comb_array[$key]['attributes'] = rtrim($list, ', ');
				}
				isset($comb_array) ? $products[$i]->combination = $comb_array : '';
				unset($comb_array);
			}
		}

		$this->tpl_view_vars = array(
			'manufacturer' => $manufacturer,
			'addresses' => $addresses,
			'products' => $products,
			'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
			'shopContext' => Shop::getContext(),
		);

		return parent::renderView();
	}

	public function initContent()
	{
		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		if ($this->display == 'editaddresses' || $this->display == 'addaddress')
			$this->content .= $this->renderFormAddress();
		else if ($this->display == 'edit' || $this->display == 'add')
		{
			if (!$this->loadObject(true))
				return;
			$this->content .= $this->renderForm();
		}
		else if ($this->display == 'view')
		{
			// Some controllers use the view action without an object
			if ($this->className)
				$this->loadObject(true);
			$this->content .= $this->renderView();
		}
		else if (!$this->ajax)
		{
			$this->content .= $this->renderList();
			$this->content .= $this->renderOptions();
		}

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function init()
	{
		parent::init();

		if (Tools::isSubmit('editaddresses'))
			$this->display = 'editaddresses';
		else if (Tools::isSubmit('addaddress'))
			$this->display = 'addaddress';
		else if (Tools::isSubmit('submitAddaddress'))
			$this->action = 'save';
		else if (Tools::isSubmit('deleteaddress'))
			$this->action = 'delete';
	}

	public function initProcess()
	{
		if (Tools::getValue('submitAddaddress') || Tools::isSubmit('deleteaddress') || Tools::isSubmit('submitBulkdeleteaddress'))
		{
			$this->table = 'address';
			$this->className = 'Address';
			$this->identifier = 'id_address';
			$this->deleted = true;
		}
		parent::initProcess();
	}

	protected function afterImageUpload()
	{
		$res = true;

		/* Generate image with differents size */
		if (($id_manufacturer = (int)Tools::getValue('id_manufacturer')) &&
			isset($_FILES) &&
			count($_FILES) &&
			file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('manufacturers');
			foreach ($images_types as $k => $image_type)
			{
				$res &= ImageManager::resize(
					_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
					_PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg',
					(int)$image_type['width'],
					(int)$image_type['height']
				);
			}
		}

		if (!$res)
			$this->errors[] = Tools::displayError('Unable to resize one or more of your pictures.');

		return $res;
	}
}


