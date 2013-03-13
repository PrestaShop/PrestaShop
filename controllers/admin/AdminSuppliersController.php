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

class AdminSuppliersControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'supplier';
		$this->className = 'Supplier';

		$this->addRowAction('view');
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->allow_export = true;
		
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->_select = 'COUNT(DISTINCT ps.`id_product`) AS products';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_supplier` ps ON (a.`id_supplier` = ps.`id_supplier`)';
		$this->_group = 'GROUP BY a.`id_supplier`';

		$this->fieldImageSettings = array('name' => 'logo', 'dir' => 'su');

		$this->fields_list = array(
			'id_supplier' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'logo' => array('title' => $this->l('Logo'), 'width' => 150, 'align' => 'center', 'image' => 'su', 'orderby' => false, 'search' => false),
			'name' => array('title' => $this->l('Name'), 'width' => 'auto'),
			'products' => array('title' => $this->l('Number of products'), 'width' => 70, 'align' => 'right', 'filter_type' => 'int', 'tmpTableFilter' => true),
			'active' => array('title' => $this->l('Enabled'), 'width' => 70, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);

		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function renderForm()
	{
		// loads current warehouse
		if (!($obj = $this->loadObject(true)))
			return;

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Suppliers'),
				'image' => '../img/admin/suppliers.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_address',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'name' => 'name',
					'size' => 40,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'cols' => 60,
					'rows' => 10,
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Will appear in the supplier list')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone:'),
					'name' => 'phone',
					'size' => 15,
					'maxlength' => 16,
					'desc' => $this->l('Phone number for this supplier')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address:'),
					'name' => 'address',
					'size' => 100,
					'maxlength' => 128,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address:').' (2)',
					'name' => 'address2',
					'size' => 100,
					'maxlength' => 128,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postal Code/Zip Code:'),
					'name' => 'postcode',
					'size' => 10,
					'maxlength' => 12,
					'required' => true,
				),
				array(
					'type' => 'text',
					'label' => $this->l('City:'),
					'name' => 'city',
					'size' => 20,
					'maxlength' => 32,
					'required' => true,
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'id_country',
					'required' => true,
					'default_value' => (int)$this->context->country->id,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id, false),
						'id' => 'id_country',
						'name' => 'name',
					),
					'desc' => $this->l('Country where the state, region or city is located')
				),
				array(
					'type' => 'select',
					'label' => $this->l('State'),
					'name' => 'id_state',
					'options' => array(
						'id' => 'id_state',
						'query' => array(),
						'name' => 'name'
					)
				),
				array(
					'type' => 'file',
					'label' => $this->l('Logo:'),
					'name' => 'logo',
					'display_image' => true,
					'desc' => $this->l('Upload a supplier logo from your computer')
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
					'desc' => $this->l('To add "tags" click in the field, write something and then press "Enter"')
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
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		// loads current address for this supplier - if possible
		$address = null;
		if (isset($obj->id))
		{
			$id_address = Address::getAddressIdBySupplierId($obj->id);

			if ($id_address > 0)
				$address = new Address((int)$id_address);
		}

		// force specific fields values (address)
		if ($address != null)
		{
			$this->fields_value = array(
				'id_address' => $address->id,
				'phone' => $address->phone,
				'address' => $address->address1,
				'address2' => $address->address2,
				'postcode' => $address->postcode,
				'city' => $address->city,
				'id_country' => $address->id_country,
				'id_state' => $address->id_state,
			);
		}
		else
			$this->fields_value = array(
				'id_address' => 0,
				'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
			);


		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}

		// set logo image
		$image = ImageManager::thumbnail(_PS_SUPP_IMG_DIR_.'/'.$this->object->id.'.jpg', $this->table.'_'.(int)$this->object->id.'.'.$this->imageType, 350, $this->imageType, true);
		$this->fields_value['image'] = $image ? $image : false;
		$this->fields_value['size'] = $image ? filesize(_PS_SUPP_IMG_DIR_.'/'.$this->object->id.'.jpg') / 1000 : false;

		return parent::renderForm();
	}

	public function renderView()
	{
		$products = $this->object->getProductsLite($this->context->language->id);
		$total_product = count($products);
		for ($i = 0; $i < $total_product; $i++)
		{
			$products[$i] = new Product($products[$i]['id_product'], false, $this->context->language->id);
			$products[$i]->loadStockData();
			// Build attributes combinations
			$combinations = $products[$i]->getAttributeCombinations($this->context->language->id);
			foreach ($combinations as $k => $combination)
			{
				$comb_infos = Supplier::getProductInformationsBySupplier($this->object->id,
																		 $products[$i]->id,
																		 $combination['id_product_attribute']);
				$comb_array[$combination['id_product_attribute']]['product_supplier_reference'] = $comb_infos['product_supplier_reference'];
				$comb_array[$combination['id_product_attribute']]['product_supplier_price_te'] = Tools::displayPrice($comb_infos['product_supplier_price_te'], new Currency($comb_infos['id_currency']));
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
			else
			{
				$product_infos = Supplier::getProductInformationsBySupplier($this->object->id,
																			$products[$i]->id,
																			0);
				$products[$i]->product_supplier_reference = $product_infos['product_supplier_reference'];
				$products[$i]->product_supplier_price_te = Tools::displayPrice($product_infos['product_supplier_price_te'], new Currency($product_infos['id_currency']));
			}
		}

		$this->tpl_view_vars = array(
			'supplier' => $this->object,
			'products' => $products,
			'stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
			'shopContext' => Shop::getContext(),
		);

		return parent::renderView();
	}

	protected function afterImageUpload()
	{
		$return = true;
		/* Generate image with differents size */
		if (($id_supplier = (int)Tools::getValue('id_supplier')) &&
			 isset($_FILES) && count($_FILES) && file_exists(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('suppliers');
			foreach ($images_types as $k => $image_type)
			{
				$file = _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg';
				if (!ImageManager::resize($file, _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'.jpg', (int)$image_type['width'], (int)$image_type['height']))
					$return = false;
			}
		}
		return $return;
	}

	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		// checks access
		if (Tools::isSubmit('submitAdd'.$this->table) && !($this->tabAccess['add'] === '1'))
		{
			$this->errors[] = Tools::displayError('You do not have permission to add suppliers.');
			return parent::postProcess();
		}

		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if (Tools::isSubmit('id_supplier') && !($obj = $this->loadObject(true)))
				return;

			// updates/creates address if it does not exist
			if (Tools::isSubmit('id_address') && (int)Tools::getValue('id_address') > 0)
				$address = new Address((int)Tools::getValue('id_address')); // updates address
			else
				$address = new Address(); // creates address

			$address->alias = Tools::getValue('name', null);
			$address->lastname = 'supplier'; // skip problem with numeric characters in supplier name
			$address->firstname = 'supplier'; // skip problem with numeric characters in supplier name
			$address->address1 = Tools::getValue('address', null);
			$address->address2 = Tools::getValue('address2', null);
			$address->postcode = Tools::getValue('postcode', null);
			$address->phone = Tools::getValue('phone', null);
			$address->id_country = Tools::getValue('id_country', null);
			$address->id_state = Tools::getValue('id_state', null);
			$address->city = Tools::getValue('city', null);

			$validation = $address->validateController();

			// checks address validity
			if (count($validation) > 0)
			{
				foreach ($validation as $item)
					$this->errors[] = $item;
				$this->errors[] = Tools::displayError('The address is not correct. Please make sure all of the required fields are completed.');
			}
			else
			{
				if (Tools::isSubmit('id_address') && Tools::getValue('id_address') > 0)
					$address->update();
				else
				{
					$address->save();
					$_POST['id_address'] = $address->id;
				}
			}
			return parent::postProcess();
		}
		else if (Tools::isSubmit('delete'.$this->table))
		{
			if (!($obj = $this->loadObject(true)))
				return;
			else if (SupplyOrder::supplierHasPendingOrders($obj->id))
				$this->errors[] = $this->l('It is not possible to delete a supplier if there are pending supplier orders.');
			else
			{
				//delete all product_supplier linked to this supplier
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_supplier` WHERE `id_supplier`='.(int)$obj->id); 
				
				$id_address = Address::getAddressIdBySupplierId($obj->id);
				$address = new Address($id_address);
				if (Validate::isLoadedObject($address))
				{
					$address->deleted = 1;
					$address->save();
				}
				return parent::postProcess();
			}
		}
		else
			return parent::postProcess();
	}

	/**
	 * @see AdminController::afterAdd()
	 */
	protected function afterAdd($object)
	{
		$id_address = (int)$_POST['id_address'];
		$address = new Address($id_address);
		if (Validate::isLoadedObject($address))
		{
			$address->id_supplier = $object->id;
			$address->save();
		}
		return true;

	}

	/**
	 * @see AdminController::afterUpdate()
	 */
	protected function afterUpdate($object)
	{
		$id_address = (int)$_POST['id_address'];
		$address = new Address($id_address);
		if (Validate::isLoadedObject($address))
		{
			if ($address->id_supplier != $object->id)
			{
				$address->id_supplier = $object->id;
				$address->save();
			}
		}
		return true;
	}

}

