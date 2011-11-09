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
		$this->table = 'product';
		$this->className = 'Product';
		$this->lang = true;

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
			'name' => array(
				'title' => $this->l('Name'),
				'filter_key' => 'b!name'
			),
			'price_te' => array(
				'title' => $this->l('Price (te)'),
				'width' => 150,
				'orderby' => false,
				'search' => false
			),
			'physical_quantity' => array(
				'title' => $this->l('Physical quantity'),
				'width' => 80,
				'orderby' => false,
				'search' => false
			),
			'usable_quantity' => array(
				'title' => $this->l('Usable quantity'),
				'width' => 80,
				'orderby' => false,
				'search' => false
			),
			'real_quantity' => array(
				'title' => $this->l('Real quantity'),
				'width' => 80,
				'orderby' => false,
				'search' => false
			),
		);

		$this->stock_instant_state_warehouses = Warehouse::getWarehouseList(true);
		array_unshift($this->stock_instant_state_warehouses, array('id_warehouse' => -1, 'name' => $this->l('All Warehouses')));

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
			$warehouse = (Tools::getValue('warehouse') ? (int)Tools::getValue('warehouse') : -1);

			$query = '
			SELECT a.id_product_attribute as id, a.id_product, a.reference, a.ean13,
				   IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as name,
				   IFNULL(s.physical_quantity, 0) as physical_quantity,
				   IFNULL(s.usable_quantity, 0) as usable_quantity,
				   s.price_te
			FROM '._DB_PREFIX_.'product_attribute a
			INNER JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = a.id_product AND pl.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = a.id_product_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute atr ON (atr.id_attribute = pac.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = atr.id_attribute AND al.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = atr.id_attribute_group AND agl.id_lang = '.$lang_id.')
			INNER JOIN '._DB_PREFIX_.'stock s ON (a.id_product_attribute = s.id_product_attribute)
			WHERE a.id_product = '.$id_product.
			($warehouse != -1 ? ' AND s.id_warehouse = '.(int)$warehouse : ' ').'
			GROUP BY a.id_product_attribute';

			// gets stock manager
			$manager = StockManagerFactory::getManager();

			// queries
			$datas = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
			foreach ($datas as &$data) // retrieves real quantity for each product
			{
				$data['real_quantity'] = $manager->getProductRealQuantities($data['id_product'],
																			$data['id'],
																			($warehouse == -1 ? null : array($warehouse)), // all or selected warehouse(s)
																			true);
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
		// query
		$this->_select = '
		a.id_product as id,
		COUNT(pa.id_product_attribute) as variations,
		s.physical_quantity as physical_quantity,
		s.usable_quantity as usable_quantity,
		s.price_te as price_te';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product = a.id_product)
						INNER JOIN `'._DB_PREFIX_.'stock` s ON (s.id_product = a.id_product)';
		if ($this->getCurrentCoverageWarehouse() != -1)
			$this->_where .= ' AND s.id_warehouse = '.$this->getCurrentCoverageWarehouse();

		// toolbar btn
		$this->toolbar_btn = array();
		// disables link
		$this->list_no_link = true;
		// adds action
		$this->addRowAction('details');
		// smarty
		$this->tpl_list_vars['stock_instant_state_warehouses'] = $this->stock_instant_state_warehouses;
		$this->tpl_list_vars['stock_instant_state_cur_warehouse'] = $this->getCurrentCoverageWarehouse();
		// adds ajax params
		$this->ajax_params = array('warehouse' => $this->getCurrentCoverageWarehouse());

		// displays help information
		$this->displayInformation($this->l('This interface allows you to display detailed informations on your stock, per warehouse.'));

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
			if ((int)$item['variations'] <= 0) // if this product does not have combinations
			{
				// removes 'details' action on products without attributes
				$this->addRowActionSkipList('details', array($item['id']));

				// gets stock manager
				$manager = StockManagerFactory::getManager();

				// gets real_quantity depending on the warehouse
				$item['real_quantity'] = $manager->getProductRealQuantities($item['id'],
																			0,
																			($this->getCurrentCoverageWarehouse() == -1 ? null : array($this->getCurrentCoverageWarehouse())),
																			true);
			}
			else // else, this product does have combinations, hence we do not display informations
			{
				$item['price_te'] = '--';
				$item['physical_quantity'] = '--';
				$item['usable_quantity'] = '--';
				$item['real_quantity'] = '--';
			}
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
			if ((int)Tools::getValue('instant_state_warehouse'))
				$warehouse = (int)Tools::getValue('instant_state_warehouse');
		}
		return $warehouse;
	}
}