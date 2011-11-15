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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class AdminStockCoverControllerCore extends AdminController
{
	private $stock_cover_warehouses;
	private $stock_cover_periods;

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;

		$this->fieldsDisplay = array(
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
			'coverage' => array(
				'title' => $this->l('Average time left'),
				'width' => 150,
				'orderby' => false,
				'search' => false
			),
			'stock' => array(
				'title' => $this->l('Quantity'),
				'width' => 80,
				'orderby' => false,
				'search' => false
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
			$warehouse = (Tools::getValue('id_warehouse') ? (int)Tools::getValue('id_warehouse') : -1);

			$query = new DbQuery();
			$query->select('pa.id_product_attribute as id, pa.id_product, stock_view.reference, stock_view.ean13,
							stock_view.upc, stock_view.usable_quantity as stock,
							IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name');
			$query->from('product_attribute pa
						  INNER JOIN
						  (
						  	SELECT SUM(s.usable_quantity) as usable_quantity, s.id_product_attribute, s.reference, s.ean13, s.upc
						   	FROM '._DB_PREFIX_.'stock s
						   	WHERE s.id_product = '.($id_product).'
						   	GROUP BY s.id_product_attribute
						   )
						   stock_view ON (stock_view.id_product_attribute = pa.id_product_attribute)');
			$query->innerJoin('product_lang pl ON (pl.id_product = pa.id_product AND pl.id_lang = '.$lang_id.')');
			$query->leftJoin('product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)');
			$query->leftJoin('attribute atr ON (atr.id_attribute = pac.id_attribute)');
			$query->leftJoin('attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$lang_id.')');
			$query->leftJoin('attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$lang_id.')');
			$query->where('pa.id_product = '.$id_product);
			if ($warehouse != -1)
				$query->where('s.id_warehouse = '.(int)$warehouse);
			$query->groupBy('pa.id_product_attribute');

			$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
			foreach ($datas as &$data)
			{
				if ($this->getCurrentCoverageWarehouse() == -1)
					$data['coverage'] = StockManagerFactory::getManager()->getProductCoverage($data['id_product'], $data['id'], $period);
				else
					$data['coverage'] = StockManagerFactory::getManager()->getProductCoverage($data['id_product'], $data['id'], $period, $warehouse);
			}
			echo Tools::jsonEncode(array('data'=> $datas, 'fields_display' => $this->fieldsDisplay));
		}
		die;
	}

	/**
	 * AdminController::initList() override
	 * @see AdminController::initList()
	 */
	public function initList()
	{
		$this->addRowAction('details');

		$this->toolbar_btn = array();

		// disables link
		$this->list_no_link = true;

		// query
		$this->_select = 'a.id_product as id, COUNT(pa.id_product_attribute) as variations, SUM(s.usable_quantity) as stock';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = a.id_product)
						INNER JOIN `'._DB_PREFIX_.'stock` s ON (s.id_product = a.id_product)';
		if ($this->getCurrentCoverageWarehouse() != -1)
			$this->_where .= ' AND s.id_warehouse = '.$this->getCurrentCoverageWarehouse();

		$this->tpl_list_vars['stock_cover_periods'] = $this->stock_cover_periods;
		$this->tpl_list_vars['stock_cover_cur_period'] = $this->getCurrentCoveragePeriod();
		$this->tpl_list_vars['stock_cover_warehouses'] = $this->stock_cover_warehouses;
		$this->tpl_list_vars['stock_cover_cur_warehouse'] = $this->getCurrentCoverageWarehouse();
		$this->ajax_params = array('period' => $this->getCurrentCoveragePeriod(), 'warehouse' => $this->getCurrentCoverageWarehouse());

		$this->displayInformation($this->l('Considering the coverage period choosen and the quantity of products/combinations that you sold,
					  						this interface gives you an idea of when one product will run out of stock.'));

		return parent::initList();
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
				if ($this->getCurrentCoverageWarehouse() == -1) // if all warehouses
					$item['coverage'] = StockManagerFactory::getManager()->getProductCoverage(
						$item['id'],
						0,
						$this->getCurrentCoveragePeriod()
					);
				else // else selected warehouse
					$item['coverage'] = StockManagerFactory::getManager()->getProductCoverage(
						$item['id'],
						0,
						$this->getCurrentCoveragePeriod(),
						$this->getCurrentCoverageWarehouse()
					);

				// removes 'details' action on products without attributes
				$this->addRowActionSkipList('details', array($item['id']));
			}
			else
			{
				$item['stock'] = 'See details';
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
	private function getCurrentCoveragePeriod()
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
	private function getCurrentCoverageWarehouse()
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
}
