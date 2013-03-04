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
class AdminStockInstantStateControllerCore extends AdminController
{
	protected $stock_instant_state_warehouses = array();

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'stock';
		$this->className = 'Stock';
		$this->lang = false;
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->fields_list = array(
			'reference' => array(
				'title' => $this->l('Reference'),
				'align' => 'center',
				'width' => 200,
				'havingFilter' => true
			),
			'ean13' => array(
				'title' => $this->l('EAN13'),
				'align' => 'center',
				'width' => 100,
			),
			'upc' => array(
				'title' => $this->l('UPC'),
				'align' => 'center',
				'width' => 100,
			),
			'name' => array(
				'title' => $this->l('Name'),
				'havingFilter' => true
			),
			'price_te' => array(
				'title' => $this->l('Price (tax excl.)'),
				'width' => 150,
				'orderby' => true,
				'search' => false,
				'type' => 'price',
				'currency' => true,
			),
			'valuation' => array(
				'title' => $this->l('Valuation'),
				'width' => 150,
				'orderby' => true,
				'search' => false,
				'type' => 'price',
				'currency' => true,
				'hint' => $this->l('Total value of the physical quantity. The sum (for all prices) is not available for all warehouses, please filter by warehouse.')
			),
			'physical_quantity' => array(
				'title' => $this->l('Physical quantity'),
				'width' => 80,
				'orderby' => true,
				'search' => false
			),
			'usable_quantity' => array(
				'title' => $this->l('Usable quantity'),
				'width' => 80,
				'orderby' => true,
				'search' => false,
			),
			'real_quantity' => array(
				'title' => $this->l('Real quantity'),
				'width' => 80,
				'orderby' => true,
				'search' => false,
				'hint' => $this->l('Pysical quantity (usable) - Client orders + Supply Orders'),
			),
		);

		$this->addRowAction('details');
		$this->stock_instant_state_warehouses = Warehouse::getWarehouses(true);
		array_unshift($this->stock_instant_state_warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));

		parent::__construct();
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		// query
		$this->_select = '
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(DISTINCT agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name,
			w.id_currency';

		$this->_group = 'GROUP BY a.id_product, a.id_product_attribute';

		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'warehouse` w ON (w.id_warehouse = a.id_warehouse)';
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
			a.id_product = pl.id_product
			AND pl.id_lang = '.(int)$this->context->language->id.'
		)';
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = a.id_product_attribute)';
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute` atr ON (atr.id_attribute = pac.id_attribute)';
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
			al.id_attribute = pac.id_attribute
			AND al.id_lang = '.(int)$this->context->language->id.'
		)';
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
			agl.id_attribute_group = atr.id_attribute_group
			AND agl.id_lang = '.(int)$this->context->language->id.'
		)';

		if ($this->getCurrentCoverageWarehouse() != -1)
		{
			$this->_where .= ' AND a.id_warehouse = '.$this->getCurrentCoverageWarehouse();
			self::$currentIndex .= '&id_warehouse='.(int)$this->getCurrentCoverageWarehouse();
		}

		// toolbar btn
		$this->toolbar_btn = array();
		// disables link
		$this->list_no_link = true;

		// smarty
		$this->tpl_list_vars['stock_instant_state_warehouses'] = $this->stock_instant_state_warehouses;
		$this->tpl_list_vars['stock_instant_state_cur_warehouse'] = $this->getCurrentCoverageWarehouse();
		// adds ajax params
		$this->ajax_params = array('id_warehouse' => $this->getCurrentCoverageWarehouse());

		// displays help information
		$this->displayInformation($this->l('This interface allows you to display detailed information about your stock per warehouse.'));

		// sets toolbar
		$this->initToolbar();

		$list = parent::renderList();

		// if export requested
		if ((Tools::isSubmit('csv_quantities') || Tools::isSubmit('csv_prices')) &&
			(int)Tools::getValue('id_warehouse') != -1)
		{
			if (count($this->_list) > 0)
			{
				$this->renderCSV();
				die;
			}
			else
				$this->displayWarning($this->l('There is nothing to export as CSV.'));
		}

		return $list;
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if (Tools::isSubmit('csv') && (int)Tools::getValue('id_warehouse') != -1)
			$limit = false;

		$order_by_valuation = false;
		$order_by_real_quantity = false;
		if ($this->context->cookie->{$this->table.'Orderby'} == 'valuation')
		{
			unset($this->context->cookie->{$this->table.'Orderby'});
			$order_by_valuation = true;
		}
		else if ($this->context->cookie->{$this->table.'Orderby'} == 'real_quantity')
		{
			unset($this->context->cookie->{$this->table.'Orderby'});
			$order_by_real_quantity = true;
		}

		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		$nb_items = count($this->_list);
		for ($i = 0; $i < $nb_items; ++$i)
		{
			$item = &$this->_list[$i];

			$item['price_te'] = '--';
			$item[$this->identifier] = $item['id_product'].'_'.$item['id_product_attribute'];

			// gets stock manager
			$manager = StockManagerFactory::getManager();

			// gets quantities and valuation
			$query = new DbQuery();
			$query->select('SUM(physical_quantity) as physical_quantity');
			$query->select('SUM(usable_quantity) as usable_quantity');
			$query->select('SUM(price_te * physical_quantity) as valuation');
			$query->from('stock');
			$query->where('id_product = '.(int)$item['id_product'].' AND id_product_attribute = '.(int)$item['id_product_attribute']);

			if ($this->getCurrentCoverageWarehouse() != -1)
				$query->where('id_warehouse = '.(int)$this->getCurrentCoverageWarehouse());

			$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);

			$item['physical_quantity'] = $res['physical_quantity'];
			$item['usable_quantity'] = $res['usable_quantity'];

			// gets real_quantity depending on the warehouse
			$item['real_quantity'] = $manager->getProductRealQuantities($item['id_product'],
																		$item['id_product_attribute'],
																		($this->getCurrentCoverageWarehouse() == -1 ? null : array($this->getCurrentCoverageWarehouse())),
																		true);

			// removes the valuation if the filter corresponds to 'all warehouses'
			if ($this->getCurrentCoverageWarehouse() == -1)
				$item['valuation'] = 'N/A';
			else
				$item['valuation'] = $res['valuation'];
		}

		if ($this->getCurrentCoverageWarehouse() != -1 && $order_by_valuation)
			usort($this->_list, array($this, 'valuationCmp'));
		else if ($order_by_real_quantity)
			usort($this->_list, array($this, 'realQuantityCmp'));
	}

	/**
	 * CMP
	 *
	 * @param array $n
	 * @param array $m
	 */
	public function valuationCmp($n, $m)
	{
		if ($this->context->cookie->{$this->table.'Orderway'} == 'desc')
			return $n['valuation'] > $m['valuation'];
		else
			return $n['valuation'] < $m['valuation'];
	}

	/**
	 * CMP
	 *
	 * @param array $n
	 * @param array $m
	 */
	public function realQuantityCmp($n, $m)
	{
		if ($this->context->cookie->{$this->table.'Orderway'} == 'desc')
			return $n['real_quantity'] > $m['real_quantity'];
		else
			return $n['real_quantity'] < $m['real_quantity'];
	}

	/**
	 * Gets the current warehouse used
	 *
	 * @return int id_warehouse
	 */
	protected function getCurrentCoverageWarehouse()
	{
		static $warehouse = 0;

		if ($warehouse == 0)
		{
			$warehouse = -1; // all warehouses
			if ((int)Tools::getValue('id_warehouse'))
				$warehouse = (int)Tools::getValue('id_warehouse');
		}
		return $warehouse;
	}

	/**
	 * Method called when an ajax request is made
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcess()
	{
		if (Tools::isSubmit('id')) // if a product id is submit
		{
			$this->lang = false;
			$lang_id = (int)$this->context->language->id;
			$ids = explode('_', Tools::getValue('id'));
			if (count($ids) != 2)
				die;

			$id_product = $ids[0];
			$id_product_attribute = $ids[1];
			$id_warehouse = Tools::getValue('id_warehouse', -1);

			$query = new DbQuery();
			$query->select('w.id_currency, s.price_te, SUM(s.physical_quantity) as physical_quantity, SUM(s.usable_quantity) as usable_quantity,
							(s.price_te * SUM(s.physical_quantity)) as valuation');
			$query->from('stock', 's');
			$query->leftJoin('warehouse', 'w', 'w.id_warehouse = s.id_warehouse');
			$query->where('s.id_product = '.(int)$id_product.' AND s.id_product_attribute = '.(int)$id_product_attribute);
			if ($id_warehouse != -1)
				$query->where('s.id_warehouse = '.(int)$id_warehouse);
			$query->groupBy('s.price_te');

			$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

			foreach ($datas as &$data)
			{
				$currency = new Currency($data['id_currency']);
				if (Validate::isLoadedObject($currency))
				{
					$data['price_te'] = Tools::displayPrice($data['price_te'], $currency);
					$data['valuation'] = Tools::displayPrice($data['valuation'], $currency);
				}
			}

			echo Tools::jsonEncode(array('data'=> $datas, 'fields_display' => $this->fields_list));
		}
		die;
	}

	/**
	 * @see AdminController::initToolbar();
	 */
	public function initToolbar()
	{
		if (Tools::isSubmit('id_warehouse') && (int)Tools::getValue('id_warehouse') != -1)
		{
			$this->toolbar_btn['export-stock-state-quantities-csv'] = array(
				'short' => 'Export this list as CSV',
				'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&amp;csv_quantities&amp;id_warehouse='.(int)$this->getCurrentCoverageWarehouse(),
				'desc' => $this->l('Export Quantities (CSV)'),
			);

			$this->toolbar_btn['export-stock-state-prices-csv'] = array(
				'short' => 'Export this list as CSV',
				'href' => $this->context->link->getAdminLink('AdminStockInstantState').'&amp;csv_prices&amp;id_warehouse='.(int)$this->getCurrentCoverageWarehouse(),
				'desc' => $this->l('Export Prices (CSV)'),
			);
		}
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

	/**
	 * Exports CSV
	 */
	public function renderCSV()
	{
		if (count($this->_list) <= 0)
			return;

		// sets warehouse id and warehouse name
		$id_warehouse = (int)Tools::getValue('id_warehouse');
		$warehouse_name = Warehouse::getWarehouseNameById($id_warehouse);

		// if quantities requested
		if (Tools::isSubmit('csv_quantities'))
		{
			// filename
			$filename = $this->l('stock_instant_state_quantities').'_'.$warehouse_name.'.csv';

			// header
			header('Content-type: text/csv');
			header('Cache-Control: no-store, no-cache');
			header('Content-disposition: attachment; filename="'.$filename);

			// puts keys
			$keys = array('id_product', 'id_product_attribute', 'reference', 'ean13', 'upc', 'name', 'physical_quantity', 'usable_quantity', 'real_quantity');
			echo sprintf("%s\n", implode(';', $keys));

			// puts rows
			foreach ($this->_list as $row)
			{
				$row_csv = array($row['id_product'], $row['id_product_attribute'], $row['reference'],
								 $row['ean13'], $row['upc'], $row['name'],
								 $row['physical_quantity'], $row['usable_quantity'], $row['real_quantity']
				);

				// puts one row
				echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row_csv)));
			}
		}
		// if prices requested
		else if (Tools::isSubmit('csv_prices'))
		{
			// sets filename
			$filename = $this->l('stock_instant_state_prices').'_'.$warehouse_name.'.csv';

			// header
			header('Content-type: text/csv');
			header('Cache-Control: no-store, no-cache');
			header('Content-disposition: attachment; filename="'.$filename);

			// puts keys
			$keys = array('id_product', 'id_product_attribute', 'reference', 'ean13', 'upc', 'name', 'price_te', 'physical_quantity', 'usable_quantity');
			echo sprintf("%s\n", implode(';', $keys));

			foreach ($this->_list as $row)
			{
				$id_product = (int)$row['id_product'];
				$id_product_attribute = (int)$row['id_product_attribute'];

				// gets prices
				$query = new DbQuery();
				$query->select('s.price_te, SUM(s.physical_quantity) as physical_quantity, SUM(s.usable_quantity) as usable_quantity');
				$query->from('stock', 's');
				$query->leftJoin('warehouse', 'w', 'w.id_warehouse = s.id_warehouse');
				$query->where('s.id_product = '.$id_product.' AND s.id_product_attribute = '.$id_product_attribute);
				$query->where('s.id_warehouse = '.$id_warehouse);
				$query->groupBy('s.price_te');
				$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

				// puts data
				foreach ($datas as $data)
				{
					$row_csv = array($row['id_product'], $row['id_product_attribute'], $row['reference'],
									 $row['ean13'], $row['upc'], $row['name'],
									 $data['price_te'], $data['physical_quantity'], $data['usable_quantity']);

					// puts one row
					echo sprintf("%s\n", implode(';', array_map(array('CSVCore', 'wrap'), $row_csv)));
				}
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
