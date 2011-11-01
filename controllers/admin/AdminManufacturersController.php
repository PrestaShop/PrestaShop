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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminManufacturersControllerCore extends AdminController
{
	/** @var array countries list */
	private $countries_array = array();

	public function __construct()
	{
	 	$this->table = 'manufacturer';
		$this->className = 'Manufacturer';
	 	$this->lang = false;
	 	$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		$this->fieldImageSettings = array(
			'name' => 'logo',
			'dir' => 'm'
		);

		$this->fieldsDisplay = array(
			'id_manufacturer' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 200
			),
			'logo' => array(
				'title' => $this->l('Logo'),
				'image' => 'm',
				'orderby' => false,
				'search' => false
			),
			'addresses' => array(
				'title' => $this->l('Addresses'),
				'tmpTableFilter' => true,
				'width' => 20
			),
			'products' => array(
				'title' => $this->l('Products'),
				'tmpTableFilter' => true,
				'width' => 20
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'width' => 25,
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false
			)
		);

		parent::__construct();
	}

	public function initList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('view');

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

		$this->initToolbar();
		$this->content .= parent::initList();

		// reset actions and query vars
		$this->actions = array();
		unset($this->fieldsDisplay, $this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

		$this->table = 'address';
	 	$this->identifier = 'id_address';
		$this->deleted = true;

		$this->addRowAction('editaddresses');
		$this->addRowAction('delete');

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

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

		$this->fieldsDisplay = array(
			'id_address' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'manufacturer_name' => array(
				'title' => $this->l('Manufacturer'),
				'width' => 100
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
				'title' => $this->l('Postcode/ Zip Code'),
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
				'select' => $this->countries_array,
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
	 	$this->_where = 'AND a.`id_customer` = 0';

	 	$this->context->smarty->assign('title_list', $this->l('Manufacturers addresses:'));

		// call postProcess() for take care about actions and filters
		$this->postProcess();

		$this->initToolbar();
		$this->content .= parent::initList();
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

        return $this->context->smarty->fetch(_PS_ADMIN_DIR_.'/themes/template/manufacturers/list_action_edit_adresses.tpl');
	}

	public function initForm()
	{
		$lang_tags = 'short_description¤description¤meta_title¤meta_keywords¤meta_description';
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Manufacturers'),
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
					'attributeLang' => $lang_tags,
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
					'attributeLang' => $lang_tags,
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
					'p' => $this->l('Upload manufacturer logo from your computer')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title:'),
					'name' => 'meta_title',
					'lang' => true,
					'attributeLang' => $lang_tags,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'meta_description',
					'lang' => true,
					'attributeLang' => $lang_tags,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta keywords:'),
					'name' => 'meta_keywords',
					'lang' => true,
					'attributeLang' => $lang_tags,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
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
				'type' => 'group_shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
				'values' => Shop::getTree()
			);
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		$image = cacheImage(_PS_MANU_IMG_DIR_.'/'.$manufacturer->id.'.jpg', $this->table.'_'.(int)$manufacturer->id.'.'.$this->imageType, 350, $this->imageType, true);

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

		//Added values of object Shop
		if ($manufacturer->id)
		{
			$assos = array();
			$sql = 'SELECT `id_group_shop`, `'.pSQL($this->identifier).'`
					FROM `'._DB_PREFIX_.pSQL($this->table).'_group_shop`
					WHERE `'.pSQL($this->identifier).'` = '.(int)$manufacturer->id;
			foreach (Db::getInstance()->executeS($sql) as $row)
				$this->fields_value['shop'][$row['id_group_shop']][] = $row[$this->identifier];
		}

		return parent::initForm();
	}

	public function initFormAddress()
	{
		// Change table and className for addresses
	 	$this->table = 'address';
	 	$this->className = 'Address';
	 	$id_address = Tools::getValue('id_address');

	 	// Create Object Address
		$address = new Address($id_address);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Addresses'),
				'image' => '../img/admin/contact.gif'
			)
		);

		if (!$address->id_manufacturer || !Manufacturer::manufacturerExists($address->id_manufacturer))
			$this->fields_form['input'][] = array(
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
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Manufacturer:'),
				'name' => 'name',
				'disabled' => true,
			);

			$this->fields_form['input'][] = array(
				'type' => 'hidden',
				'name' => 'id_manufacturer'
			);
		}

		$this->fields_form['input'][] = array(
			'type' => 'hidden',
			'name' => 'alias',
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Last name:'),
			'name' => 'lastname',
			'size' => 33,
			'required' => true,
			'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:'
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('First name:'),
			'name' => 'firstname',
			'size' => 33,
			'required' => true,
			'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:'
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Address:'),
			'name' => 'address1',
			'size' => 33,
			'required' => true,
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Address (2):'),
			'name' => 'address2',
			'size' => 33,
			'required' => false,
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Postcode / Zip Code:'),
			'name' => 'postcode',
			'size' => 33,
			'required' => false,
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('City:'),
			'name' => 'city',
			'size' => 33,
			'required' => true,
		);
		$this->fields_form['input'][] = array(
			'type' => 'select',
			'label' => $this->l('Country:'),
			'name' => 'id_country',
			'required' => false,
			'options' => array(
				'query' => Country::getCountries($this->context->language->id),
				'id' => 'id_country',
				'name' => 'name'
			)
		);
		$this->fields_form['input'][] = array(
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
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Home phone:'),
			'name' => 'phone',
			'size' => 33,
			'required' => false,
		);
		$this->fields_form['input'][] = array(
			'type' => 'text',
			'label' => $this->l('Mobile phone:'),
			'name' => 'phone_mobile',
			'size' => 33,
			'required' => false,
		);
		$this->fields_form['input'][] = array(
			'type' => 'textarea',
			'label' => $this->l('Other:'),
			'name' => 'other',
			'cols' => 36,
			'rows' => 4,
			'required' => false,
			'hint' => $this->l('Forbidden characters:').' <>;=#{}'
		);
		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		$this->fields_value = array(
			'name' => Manufacturer::getNameById($address->id_manufacturer),
			'alias' => 'manufacturer'
		);

		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = $this->table;
		$helper->identifier = $this->identifier;
		$helper->id = $address->id;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->getFieldsValue($address);
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->tpl = 'manufacturers/form_addresses.tpl';
		$this->content .= $helper->generateForm($this->fields_form);
	}

	public function initView()
	{
		if (!($manufacturer = $this->loadObject()))
			return;

		$addresses = $manufacturer->getAddresses($this->context->language->id);

		$products = $manufacturer->getProductsLite($this->context->language->id);
		$total_product = count($products);
		for ($i = 0; $i < $total_product; $i++)
		{
			$products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
			/* Build attributes combinaisons */
			$combinaisons = $products[$i]->getAttributeCombinaisons($this->context->language->id);
			foreach ($combinaisons as $k => $combinaison)
			{
				$comb_array[$combinaison['id_product_attribute']]['reference'] = $combinaison['reference'];
				$comb_array[$combinaison['id_product_attribute']]['ean13'] = $combinaison['ean13'];
				$comb_array[$combinaison['id_product_attribute']]['upc'] = $combinaison['upc'];
				$comb_array[$combinaison['id_product_attribute']]['quantity'] = $combinaison['quantity'];
				$comb_array[$combinaison['id_product_attribute']]['attributes'][] = array(
					$combinaison['group_name'],
					$combinaison['attribute_name'],
					$combinaison['id_attribute']
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
				isset($comb_array) ? $products[$i]->combinaison = $comb_array : '';
				unset($comb_array);
			}
		}

		$this->context->smarty->assign(array(
			'manufacturer' => $manufacturer,
			'addresses' => $addresses,
			'products' => $products
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
		else if (is_array($this->bulk_actions))
		{
			foreach ($this->bulk_actions as $bulk_action => $params)
			{
				if (Tools::isSubmit('submitBulk'.$bulk_action.$this->table))
				{
					$this->action = 'bulk'.$bulk_action;
					$this->boxes = Tools::getValue($this->table.'Box');
					break;
				}
			}
			if ($this->ajax && method_exists($this, 'ajaxPreprocess'))
				$this->ajaxPreProcess();
		}
	}

	public function initContent()
	{
		if ($this->display == 'edit' || $this->display == 'add')
		{
			if (!($this->object = $this->loadObject(true)))
				return;
			$this->content .= $this->initForm();
		}
		else if ($this->display == 'editaddresses' || $this->display == 'addaddress')
			$this->content .= $this->initFormAddress();
		else if ($this->display == 'view')
			$this->content .= $this->initView();
		else
		{
			$this->content .= $this->initList();
			$this->content .= $this->initOptions();
		}

		$this->context->smarty->assign(array(
			'table' => $this->table,
			'current' => self::$currentIndex,
			'token' => $this->token,
			'content' => $this->content
		));
	}

	public function postProcess()
	{
		if (Tools::getValue('submitAddaddress') || Tools::isSubmit('deleteaddress'))
		{
			$this->table = 'address';
			$this->className = 'Address';
	 		$this->identifier = 'id_address';
			$this->deleted = true;
		}
		parent::postProcess();
	}

	public function afterImageUpload()
	{
		/* Generate image with differents size */
		if (($id_manufacturer = (int)Tools::getValue('id_manufacturer')) &&
			isset($_FILES) &&
			count($_FILES) &&
			file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('manufacturers');
			foreach ($images_types as $k => $image_type)
				imageResize(
					_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
					_PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg',
					(int)$image_type['width'],
					(int)$image_type['height']
				);
		}
	}
}


