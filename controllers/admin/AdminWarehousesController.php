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

/**
 * @since 1.5.0
 */
class AdminWarehousesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
	 	$this->table = 'warehouse';
	 	$this->className = 'Warehouse';
		$this->deleted = true;
		$this->lang = false;
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->fields_list = array(
			'reference'	=> array(
				'title' => $this->l('Reference'),
			),
			'name' => array(
				'title' => $this->l('Name'),
			),
			'management_type' => array(
				'title' => $this->l('Managment type'),
			),
			'employee' => array(
				'title' => $this->l('Manager'),
				'filter_key' => 'employee',
				'havingFilter' => true
			),
			'location' => array(
				'title' => $this->l('Location'),
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'contact' => array(
				'title' => $this->l('Phone Number'),
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
		);

		parent::__construct();
	}

	public function initPageHeaderToolbar()
	{
		if(empty($this->display))
			$this->page_header_toolbar_btn['new_warehouse'] = array(
				'href' => self::$currentIndex.'&amp;addwarehouse&amp;token='.$this->token,
				'desc' => $this->l('Add new warehouse'),
				'icon' => 'process-icon-new'
			);
		
		parent::initPageHeaderToolbar();
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		// removes links on rows
		$this->list_no_link = true;

		// adds actions on rows
		$this->addRowAction('edit');
		$this->addRowAction('view');
		$this->addRowAction('delete');

		// query: select
		$this->_select = '
			reference,
			name,
			management_type,
			CONCAT(e.lastname, \' \', e.firstname) as employee,
			ad.phone as contact,
			CONCAT(ad.city, \' - \', c.iso_code) as location';

		// query: join
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)
			LEFT JOIN `'._DB_PREFIX_.'address` ad ON (ad.id_address = a.id_address)
			LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.id_country = ad.id_country)';

		// display help informations
		$this->displayInformation($this->l('This interface allows you to manage your warehouses.').'<br />');
		$this->displayInformation($this->l('Before adding stock in your warehouses, you should check the default currency used.').'<br />');
		$this->displayInformation($this->l('You should also check:'));
		$this->displayInformation($this->l('the management type (according to the law in your country), the valuation currency and its associated carriers and shops.').'<br />');
		$this->displayInformation($this->l('You can also see detailed information about your stock, such as its overall value, the number of products and quantities stored, etc...')
								  .'<br /><br />');
		$this->displayInformation($this->l('Be careful! Products from different warehouses will need to be shipped in different packages.'));

		return parent::renderList();
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		// loads current warehouse
		if (!($obj = $this->loadObject(true)))
			return;

		// gets the manager of the warehouse
		$query = new DbQuery();
		$query->select('id_employee, CONCAT(lastname," ",firstname) as name');
		$query->from('employee');
		$query->where('active = 1');
		$employees_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		// sets the title of the toolbar
		if (Tools::isSubmit('add'.$this->table))
			$this->toolbar_title = $this->l('Stock: Create a warehouse');
		else
			$this->toolbar_title = $this->l('Stock: Warehouse management');

		// sets the fields of the form
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Warehouse information'),
				'icon' => 'icon-pencil'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_address',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Reference:'),
					'name' => 'reference',
					'maxlength' => 32,
					'required' => true,
					'hint' => $this->l('Reference for this warehouse'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'maxlength' => 45,
					'required' => true,
					'hint' => array(
						$this->l('Name of this warehouse'),
						$this->l('Invalid characters:').' !&lt;&gt;,;?=+()@#"�{}_$%:',
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone:'),
					'name' => 'phone',
					'maxlength' => 16,
					'hint' => $this->l('Phone number for this warehouse')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address:'),
					'name' => 'address',
					'maxlength' => 128,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Address:').' (2)',
					'name' => 'address2',
					'maxlength' => 128,
					'hint' => $this->l('Address of this warehouse (complementary address is optional).'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postcode/Zip Code:'),
					'name' => 'postcode',
					'maxlength' => 12,
					'required' => true,
				),
				array(
					'type' => 'text',
					'label' => $this->l('City:'),
					'name' => 'city',
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
					'hint' => $this->l('Warehouse location country')
				),
				array(
					'type' => 'select',
					'label' => $this->l('State:'),
					'name' => 'id_state',
					'required' => true,
					'options' => array(
						'query' => array(),
						'id' => 'id_state',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Manager:'),
					'name' => 'id_employee',
					'required' => true,
					'options' => array(
						'query' => $employees_array,
						'id' => 'id_employee',
						'name' => 'name'
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Carriers:'),
					'name' => 'ids_carriers[]',
					'required' => false,
					'multiple' => true,
					'options' => array(
						'query' => Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
						'id' => 'id_reference',
						'name' => 'name'
					),
					'hint' => array(
						$this->l('Associated carriers. Use CTRL+CLICK to select several.'),
						$this->l('You must select at least one carrier, if you do not select a carrier none will be able to ship from this warehouse.'),
						$this->l('You can specify the number of carriers available to ship orders from particular warehouses.'),
					)
				),
			),

		);

		// Shop Association
		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shops:'),
				'name' => 'checkBoxShopAsso',
				'hint' => $this->l('Associated shops'),
				'disable_shared' => Shop::SHARE_STOCK
			);
		}

		// if it is still possible to change currency valuation and management type
		if (Tools::isSubmit('addwarehouse') || Tools::isSubmit('submitAddwarehouse'))
		{
			// adds input management type
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Management type:'),
				'hint' => $this->l('Be careful! You won\'t be able to change this value later!'),
				'name' => 'management_type',
				'required' => true,
				'options' => array(
					'query' => array(
						array(
							'id' => 'WA',
							'name' => $this->l('Average Weight')
						),
						array(
							'id' => 'FIFO',
							'name' => $this->l('First In, First Out')
						),
						array(
							'id' => 'LIFO',
							'name' => $this->l('Last In, First Out')
						),
					),
					'id' => 'id',
					'name' => 'name'
				),
				'hint' => $this->l('Inventory valuation method')
			);
			
			// adds input valuation currency
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Stock valuation currency:'),
				'hint' => $this->l('Be careful! You won\'t be able to change this value later!'),
				'name' => 'id_currency',
				'required' => true,
				'options' => array(
					'query' => Currency::getCurrencies(),
					'id' => 'id_currency',
					'name' => 'name'
				)
			);
		}
		else // else hide input
		{
			$this->fields_form['input'][] = array(
				'type' => 'hidden',
				'name' => 'management_type'
			);

			$this->fields_form['input'][] = array(
				'type' => 'hidden',
				'name' => 'id_currency'
			);
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('Save'),
			'class' => 'button'
		);

		$address = null;
		// loads current address for this warehouse - if possible
		if ($obj->id_address > 0)
			$address = new Address($obj->id_address);

		// loads current shops associated with this warehouse
		$shops = $obj->getShops();
		$ids_shop = array();
		foreach ($shops as $shop)
			$ids_shop[] = $shop['id_shop'];

		// loads current carriers associated with this warehouse
		$carriers = $obj->getCarriers(true);

		// if an address is available : force specific fields values
		if ($address != null)
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
		else // loads default country
			$this->fields_value = array(
				'id_address' => 0,
				'id_country' => Configuration::get('PS_COUNTRY_DEFAULT')
			);

		// loads shops and carriers
		$this->fields_value['ids_shops[]'] = $ids_shop;
		$this->fields_value['ids_carriers[]'] = $carriers;

		if (!Validate::isLoadedObject($obj))
			$this->fields_value['id_currency'] = (int)Configuration::get('PS_CURRENCY_DEFAULT');

		return parent::renderForm();
	}

	/**
	 * @see AdminController::renderView()
	 */
	public function renderView()
	{
		// gets necessary objects
		$id_warehouse = (int)Tools::getValue('id_warehouse');
		$warehouse = new Warehouse($id_warehouse);
		$employee = new Employee($warehouse->id_employee);
		$currency = new Currency($warehouse->id_currency);
		$address = new Address($warehouse->id_address);
		$shops = $warehouse->getShops();

		$this->toolbar_title = $warehouse->name;

		// checks objects
		if (!Validate::isLoadedObject($warehouse) ||
			!Validate::isLoadedObject($employee) ||
			!Validate::isLoadedObject($currency) ||
			!Validate::isLoadedObject($address))
			return parent::renderView();

		// assigns to our view
		$this->tpl_view_vars = array(
			'warehouse' => $warehouse,
			'employee' => $employee,
			'currency' => $currency,
			'address' => $address,
			'shops' => $shops,
			'warehouse_num_products' => $warehouse->getNumberOfProducts(),
			'warehouse_value' => Tools::displayPrice(Tools::ps_round($warehouse->getStockValue(), 2), $currency),
			'warehouse_quantities' => $warehouse->getQuantitiesofProducts(),
		);

		return parent::renderView();
	}

	/**
	 * @see AdminController::afterAdd()
	 * Called once $object is set.
	 * Used to process the associations with address/shops/carriers
	 */
	protected function afterAdd($object)
	{
		// handles address association
		$address = new Address($object->id_address);
		if (Validate::isLoadedObject($address))
		{
			$address->id_warehouse = (int)$object->id;
			$address->save();
		}

		// handles carriers associations
		if (Tools::isSubmit('ids_carriers'))
			$object->setCarriers(Tools::getValue('ids_carriers'));

		return true;
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		// foreach item in the list to render
		$nb_items = count($this->_list);
		for ($i = 0; $i < $nb_items; ++$i)
		{
			// depending on the management type, translates the management type
			$item = &$this->_list[$i];
			switch ($item['management_type']) // management type can be either WA/FIFO/LIFO
			{
				case 'WA':
					$item['management_type'] = $this->l('WA');
				break;

				case 'FIFO':
					$item['management_type'] = $this->l('FIFO');
				break;

				case 'LIFO':
					$item['management_type'] = $this->l('LIFO');
				break;
			}
		}
	}
	
	public function initContent()
	{
		if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
		{
			$this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] = $this->l('You need to activate advanced stock management before using this feature.');
			return false;
		}
		parent::initContent();
	}
	
	public function initProcess()
	{
		if (!Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
		{
			$this->warnings[md5('PS_ADVANCED_STOCK_MANAGEMENT')] = $this->l('You need to activate advanced stock management before using this feature.');
			return false;
		}
		parent::initProcess();	
	}

	/**
	 * @see AdminController::processAdd();
	 */
	public function processAdd()
	{
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if (!($obj = $this->loadObject(true)))
					return;

			$this->updateAddress();
			
			// hack for enable the possibility to update a warehouse without recreate new id
			$this->deleted = false;

			return parent::processAdd();
		}
	}
	
	protected function updateAddress()
	{
		// updates/creates address if it does not exist
		if (Tools::isSubmit('id_address') && (int)Tools::getValue('id_address') > 0)
			$address = new Address((int)Tools::getValue('id_address')); // updates address
		else
			$address = new Address(); // creates address
			// sets the address
		$address->alias = Tools::getValue('reference', null);
		$address->lastname = 'warehouse'; // skip problem with numeric characters in warehouse name
		$address->firstname = 'warehouse'; // skip problem with numeric characters in warehouse name
		$address->address1 = Tools::getValue('address', null);
		$address->address2 = Tools::getValue('address2', null);
		$address->postcode = Tools::getValue('postcode', null);
		$address->phone = Tools::getValue('phone', null);
		$address->id_country = Tools::getValue('id_country', null);
		$address->id_state = Tools::getValue('id_state', null);
		$address->city = Tools::getValue('city', null);

		// validates the address
		$validation = $address->validateController();

		// checks address validity
		if (count($validation) > 0) // if not valid
		{
			foreach ($validation as $item)
				$this->errors[] = $item;
			$this->errors[] = Tools::displayError('The address is not correct. Please make sure all of the required fields are completed.');
		}
		else // valid
		{
			if (Tools::isSubmit('id_address') && Tools::getValue('id_address') > 0)
				$address->update();
			else
			{
				$address->save();
				$_POST['id_address'] = $address->id;
			}
		}
	}

	/**
	 * @see AdminController::processDelete();
	 */
	public function processDelete()
	{
		if (Tools::isSubmit('delete'.$this->table))
		{
			// check if the warehouse exists and can be deleted
			if (!($obj = $this->loadObject(true)))
				return;
			else if ($obj->getQuantitiesOfProducts() > 0) // not possible : products
				$this->errors[] = $this->l('It is not possible to delete a warehouse when there are products in it.');
			else if (SupplyOrder::warehouseHasPendingOrders($obj->id)) // not possible : supply orders
				$this->errors[] = $this->l('It is not possible to delete a Warehouse if it has pending supply orders.');
			else // else, it can be deleted
			{
				// sets the address of the warehouse as deleted
				$address = new Address($obj->id_address);
				$address->deleted = 1;
				$address->save();

				// removes associations with carriers/shops/products location
				$obj->setCarriers(array());
				$obj->resetProductsLocations();

				return parent::processDelete();
			}
		}
	}

	/**
	 * @see AdminController::processUpdate();
	 */
	public function processUpdate()
	{
		// loads object
		if (!($obj = $this->loadObject(true)))
			return;
		$this->updateAddress();
		// handles carriers associations
		$obj->setCarriers(Tools::getValue('ids_carriers'), array());

		return parent::processUpdate();
	}

	protected function updateAssoShop($id_object)
	{
		parent::updateAssoShop($id_object);
		if (!($obj = $this->loadObject(true)))
			return;
		$obj->resetStockAvailable();
	}

}
