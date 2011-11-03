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
*  @version  Release: $Revision: 7307 $
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
	 	$this->table = 'warehouse';
	 	$this->className = 'Warehouse';
		$this->context = Context::getContext();
		$this->lang = false;
		$this->fieldsDisplay = array(
			'reference'	=> array(
				'title' => $this->l('Reference'),
				'width' => 150,
			),
			'name' => array(
				'title' => $this->l('Name'),
			),
			'management_type' => array(
				'title' => $this->l('Managment type'),
				 'width' => 80,
			),
			'employee' => array(
				'title' => $this->l('Manager'),
				'width' => 200,
			),
			'location' => array(
				'title' => $this->l('Location'),
				'width' => 200,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
			'contact' => array(
				'title' => $this->l('Phone Number'),
				'width' => 200,
				'orderby' => false,
				'filter' => false,
				'search' => false,
			),
		);

		parent::__construct();
	}

	/**
	 * AdminController::initList() override
	 * @see AdminController::initList()
	 */
	public function initList()
	{
		// Checks access
		if (!($this->tabAccess['add'] === '1'))
			unset($this->toolbar_btn['new']);

		$this->list_no_link = true;
		$this->addRowAction('edit');
		$this->addRowAction('details');

		$this->_select = 'reference, name, management_type, CONCAT(e.lastname, \' \', e.firstname) AS employee,
						  ad.phone AS contact, CONCAT(ad.city, \' - \', c.iso_code) location';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)
						LEFT JOIN `'._DB_PREFIX_.'address` ad ON (ad.id_address = a.id_address)
						LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.id_country = ad.id_country)';

		$this->displayInformation($this->l('This interface allows you to manage your warehouses.').'<br />');
		$this->displayInformation($this->l('Before adding stock in your warehouses, you should check the general default currency used.').'<br />');
		$this->displayInformation($this->l('Futhermore, for each warehouse, you have to check :
											the management type (according to the law in your country), the valuation currency,
											its associated carriers and shops.').'<br />');
		$this->displayInformation($this->l('Finally, you can see detailed informations on your stock per warehouse, such as its valuation,
											the number of products and quantities stored.'));

		return parent::initList();
	}

	/**
	 * AdminController::initForm() override
	 * @see AdminController::initForm()
	 */
	public function initForm()
	{
		// gets the manager of the warehouse
		$query = new DbQuery();
		$query->select('id_employee, CONCAT(lastname," ",firstname) as name');
		$query->from('employee');
		$query->where('active = 1');
		$employees_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Warehouse'),
				'image' => '../img/admin/tab.gif'
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
					'size' => 30,
					'maxlength' => 32,
					'required' => true,
					'p' => $this->l('Reference of this warehouse'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 40,
					'maxlength' => 45,
					'required' => true,
					'p' => $this->l('Name of this warehouse')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Phone:'),
					'name' => 'phone',
					'size' => 15,
					'maxlength' => 16,
					'p' => $this->l('Phone number of this warehouse')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Adress:'),
					'name' => 'address',
					'size' => 100,
					'maxlength' => 128,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Adress:').' (2)',
					'name' => 'address2',
					'size' => 100,
					'maxlength' => 128,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Postcode/ Zip Code:'),
					'name' => 'postcode',
					'size' => 10,
					'maxlength' => 12,
					'required' => true,
				),
				array(
					'type' => 'text',
					'label' => $this->l('City:'),
					'name' => 'city',
					'size' => 10,
					'maxlength' => 12,
					'required' => true,
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'id_country',
					'required' => true,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id, false),
						'id' => 'id_country',
						'name' => 'name'
					),
					'p' => $this->l('Country where the state, region or city is located')
				),
				array(
					'type' => 'select',
					'label' => $this->l('State'),
					'name' => 'id_state',
					'required' => true,
					'options' => array(
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
					'label' => $this->l('Management type:'),
					'name' => 'management_type',
					'required' => true,
					'options' => array(
						'query' => array(
							array(
								'id' => 'WA',
								'name' => $this->l('Weight Average')
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
					'p' => $this->l('Inventory valuation method'),
					'hint' => $this->l('Do not change this value before the end of the accounting period for this warehouse.'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Stock valuation currency:'),
					'name' => 'id_currency',
					'required' => true,
					'options' => array(
						'query' => Currency::getCurrencies(),
						'id' => 'id_currency',
						'name' => 'name'
					),
					'hint' => $this->l('Do not change this value before the end of the accounting period for this warehouse.'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Associated shops:'),
					'name' => 'ids_shops[]',
					'required' => true,
					'multiple' => true,
					'options' => array(
						'query' => Shop::getShops(),
						'id' => 'id_shop',
						'name' => 'name'
					),
					'p' => $this->l('Shops'),
					'hint' => $this->l('By associating a shop to a warehouse, all products in the warehouse will be available
						for sale in it. It is also possible to ship orders of this shop from this warehouse'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Carriers:'),
					'name' => 'ids_carriers[]',
					'required' => true,
					'multiple' => true,
					'options' => array(
						'query' => Carrier::getCarriers($this->context->language->id, true),
						'id' => 'id_carrier',
						'name' => 'name'
					),
					'p' => $this->l('Associated carriers'),
					'hint' => $this->l('You can specifiy the carriers available to ship orders from this warehouse'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		// loads current warehouse
		if (!($obj = $this->loadObject(true)))
			return;

		// loads current address for this warehouse - if possible
		$address = null;
		if ($obj->id_address > 0)
			$address = new Address($obj->id_address);

		// loads current shops associated with this warehouse
		$shops = $obj->getShops();

		// loads current carriers associated with this warehouse
		$carriers = $obj->getCarriers();

		// force specific fields values
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
		else
			$this->fields_value['id_address'] = 0;

		$this->fields_value['ids_shops[]'] = $shops;
		$this->fields_value['ids_carriers[]'] = $carriers;

		return parent::initForm();
	}

	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		// Checks access
		if (Tools::isSubmit('submitAdd'.$this->table) && !($this->tabAccess['add'] === '1'))
		{
			$this->_errors[] = Tools::displayError('You do not have the required permission to add warehouses.');
			return parent::postProcess();
		}

		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if (!($obj = $this->loadObject(true)))
				return;

			// handles shops associations
			if (Tools::isSubmit('ids_shops'))
				$obj->setShops(Tools::getValue('ids_shops'));

			// handles carriers associations
			if (Tools::isSubmit('ids_carriers'))
				$obj->setCarriers(Tools::getValue('ids_carriers'));

			// updates/creates address if it does not exist
			if (Tools::isSubmit('id_address') && (int)Tools::getValue('id_address') > 0)
				// updates address
				$address = new Address((int)Tools::getValue('id_address'));
			else
				// creates address
				$address = new Address();

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

			$validation = $address->validateController();

			// checks address validity
			if (count($validation) > 0)
			{
				foreach ($validation as $item)
					$this->_errors[] = $item;
				$this->_errors[] = Tools::displayError('The address is not correct. Check if all required fields are filled.');
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
		}

		return parent::postProcess();
	}

	public function ajaxProcess()
	{
		if (Tools::isSubmit('id'))
		{
			$this->lang = false;
			$lang_id = (int)$this->context->language->id;
			$id_warehouse = (int)Tools::getValue('id');

			$query = '
			SELECT COUNT(t.id_stock)
			FROM
				(SELECT s.id_stock
				 FROM ps_stock s
				 WHERE s.id_warehouse = 1
				 GROUP BY s.id_product, s.id_product_attribute) as t';
			$refs = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

			$query = new DbQuery();
			$query->select('SUM(s.`price_te`) as total, c.`sign` as sign, SUM(s.`physical_quantity`) as quantity');
			$query->from('stock s');
			$query->leftJoin('warehouse w ON (w.`id_warehouse` = s.`id_warehouse`)');
			$query->leftJoin('currency c ON (w.`id_currency` = c.`id_currency`)');
			$query->where('s.`id_warehouse` = '.$id_warehouse);
			$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

			$content = sprintf($this->l('This warehouse stores %s reference(s) (%d quantit/ies), worth %d %s'),
							   $refs, $res[0]['quantity'], $res[0]['total'], $res[0]['sign']);
			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}
		die;
	}
}
