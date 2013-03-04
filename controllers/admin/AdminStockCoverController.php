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
class AdminStockCoverControllerCore extends AdminController
{
	protected $stock_cover_warehouses;
	protected $stock_cover_periods;

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;
		$this->colorOnBackground = true;
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->fields_list = array(
			'reference' => array(
				'title' => $this->l('Reference'),
				'align' => 'center',
				'width' => 200,
				'filter_key' => 'a!reference'
			),
			'ean13' => array(
				'title' => $this->l('EAN13'),
				'align' => 'center',
				'width' => 100,
				'filter_key' => 'a!ean13'
			),
			'upc' => array(
				'title' => $this->l('UPC'),
				'align' => 'center',
				'width' => 100,
				'filter_key' => 'a!upc'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'filter_key' => 'b!name'
			),
			'qty_sold' => array(
				'title' => $this->l('Quantity sold'),
				'width' => 160,
				'orderby' => false,
				'search' => false,
				'hint' => $this->l('Quantity sold during the defined period.'),
			),
			'coverage' => array(
				'title' => $this->l('Coverage'),
				'width' => 160,
				'orderby' => false,
				'search' => false,
				'hint' => $this->l('Days left before your stock runs out.'),
			),
			'stock' => array(
				'title' => $this->l('Quantity'),
				'width' => 80,
				'orderby' => false,
				'search' => false,
				'hint' => $this->l('Physical (usable) quantity.')
			),
		);

		// pre-defines coverage periods
		$this->stock_cover_periods = array(
			$this->l('One week') => 7,
			$this->l('Two weeks') => 14,
			$this->l('Three weeks') => 21,
			$this->l('One month') => 31,
			$this->l('Six months') => 186,
			$this->l('One year') => 365
		);

		// gets the list of warehouses available
		$this->stock_cover_warehouses = Warehouse::getWarehouses(true);
		// gets the final list of warehouses
		array_unshift($this->stock_cover_warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));

		parent::__construct();
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
			$id_product = (int)Tools::getValue('id');
			$period = (Tools::getValue('period') ? (int)Tools::getValue('period') : 7);
			$warehouse = Tools::getValue('id_warehouse', -1);
			$where_warehouse = '';
			if ($warehouse != -1)
				$where_warehouse = ' AND s.id_warehouse = '.(int)$warehouse;

			$query = new DbQuery();
			$query->select('pa.id_product_attribute as id, pa.id_product, stock_view.reference, stock_view.ean13,
							stock_view.upc, stock_view.usable_quantity as stock');
			$query->from('product_attribute', 'pa');
			$query->join('INNER JOIN
						  (
						  	SELECT SUM(s.usable_quantity) as usable_quantity, s.id_product_attribute, s.reference, s.ean13, s.upc
						   	FROM '._DB_PREFIX_.'stock s
						   	WHERE s.id_product = '.($id_product).
							$where_warehouse.'
						   	GROUP BY s.id_product_attribute
						   )
						   stock_view ON (stock_view.id_product_attribute = pa.id_product_attribute)');
			$query->where('pa.id_product = '.$id_product);
			$query->groupBy('pa.id_product_attribute');

			$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
			foreach ($datas as &$data)
			{
				$data['name'] = Product::getProductName($data['id_product'], $data['id']);

				// computes coverage
				$coverage = StockManagerFactory::getManager()->getProductCoverage(
					$data['id_product'],
					$data['id'],
					$period,
					(($this->getCurrentCoverageWarehouse() == -1) ? null : $warehouse)
				);
				if ($coverage != -1)  // if coverage is available
				{
					if ($coverage < $this->getCurrentWarning()) // if highlight needed
						$data['color'] = '#BDE5F8';
					$data['coverage'] = $coverage;
				}
				else // infinity
					$data['coverage'] = '--';

				// computes quantity sold
				$qty_sold = $this->getQuantitySold($data['id_product'], $data['id'], $this->getCurrentCoveragePeriod());
				if (!$qty_sold)
					$data['qty_sold'] = '--';
				else
					$data['qty_sold'] = $qty_sold;
			}

			echo Tools::jsonEncode(array('data'=> $datas, 'fields_display' => $this->fields_list));
		}
		die;
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		$this->addRowAction('details');

		$this->toolbar_btn = array();

		// disables link
		$this->list_no_link = true;

		// query
		$this->_select = 'a.id_product as id, COUNT(pa.id_product_attribute) as variations, SUM(s.usable_quantity) as stock';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = a.id_product)
						'.Shop::addSqlAssociation('product_attribute', 'pa', false).'
						INNER JOIN `'._DB_PREFIX_.'stock` s ON (s.id_product = a.id_product)';
		$this->_group = 'GROUP BY a.id_product';

		self::$currentIndex .= '&coverage_period='.(int)$this->getCurrentCoveragePeriod().'&warn_days='.(int)$this->getCurrentWarning();
		if ($this->getCurrentCoverageWarehouse() != -1)
		{
			$this->_where .= ' AND s.id_warehouse = '.(int)$this->getCurrentCoverageWarehouse();
			self::$currentIndex .= '&id_warehouse='.(int)$this->getCurrentCoverageWarehouse();
		}

		// Hack for multi shop ..
		$this->_where .= ' AND b.id_shop = 1';

		$this->tpl_list_vars['stock_cover_periods'] = $this->stock_cover_periods;
		$this->tpl_list_vars['stock_cover_cur_period'] = $this->getCurrentCoveragePeriod();
		$this->tpl_list_vars['stock_cover_warehouses'] = $this->stock_cover_warehouses;
		$this->tpl_list_vars['stock_cover_cur_warehouse'] = $this->getCurrentCoverageWarehouse();
		$this->tpl_list_vars['stock_cover_warn_days'] = $this->getCurrentWarning();
		$this->ajax_params = array(
			'period' => $this->getCurrentCoveragePeriod(),
			'id_warehouse' => $this->getCurrentCoverageWarehouse(),
			'warn_days' => $this->getCurrentWarning()
		);

		$this->displayInformation($this->l('Considering the coverage period chosen and the quantity of products/combinations that you sold.'));
		$this->displayInformation($this->l('this interface gives you an idea of when a product will run out of stock.'));

		return parent::renderList();
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		$nb_items = count($this->_list);
		for ($i = 0; $i < $nb_items; ++$i)
		{
			$item = &$this->_list[$i];
			if ((int)$item['variations'] <= 0)
			{
				// computes coverage and displays (highlights if needed)
				$coverage = StockManagerFactory::getManager()->getProductCoverage(
								$item['id'],
								0,
								$this->getCurrentCoveragePeriod(),
								(($this->getCurrentCoverageWarehouse() == -1) ? null : $this->getCurrentCoverageWarehouse())
				);
				if ($coverage != -1) // coverage is available
				{
					if ($coverage < $this->getCurrentWarning())
						$item['color'] = '#BDE5F8';

					$item['coverage'] = $coverage;
				}
				else // infinity
					$item['coverage'] = '--';

				// computes quantity sold
				$qty_sold = $this->getQuantitySold($item['id'], 0, $this->getCurrentCoveragePeriod());
				if (!$qty_sold)
					$item['qty_sold'] = '--';
				else
					$item['qty_sold'] = $qty_sold;

				// removes 'details' action on products without attributes
				$this->addRowActionSkipList('details', array($item['id']));


			}
			else
			{
				$item['stock'] = $this->l('See details');
				$item['reference'] = '--';
				$item['ean13'] = '--';
				$item['upc'] = '--';
			}
		}
	}

	/**
	 * Gets the current coverage period used
	 *
	 * @return int coverage period
	 */
	protected function getCurrentCoveragePeriod()
	{
		static $coverage_period = 0;

		if ($coverage_period == 0)
		{
			$coverage_period = 7; // Week by default
			if ((int)Tools::getValue('coverage_period'))
				$coverage_period = (int)Tools::getValue('coverage_period');
		}
		return $coverage_period;
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
	 * Gets the current warning
	 *
	 * @return int warn_days
	 */
	protected function getCurrentWarning()
	{
		static $warning = 0;

		if ($warning == 0)
		{
			$warning = 0;
			if (Tools::getValue('warn_days') && Validate::isInt(Tools::getValue('warn_days')))
				$warning = (int)Tools::getValue('warn_days');
		}
		return $warning;
	}

	/**
	 * For a given product, and a given period, returns the quantity sold
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $coverage
	 * @return int $quantity
	 */
	protected function getQuantitySold($id_product, $id_product_attribute, $coverage)
	{
		$query = new DbQuery();
		$query->select('SUM(od.product_quantity)');
		$query->from('order_detail', 'od');
		$query->leftJoin('orders', 'o', 'od.id_order = o.id_order');
		$query->leftJoin('order_history', 'oh', 'o.date_upd = oh.date_add');
		$query->leftJoin('order_state', 'os', 'os.id_order_state = oh.id_order_state');
		$query->where('od.product_id = '.(int)$id_product);
		$query->where('od.product_attribute_id = '.(int)$id_product_attribute);
		$query->where('TO_DAYS(NOW()) - TO_DAYS(oh.date_add) <= '.(int)$coverage);
		$query->where('o.valid = 1');
		$query->where('os.logable = 1 AND os.delivery = 1 AND os.shipped = 1');

		$quantity = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		return $quantity;
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
