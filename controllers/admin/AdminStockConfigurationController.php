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
class AdminStockConfigurationControllerCore extends AdminController
{
	/*
	 * By default, we use StockMvtReason as the table / className
	 */
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'stock_mvt_reason';
	 	$this->className = 'StockMvtReason';
		$this->lang = true;
		$this->multishop_context = Shop::CONTEXT_ALL;

		// defines fields
		$this->fields_list = array(
			'id_stock_mvt_reason' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 40,
				'search' => false,
			),
			'sign' => array(
				'title' => $this->l('Sign'),
				'width' => 100,
				'align' => 'center',
				'type' => 'select',
				'filter_key' => 'a!sign',
				'list' => array(
					'1' => $this->l('Increase'),
					'-1' => $this->l('Decrease'),
				),
				'icon' => array(
					-1 => 'remove_stock.png',
					1 => 'add_stock.png'
				),
				'orderby' => false
			),
			'name' => array(
				'title' => $this->l('Name'),
				'filter_key' => 'b!name',
				'width' => 250
			),
		);

		// loads labels (incremenation)
		$reasons_inc = StockMvtReason::getStockMvtReasonsWithFilter($this->context->language->id,
																	array(Configuration::get('PS_STOCK_MVT_TRANSFER_TO')), 1);
		// loads labaels (decremenation)
		$reasons_dec = StockMvtReason::getStockMvtReasonsWithFilter($this->context->language->id,
																	array(Configuration::get('PS_STOCK_MVT_TRANSFER_FROM')), -1);

		// defines options for StockMvt
		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Options'),
				'fields' =>	array(
					'PS_STOCK_MVT_INC_REASON_DEFAULT' => array(
						'title' => $this->l('Default label for increasing stock:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_inc,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_STOCK_MVT_DEC_REASON_DEFAULT' => array(
						'title' => $this->l('Default label for decreasing stock:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_dec,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_STOCK_CUSTOMER_ORDER_REASON' => array(
						'title' => $this->l('Default label for decreasing stock when a customer order is shipped:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_dec,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_STOCK_MVT_SUPPLY_ORDER' => array(
						'title' => $this->l('Default label for increasing stock when a supply order is received:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_inc,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
				),
				'submit' => array(),
			)
		);

		parent::__construct();
	}

	public function init()
	{
		// if we are managing the second list (i.e. supply order state)
		if (Tools::isSubmit('submitAddsupply_order_state') ||
			Tools::isSubmit('addsupply_order_state') ||
			Tools::isSubmit('updatesupply_order_state') ||
			Tools::isSubmit('deletesupply_order_state'))
		{
			$this->table = 'supply_order_state';
		 	$this->className = 'SupplyOrderState';
		 	$this->identifier = 'id_supply_order_state';
			$this->display = 'edit';
		}
		parent::init();
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		// if we are managing StockMvtReason
		if (Tools::isSubmit('addstock_mvt_reason') ||
			Tools::isSubmit('updatestock_mvt_reason') ||
			Tools::isSubmit('submitAddstock_mvt_reason') ||
			Tools::isSubmit('submitUpdatestock_mvt_reason'))
		{
			$this->toolbar_title = $this->l('Stock: Add stock movement label');

			$this->fields_form = array(
				'legend' => array(
					'title' => $this->l('Stock Movement label'),
					'image' => '../img/admin/edit.gif'
				),
				'input' => array(
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Name:'),
						'name' => 'name',
						'size' => 50,
						'required' => true
					),
					array(
						'type' => 'select',
						'label' => $this->l('Action:'),
						'name' => 'sign',
						'required' => true,
						'options' => array(
							'query' => array(
								array(
									'id' => '1',
									'name' => $this->l('Increase stock')
								),
								array(
									'id' => '-1',
									'name' => $this->l('Decrease stock')
								),
							),
							'id' => 'id',
							'name' => 'name'
						),
						'desc' => $this->l('Select the corresponding action: Increase or decrease stock?')
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				)
			);
		}
		// else, if we are managing Supply Order State
		else if (Tools::isSubmit('addsupply_order_state') ||
				 Tools::isSubmit('updatesupply_order_state') ||
				 Tools::isSubmit('submitAddsupply_order_state') ||
				 Tools::isSubmit('submitUpdatesupply_order_state'))
		{
			$this->fields_form = array(
					'legend' => array(
						'title' => $this->l('Supply Order Status'),
						'image' => '../img/admin/edit.gif'
					),
					'input' => array(
						array(
							'type' => 'text',
							'lang' => true,
							'label' => $this->l('Status:'),
							'name' => 'name',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'color',
							'label' => $this->l('Color:'),
							'name' => 'color',
							'size' => 20,
							'desc' => $this->l('The background of the PrestaShop Back Office will be displayed in this color (HTML colors only, please).'),
						),
						array(
							'type' => 'radio',
							'label' => $this->l('Editable:'),
							'name' => 'editable',
							'required' => true,
							'class' => 't',
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'active_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => 'active_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'desc' => $this->l('Is it is possible to edit the order? Keep in mind that an editable order can not be sent to the supplier.')
						),
						array(
							'type' => 'radio',
							'label' => $this->l('Delivery note:'),
							'name' => 'delivery_note',
							'required' => true,
							'class' => 't',
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'active_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => 'active_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'desc' => $this->l('Is it possible to generate a delivery note for the order?')
						),
						array(
							'type' => 'radio',
							'label' => $this->l('Delivery state:'),
							'name' => 'receipt_state',
							'required' => true,
							'class' => 't',
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'active_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => 'active_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'desc' => $this->l('Define if products have been either partially or completely received. This will allow you to know if ordered products have to be added to the corresponding warehouse.'),
						),
						array(
							'type' => 'radio',
							'label' => $this->l('Pending receipt:'),
							'name' => 'pending_receipt',
							'required' => true,
							'class' => 't',
							'is_bool' => true,
							'values' => array(
								array(
									'id' => 'active_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => 'active_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'desc' => $this->l('The customer is awaiting delivery.')
						),
					),
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button'
					)
				);

				if (Tools::isSubmit('addsupply_order_state'))
					$this->toolbar_title = $this->l('Stock: Add supply order status');
				else
				{
					$this->toolbar_title = $this->l('Stock: Update supply order status');

					$id_supply_order_state = Tools::getValue('id_supply_order_state', 0);

					// only some fields are editable for initial states
					if (in_array($id_supply_order_state, array(1, 2, 3, 4, 5, 6)))
					{
						$this->fields_form = array(
							'legend' => array(
								'title' => $this->l('Supply order status'),
								'image' => '../img/admin/edit.gif'
							),
							'input' => array(
								array(
									'type' => 'text',
									'lang' => true,
									'label' => $this->l('Status:'),
									'name' => 'name',
									'size' => 50,
									'required' => true
								),
								array(
									'type' => 'color',
									'label' => $this->l('Back Office color:'),
									'name' => 'color',
									'size' => 20,
									'desc' => $this->l('The background of PrestaShop\'s Back Office will be displayed in this color (HTML colors only, please).'),
								),
							),
							'submit' => array(
								'title' => $this->l('Save'),
								'class' => 'button'
							)
						);
					}

					if (!($obj = new SupplyOrderState((int)$id_supply_order_state)))
						return;

					$this->fields_value = array(
						'color' => $obj->color,
						'editable' => $obj->editable,
						'delivery_note' => $obj->delivery_note,
						'receipt_state' => $obj->receipt_state,
						'pending_receipt' => $obj->pending_receipt,
					);
					foreach ($this->getLanguages() as $language)
							$this->fields_value['name'][$language['id_lang']] = $this->getFieldValue($obj, 'name', $language['id_lang']);
				}
		}

		return parent::renderForm();
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		/**
		 * General messages displayed for all lists
		 */
		$this->displayInformation($this->l('This interface allows you to configure your supply order status and stock movement labels.').'<br />');

		// Checks access
		if (!($this->tabAccess['add'] === '1'))
			unset($this->toolbar_btn['new']);

		/**
		 * First list
		 * Stock Mvt Labels/Reasons
		 */
		$first_list = null;
		$this->list_no_link = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowActionSkipList('edit', array(1, 2, 3, 4, 5, 6, 7, 8));
		$this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5, 6, 7, 8));
		$this->_where = ' AND a.deleted = 0';

		$this->toolbar_title = $this->l('Stock: Stock movement labels');
		$first_list = parent::renderList();

		/**
		 * Second list
		 * Supply Order Status/State
		 */
		$second_list = null;
		unset($this->_select, $this->_where, $this->_join, $this->_group, $this->_filterHaving, $this->_filter, $this->list_skip_actions['delete'], $this->list_skip_actions['edit']);

		// generates the actual second list
		$second_list = $this->initSupplyOrderStatusList();

		// resets default table and className for options list management
		$this->table = 'stock_mvt_reason';
	 	$this->className = 'StockMvtReason';

		// returns the final list
		return $second_list.$first_list;
	}

	/*
	 * Help function for AdminStockConfigurationController::renderList()
	 * @see AdminStockConfigurationController::renderList()
	 */
	public function initSupplyOrderStatusList()
	{
		$this->table = 'supply_order_state';
		$this->className = 'SupplyOrderState';
		$this->identifier = 'id_supply_order_state';
		$this->_defaultOrderBy = 'id_supply_order_state';
		$this->lang = true;
		$this->list_no_link = true;
		$this->_orderBy = null;
		$this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5, 6));
		$this->toolbar_title = $this->l('Stock: Supply order status');
		$this->initToolbar();

		$this->fields_list = array(
			'name' => array(
				'title' => $this->l('Name'),
				'color' => 'color',
			),
			'editable' => array(
				'title' => $this->l('Editable?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'delivery_note' => array(
				'title' => $this->l('Is there a delivery note available?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'pending_receipt' => array(
				'title' => $this->l('Is there a pending receipt?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'receipt_state' => array(
				'title' => $this->l('Delivery state?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
			'enclosed' => array(
				'title' => $this->l('Enclosed order state?'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'width' => 170,
				'orderby' => false
			),
		);

		return parent::renderList();
	}

	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		// SupplyOrderState
		if (Tools::isSubmit('submitAddsupply_order_state') ||
			Tools::isSubmit('deletesupply_order_state') ||
			Tools::isSubmit('submitUpdatesupply_order_state'))
		{
			if (Tools::isSubmit('deletesupply_order_state'))
				$this->action = 'delete';
			else
				$this->action = 'save';
			$this->table = 'supply_order_state';
		 	$this->className = 'SupplyOrderState';
		 	$this->identifier = 'id_supply_order_state';
		 	$this->_defaultOrderBy = 'id_supply_order_state';
		}
		// StockMvtReason
		else if (Tools::isSubmit('delete'.$this->table))
			$this->deleted = true;

		return parent::postProcess();
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		//If there is a field product_name in the list, check if this field is null and display standard message
		foreach ($this->fields_list as $key => $value)
			if ($key == 'product_name')
			{
				$nb_items = count($this->_list);

				for ($i = 0; $i < $nb_items; ++$i)
				{
					$item = &$this->_list[$i];

					if (empty($item['product_name']))
						$item['product_name'] = $this->l('The name of this product is not available. It may have been deleted from the system.');
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
}
