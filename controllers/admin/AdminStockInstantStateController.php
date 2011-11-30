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
class AdminStockInstantStateControllerCore extends AdminController
{
	private $stock_instant_state_warehouses = array();

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'stock';
		$this->className = 'Stock';
		$this->lang = false;

		$this->fieldsDisplay = array(
			'reference' => array(
				'title' => $this->l('Reference'),
				'align' => 'center',
				'width' => 200,
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
				'title' => $this->l('Price (te)'),
				'width' => 150,
				'orderby' => true,
				'search' => false,
				'type' => 'price',
				'currency' => true,
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
				'hint' => $this->l('Pysical qty,
									in combination with the quantity you ordered (atm) from your supplier,
									minus what is ordered (atm) by clients.')
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
			SUM(a.physical_quantity) as physical_quantity,
			SUM(a.usable_quantity) as usable_quantity,
			w.id_currency';

		$this->_group = 'GROUP BY a.id_product, a.id_product_attribute';

		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'warehouse w ON (w.id_warehouse = a.id_warehouse)';

		if ($this->getCurrentCoverageWarehouse() != -1)
			$this->_where .= ' AND a.id_warehouse = '.$this->getCurrentCoverageWarehouse();

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
		$this->displayInformation($this->l('This interface allows you to display detailed informations on your stock, per warehouse.'));

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

			$item['price_te'] = '--';
			$item[$this->identifier] = $item['id_product'].'_'.$item['id_product_attribute'];
			$item['name'] = Product::getProductName($item['id_product'], $item['id_product_attribute']);

			// gets stock manager
			$manager = StockManagerFactory::getManager();

			// gets real_quantity depending on the warehouse
			$item['real_quantity'] = $manager->getProductRealQuantities($item['id_product'],
																		$item['id_product_attribute'],
																		($this->getCurrentCoverageWarehouse() == -1 ? null : array($this->getCurrentCoverageWarehouse())),
																		true);
		}
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
			$query->select('w.id_currency, s.price_te, SUM(s.physical_quantity) as physical_quantity, SUM(s.usable_quantity) as usable_quantity');
			$query->from('stock s');
			$query->leftJoin('warehouse w ON (w.id_warehouse = s.id_warehouse)');
			$query->where('s.id_product = '.(int)$id_product.' AND s.id_product_attribute = '.(int)$id_product_attribute);
			if ($id_warehouse != -1)
				$query->where('s.id_warehouse = '.(int)$id_warehouse);
			$query->groupBy('s.price_te');

			$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

			foreach ($datas as &$data)
			{
				$currency = new Currency($data['id_currency']);
				if (Validate::isLoadedObject($currency))
					$data['price_te'] = Tools::displayPrice($data['price_te'], $currency);
			}

			echo Tools::jsonEncode(array('data'=> $datas, 'fields_display' => $this->fieldsDisplay));
		}
		die;
	}

}