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

class AdminStockInstantStateControllerCore extends AdminController
{
	private $_default_order_by = 'id_product';

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'stock';
		$this->lang = false;

		$this->fieldsDisplay = array(
			'ean13' => array(
				'title' => $this->l('EAN13'),
				'width' => 100
			),
			'reference' => array(
				'title' => $this->l('Reference'),
				'width' => 250,
				'filter_key' => 'p!reference'
			),
			'designation' => array(
				'title' => $this->l('Product name'),
				'filter_key' => 'designation',
				'havingFilter' => true
			),
			'price_te' => array(
				'title' => $this->l('Price'),
				'align' => 'center',
				'width' => 100,
				'havingFilter' => true
			),
			'physical_quantity' => array(
				'title' => $this->l('Physical quantity'),
				'align' => 'center',
				'width' => 80,
				'havingFilter' => true
			),
			'usable_quantity' => array(
				'title' => $this->l('Usable quantity'),
				'align' => 'center',
				'width' => 80,
				'havingFilter' => true
			),
			'real_quantity' => array(
				'title' => $this->l('Real quantity'),
				'align' => 'center',
				'width' => 80,
				'filter' => false,
				'search' => false,
				'orderby' => false
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
		$this->displayInformation($this->l('This interface allows you to display detailed informations on your stock, per warehouse.').'<br />');

		$this->toolbar_btn = array();

		$this->addRowAction('details');

		//no link on list rows
		$this->list_no_link = true;

		$this->tpl_list_vars['list_warehouses'] = Warehouse::getWarehouseList(true);
		$this->tpl_list_vars['current_warehouse'] = $this->getCurrentWarehouseId();

		return parent::initList();
	}

	/**
	 * method call when ajax request is made with the details row action
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcess()
	{
		// get current lang id
		$lang_id = (int)$this->context->language->id;

		$query = 'SELECT physical_quantity, usable_quantity, s.price_te
			FROM '._DB_PREFIX_.'stock s
			INNER JOIN '._DB_PREFIX_.'product p
				ON (p.id_product = s.id_product)
			INNER JOIN '._DB_PREFIX_.'product_lang pl
				ON (pl.id_product = p.id_product AND pl.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac
				ON (pac.id_product_attribute = s.id_product_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute a
				ON (a.id_attribute = pac.id_attribute)
			LEFT JOIN '._DB_PREFIX_.'attribute_lang al
				ON (al.id_attribute = a.id_attribute AND al.id_lang = '.$lang_id.')
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl
				ON (agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = '.$lang_id.')
			WHERE s.id_product = (SELECT id_product FROM '._DB_PREFIX_.'stock WHERE id_stock = '.(int)Tools::getValue('id').')
				AND s.id_product_attribute = (SELECT id_product_attribute FROM '._DB_PREFIX_.'stock WHERE id_stock = '.(int)Tools::getValue('id').')
				AND s.id_warehouse = (SELECT id_warehouse FROM '._DB_PREFIX_.'stock WHERE id_stock = '.(int)Tools::getValue('id').')
			GROUP BY id_stock';

		$data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		foreach ($data as &$row)
			$row['price_te'] = Tools::displayPrice($row['price_te'], $this->getCurrency());

		echo Tools::jsonEncode(array(
			'data'=> $data,
			'fields_display' => $this->fieldsDisplay,
		));

		die();
	}

	protected function getCurrentWarehouseId()
	{
		static $warehouse = 0;
		if ($warehouse == 0)
		{
			$warehouse = 1;
			if ((int)Tools::getValue('warehouse'))
				$warehouse = (int)Tools::getValue('warehouse');
			else if ((int)$this->context->cookie->warehouse)
				$warehouse = (int)$this->context->cookie->warehouse;
			$this->context->cookie->warehouse = $warehouse;
		}

		return $warehouse;
	}

	protected function getCurrency()
	{
		static $currency = null;
		if (is_null($currency))
		{
			$warehouse = new Warehouse($this->getCurrentWarehouseId());
			$currency = new Currency($warehouse->id_currency);
		}
		return $currency;
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		$id_lang = (int)$id_lang;

		// Manage default params values
		if (empty($limit))
		{
			if (!isset($this->context->cookie->{$this->table.'_pagination'}))
				$limit = $this->_pagination[1];
			else
				$limit = $this->context->cookie->{$this->table.'_pagination'};
		}

		$limit = (int)Tools::getValue('pagination', $limit);
		$this->context->cookie->{$this->table.'_pagination'} = $limit;

		if (!Validate::isTableOrIdentifier($this->table))
			die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');

		if (empty($order_by))
			$order_by = $this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : $this->_default_order_by;

			if (empty($order_way))
			$order_way = $this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderway') : 'ASC';

		$query = 'SELECT SQL_CALC_FOUND_ROWS
					id_stock,
					id_product,
					id_product_attribute,
					designation,
					IF((count(id_stock)=1), s.price_te, \'--\') as price_te,
					SUM(physical_quantity) physical_quantity,
					SUM(usable_quantity) usable_quantity,
					ean13,
					reference,
					IF((count(id_stock)=1), 0, 1) as need_details
				FROM (SELECT
					IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.name SEPARATOR \', \')), pl.name) as designation,
					id_stock, p.id_product, s.id_product_attribute, ean13, p.reference,
					s.price_te,
					physical_quantity,
					usable_quantity
					FROM '._DB_PREFIX_.'stock s
					INNER JOIN '._DB_PREFIX_.'product p
						ON (p.id_product = s.id_product)
					INNER JOIN '._DB_PREFIX_.'product_lang pl
						ON (pl.id_product = p.id_product AND pl.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac
						ON (pac.id_product_attribute = s.id_product_attribute)
					LEFT JOIN '._DB_PREFIX_.'attribute a
						ON (a.id_attribute = pac.id_attribute)
					LEFT JOIN '._DB_PREFIX_.'attribute_lang al
						ON (al.id_attribute = a.id_attribute AND al.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl
						ON (agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = '.$id_lang.')
					WHERE id_warehouse = '.$this->getCurrentWarehouseId().' '.(isset($this->_where) ? $this->_where.' ' : '').
					($this->deleted ? 'AND a.`deleted` = 0 ' : '').
					(isset($this->_filter) ? $this->_filter : '').'
					GROUP BY id_stock
					'.((isset($this->_filterHaving) || isset($this->_having)) ? 'HAVING ' : '').
					(isset($this->_filterHaving) ? ltrim($this->_filterHaving, ' AND ') : '').
					(isset($this->_having) ? $this->_having.' ' : '').'
				) s
				GROUP BY id_product_attribute, id_product
				ORDER BY `'.pSQL($order_by).'` '.pSQL($order_way).'
				LIMIT '.(int)$start.','.(int)$limit;

		$this->_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		$this->_listTotal = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');

		$manager = StockManagerFactory::getManager();

		foreach ($this->_list as &$row)
		{
			if (is_numeric($row['price_te']))
				$row['price_te'] = Tools::displayPrice($row['price_te'], $this->getCurrency());
			if (!$row['need_details'])
				$this->addRowActionSkipList('details', $row['id_stock']);
			$row['real_quantity'] = $manager->getProductRealQuantities(
				$row['id_product'],
				$row['id_product_attribute'],
				array($this->getCurrentWarehouseId()),
				true
			);
		}
	}
}

