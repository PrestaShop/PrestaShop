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
*  @version  Release: $Revision: 9565 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminStockMvtControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'stock_mvt_reason';
	 	$this->className = 'StockMvtReason';
		$this->lang = true;

		$this->fieldsDisplay = array(
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
					'1' => $this->l('Increment'),
					'-1' => $this->l('Decrement'),
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
				'width' => 500
			),
		);

		$reasons_inc = StockMvtReason::getStockMvtReasonsWithFilter($this->context->language->id,
																	array(Configuration::get('PS_STOCK_MVT_TRANSFER_TO')), 1);
		$reasons_dec = StockMvtReason::getStockMvtReasonsWithFilter($this->context->language->id,
																	array(Configuration::get('PS_STOCK_MVT_TRANSFER_FROM')), -1);

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Options'),
				'fields' =>	array(
					'PS_STOCK_MVT_INC_REASON_DEFAULT' => array(
						'title' => $this->l('Default reason when incrementing stock:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_inc,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_STOCK_MVT_DEC_REASON_DEFAULT' => array(
						'title' => $this->l('Default reason when decrementing stock:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_dec,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_STOCK_CUSTOMER_ORDER_REASON' => array(
						'title' => $this->l('Default reason when decrementing stock when a customer order is shipped:'),
						'cast' => 'intval',
						'type' => 'select',
						'list' => $reasons_dec,
						'identifier' => 'id_stock_mvt_reason',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_STOCK_MVT_SUPPLY_ORDER' => array(
						'title' => $this->l('Default reason when incrementing stock when a supply order is received:'),
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

		$this->tpl_list_vars['list_warehouses'] = array();

		parent::__construct();
	}

	/**
	 * AdminController::initForm() override
	 * @see AdminController::initForm()
	 */
	public function initForm()
	{
		$this->toolbar_title = $this->l('Stock : Add Stock movement reason');

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Stock Movement Reason'),
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
					'desc' => $this->l('Select the corresponding action : increments or decrements stock.')
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		return parent::initForm();
	}

	/**
	 * AdminController::initList() override
	 * @see AdminController::initList()
	 */
	public function initList()
	{
		$this->displayInformation($this->l('This interface allows you to display the stock movements for a selected warehouse.').'<br />');
		$this->displayInformation($this->l('Also, it allows you to add and edit your own stock movement reasons.'));

		// access
		if (!($this->tabAccess['add'] === '1'))
			unset($this->toolbar_btn['new']);

		//no link on list rows
		$this->list_no_link = true;

		/*
		 * Manage default list
		 */
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowActionSkipList('edit', array(1, 2, 3, 4, 5, 6, 7, 8));
		$this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5, 6, 7, 8));

		$this->toolbar_title = $this->l('Stock : Stock movements reasons');
		$first_list = parent::initList();

		/*
		 * Manage second list
		 */
		$warehouses = Warehouse::getWarehouses(true);
		array_unshift($warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));
		$this->tpl_list_vars['list_warehouses'] = $warehouses;
		$this->tpl_list_vars['current_warehouse'] = $this->getCurrentWarehouseId();

		// reset actions, toolbar and query vars
		$this->actions = array();
		$this->toolbar_btn = array();
		$this->toolbar_title = $this->l('Stock : Stock movements');
		unset($this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

		// override table, land, className and identifier for the current controller
		$this->deleted = false;
	 	$this->table = 'stock_mvt';
	 	$this->className = 'StockMvt';
	 	$this->identifier = 'id_stock_mvt';
	 	$this->lang = false;

	 	// test if a filter is applied for this list
		if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false)
			$this->filter = true;

		// test if a filter reset request is required for this list
		if (isset($_POST['submitReset'.$this->table]))
			$this->action = 'reset_filters';
		else
			$this->action = '';

		// redifine fields display
		$this->fieldsDisplay = array(
			'product_reference' => array(
				'title' => $this->l('Product Reference'),
				'width' => 100,
				'havingFilter' => true
			),
			'product_ean13' => array(
				'title' => $this->l('Product EAN 13'),
				'width' => 75,
				'havingFilter' => true
			),
			'product_upc' => array(
				'title' => $this->l('Product UPC'),
				'width' => 75,
				'havingFilter' => true
			),
			'product_name' => array(
				'title' => $this->l('Product Name'),
				'havingFilter' => true
			),
			'sign' => array(
				'title' => $this->l('Sign'),
				'width' => 100,
				'align' => 'center',
				'type' => 'select',
				'filter_key' => 'a!sign',
				'list' => array(
					'1' => $this->l('Increment'),
					'-1' => $this->l('Decrement'),
				),
				'icon' => array(
					-1 => 'remove_stock.png',
					1 => 'add_stock.png'
				),
			),
			'physical_quantity' => array(
				'title' => $this->l('Quantity'),
				'width' => 40,
				'filter_key' => 'a!physical_quantity'
			),
			'price_te' => array(
				'title' => $this->l('Price (TE)'),
				'width' => 70,
				'align' => 'right',
				'type' => 'price',
				'currency' => true,
				'filter_key' => 'a!price_te'
			),
			'reason' => array(
				'title' => $this->l('Reason'),
				'width' => 100,
				'havingFilter' => true
			),
			'employee' => array(
				'title' => $this->l('Employee'),
				'width' => 100,
				'havingFilter' => true
			),
			'date_add' => array(
				'title' => $this->l('Date'),
				'width' => 150,
				'align' => 'right',
				'type' => 'datetime',
				'filter_key' => 'a!date_add'
			),
		);

		// make new query
		$this->_select = '
			CONCAT(pl.name, \' \', GROUP_CONCAT(IFNULL(al.name, \'\'), \'\')) product_name,
			CONCAT(a.employee_lastname, \' \', a.employee_firstname) AS employee,
			mrl.name AS reason,
			stock.reference AS product_reference,
			stock.ean13 AS product_ean13,
			stock.upc AS product_upc,
			w.id_currency AS id_currency';

		$this->_join = 'INNER JOIN '._DB_PREFIX_.'stock stock ON a.id_stock = stock.id_stock
							LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
								stock.id_product = pl.id_product
								AND pl.id_lang = '.(int)$this->context->language->id.$this->context->shop->addSqlRestrictionOnLang('pl').'
							)
							LEFT JOIN `'._DB_PREFIX_.'stock_mvt_reason_lang` mrl ON (
								a.id_stock_mvt_reason = mrl.id_stock_mvt_reason
								AND mrl.id_lang = '.(int)$this->context->language->id.'
							)
							LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = stock.id_warehouse)
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = stock.id_product_attribute)
							LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
								al.id_attribute = pac.id_attribute
								AND al.id_lang = '.(int)$this->context->language->id.'
							)';
		$this->_group = 'GROUP BY a.id_stock_mvt';

		$id_warehouse = $this->getCurrentWarehouseId();
		if ($id_warehouse > 0)
			$this->_where = ' AND w.id_warehouse = '.$id_warehouse;

		// call postProcess() for take care about actions and filters
		$this->postProcess();

		// generate the second list
		$second_list = parent::initList();

		// reset all query vars
		unset($this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

		// reset default table and className for options list management
		$this->table = 'stock_mvt_reason';
	 	$this->className = 'StockMvtReason';

		// return the two lists
		return $second_list.$first_list;


	}

	/**
	 * Gets the current warehouse for this controller
	 *
	 * @return int warehouse_id
	 */
	protected function getCurrentWarehouseId()
	{
		static $warehouse = 0;

		if ($warehouse == 0)
		{
			$warehouse = -1;
			if ((int)Tools::getValue('id_warehouse'))
				$warehouse = (int)Tools::getValue('id_warehouse');
		}

		return $warehouse;
	}

	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		//when deleting a movement reason, enable deleted flag for parent postProcess and no remove the corresponding row from the database
		if (Tools::isSubmit('delete'.$this->table))
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
		foreach ($this->fieldsDisplay as $key => $value)
			if ($key == 'product_name')
			{
				$nb_items = count($this->_list);

				for ($i = 0; $i < $nb_items; ++$i)
				{
					$item = &$this->_list[$i];

					if (empty($item['product_name']))
						$item['product_name'] = $this->l('The name of this product is not available. Maybe it has been deleted from the system.');
				}
			}
	}
}