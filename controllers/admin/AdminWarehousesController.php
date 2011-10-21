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
				'width' => 40
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 300,
				'havingFilter' => true
			),
			'management_type' => array(
				'title' => $this->l('Managment type'),
				 'width' => 40
			),
			'employee' => array(
				'title' => $this->l('Manager'),
				'width' => 150,
				'havingFilter' => true
			),
			'location' => array(
				'title' => $this->l('Location'),
				'width' => 150
			),
			'contact' => array(
				'title' => $this->l('Phone Number'),
				'width' => 50
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
		$this->addRowAction('edit');

		$this->_select = 'reference, name, management_type, CONCAT(e.lastname, \' \', e.firstname) AS employee,
						  ad.phone AS contact, CONCAT(ad.city, \' - \', c.iso_code) location';

		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)
						LEFT JOIN `'._DB_PREFIX_.'address` ad ON (ad.id_address = a.id_address)
						LEFT JOIN `'._DB_PREFIX_.'country` c ON (c.id_country = ad.id_country)';

		return parent::initList();
	}

	/**
	 * AdminController::initForm() override
	 * @see AdminController::initForm()
	 */
	public function initForm()
	{
		// Get employee list for warehouse manager
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
					'p' => $this->l('Code / Reference of this warehouse'),
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
					'p' => $this->l('Country where state, region or city is located')
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
					'hint' => $this->l('Do not change this value before the end of the accounting period for this Warehouse.'),
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
					'hint' => $this->l('Do not change this value before the end of the accounting period for this Warehouse.'),
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
					'p' => $this->l('Associated shops'),
					'hint' => $this->l('By associating a shop to a warehouse, all products in this warehouse will be available
						for sale in the associated shop. Shipment of an order of this shop is also possible from this warehouse'),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Associated carriers:'),
					'name' => 'ids_carriers[]',
					'required' => true,
					'multiple' => true,
					'options' => array(
						'query' => Carrier::getCarriers($this->context->language->id, true),
						'id' => 'id_carrier',
						'name' => 'name'
					),
					'p' => $this->l('Associated carrier'),
					'hint' => $this->l('You can specifiy the carriers availables for shipping orders from this warehouse'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		//loas current warehouse
		if (!($obj = $this->loadObject(true)))
			return;

		//load current address for this warehouse if possible
		$address = null;
		if ($obj->id_address > 0)
			$address = new Address($obj->id_address);

		//load current shops associated with this warehouse
		$shops = $obj->getShops();

		//load current carriers associated with this warehouse
		$carriers = $obj->getCarriers();

		//force specific fields values
		if ($address != null)
			$this->fields_value = array(
				'phone' => $address->phone,
				'address' => $address->address1,
				'address2' => $address->address2,
				'postcode' => $address->postcode,
				'city' => $address->city,
				'id_country' => $address->id_country,
				'id_state' => $address->id_state,
			);

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
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if (!($obj = $this->loadObject(true)))
				return;

			//handle shops associations
			if (Tools::isSubmit('ids_shops'))
				$obj->setShops(Tools::getValue('ids_shops'));

			//handle carriers associations
			if (Tools::isSubmit('ids_carriers'))
				$obj->setCarriers(Tools::getValue('ids_carriers'));

			// update/create address if not exists
			if (Tools::isSubmit('id_address') && Tools::getValue('id_address') > 0)
				//update address
				$address = new Address((int)Tools::getValue('id_address'));
			else
				//create address
				$address = new Address();

			$address->alias = Tools::getValue('name', null);
			$address->lastname = Tools::getValue('name', null);
			$address->firstname = Tools::getValue('name', null);
			$address->address1 = Tools::getValue('address', null);
			$address->address2 = Tools::getValue('address2', null);
			$address->postcode = Tools::getValue('postcode', null);
			$address->phone = Tools::getValue('phone', null);
			$address->id_country = Tools::getValue('id_country', null);
			$address->id_state = Tools::getValue('id_state', null);
			$address->city = Tools::getValue('city', null);

			// check address validity
			if (!$address->validateFields(false))
				$this->_errors[] = Tools::displayError('The address is not correct. Check if all required fields are filled.');
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

}
