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

class AdminStockInstantStateControllerCore  extends AdminController
{
	private $_default_order_by = 'id_product';
	
	public function __construct()
	{
		$this->table = 'stock';
		$this->className = 'Stock';
		$this->lang = false;
		$this->requiredDatabase = true;
		
		$this->deleted = 0;

		$this->context = Context::getContext();

		$this->fieldsDisplay = array(
			'id_product' => array('title' => $this->l('Id product'), 'align' => 'center', 'width' => 25, 'filter_key' => 's!id_product'),
			'ean13' => array('title' => $this->l('EAN13'), 'align' => 'center', 'width' => 25),
			'reference' => array('title' => $this->l('Reference'), 'align' => 'center', 'width' => 25, 'filter_key' => 'p!reference'),
			'designation' => array('title' => $this->l('Product name'), 'width' => 130, 'filter_key' => 'designation', 'havingFilter' => true),
			'physical_quantity' => array('title' => $this->l('Physical quantity'), 'align' => 'center', 'havingFilter' => true),
			'price_te' => array('title' => $this->l('Price'), 'align' => 'center', 'width' => 25, 'havingFilter' => true),
			'usable_quantity' => array('title' => $this->l('Usable quantity'), 'align' => 'center', 'havingFilter' => true),
			'real_quantity' => array('title' => $this->l('Real quantity'), 'align' => 'center', 'filter' => false, 'search' => false, 'orderby' => false)
		);

		$this->display = 'list';

		$enabled = '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />';
		$disabled = '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />';
		
		$this->addRowAction('details');
		
		parent::__construct();
	}
	
	public function ajaxProcess()
	{
		$query = 'SELECT physical_quantity, usable_quantity, s.price_te
		FROM stock s
		INNER JOIN '._DB_PREFIX_.'product p
			ON (p.id_product = s.id_product)
		INNER JOIN '._DB_PREFIX_.'product_lang pl
			ON (pl.id_product = p.id_product AND pl.id_lang = 1)
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac
			ON (pac.id_product_attribute = s.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'attribute a
			ON (a.id_attribute = pac.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'attribute_lang al
			ON (al.id_attribute = a.id_attribute AND al.id_lang = 1)
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl
			ON (agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = 1)
		WHERE s.id_product = (SELECT id_product FROM '._DB_PREFIX_.'stock WHERE id_stock = '.(int)Tools::getValue('id').')
			AND s.id_product_attribute = (SELECT id_product_attribute FROM '._DB_PREFIX_.'stock WHERE id_stock = '.(int)Tools::getValue('id').')
			AND s.id_warehouse = (SELECT id_warehouse FROM '._DB_PREFIX_.'stock WHERE id_stock = '.(int)Tools::getValue('id').')
		GROUP BY id_stock';
		
		echo Tools::jsonEncode(array(
				'data'=> Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query),
				'fields_display' => $this->fieldsDisplay
		));
		die();
	}
	
	public function initContent()
	{
		$query = new DbQuery();
		$query->select('w.id_warehouse, name');
		$query->from('warehouse w');
		$query->innerJoin('warehouse_shop ws ON ws.id_warehouse = w.id_warehouse AND ws.id_shop = '.Context::getContext()->shop->getID(true));
		
		$this->context->smarty->assign('warehouse_list', Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query));
		$this->context->smarty->assign('current_warehouse', $this->getCurrentWarehouseId());
		
		parent::initContent();
	}
	
	protected function getCurrentWarehouseId()
	{
		static $warehouse = 0;
		if ($warehouse == 0)
		{
			$warehouse = 1;
			if ((int)Tools::getValue('warehouse'))
				$warehouse = (int)Tools::getValue('warehouse');
		}
		return $warehouse;
	}
	
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		/* Manage default params values */
		if (empty($limit))
			$limit = ((!isset($this->context->cookie->{$this->table.'_pagination'})) ? $this->_pagination[1] : $limit = $this->context->cookie->{$this->table.'_pagination'});
		$limit = (int)(Tools::getValue('pagination', $limit));
		$this->context->cookie->{$this->table.'_pagination'} = $limit;
		
		
		if (!Validate::isTableOrIdentifier($this->table))
			die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');

		if (empty($orderBy))
			$order_by = $this->context->cookie->__get($this->table.'Orderby') ? $this->context->cookie->__get($this->table.'Orderby') : $this->_default_order_by;
		if (empty($orderWay))
			$order_way = $this->context->cookie->__get($this->table.'Orderway') ? $this->context->cookie->__get($this->table.'Orderway') : 'ASC';

		
		$query = 'SELECT SQL_CALC_FOUND_ROWS
			IFNULL(CONCAT(pl.name, \' : \', GROUP_CONCAT(agl.`name`, \' - \', al.name SEPARATOR \', \')),pl.name) as designation,
			id_stock, p.id_product, s.id_product_attribute, ean13, p.reference,
			IF(((count(id_stock) - count(DISTINCT id_stock))>1), s.price_te, \'--\') as price_te,
			IF(((count(id_stock) - count(DISTINCT id_stock))>1), 0, 1) as need_details,
			CAST((SUM(physical_quantity) / (count(id_stock) - count(DISTINCT id_stock) + 1)) AS SIGNED INTEGER) AS physical_quantity,
			CAST((SUM(usable_quantity) / (count(id_stock) - count(DISTINCT id_stock) + 1)) AS SIGNED INTEGER) AS usable_quantity
		FROM '._DB_PREFIX_.'stock s
		INNER JOIN '._DB_PREFIX_.'product p
			ON (p.id_product = s.id_product)
		INNER JOIN '._DB_PREFIX_.'product_lang pl
			ON (pl.id_product = p.id_product AND pl.id_lang = 1)
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac
			ON (pac.id_product_attribute = s.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'attribute a
			ON (a.id_attribute = pac.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'attribute_lang al
			ON (al.id_attribute = a.id_attribute AND al.id_lang = 1)
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl
			ON (agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = 1)
		WHERE id_warehouse = '.$this->getCurrentWarehouseId().' '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').
		(isset($this->_filter) ? $this->_filter : '').'
		GROUP BY pac.id_product_attribute
		'.((isset($this->_filterHaving) || isset($this->_having)) ? 'HAVING ' : '').(isset($this->_filterHaving) ? ltrim($this->_filterHaving, ' AND ') : '').
		(isset($this->_having) ? $this->_having.' ' : '').'
		ORDER BY `'.pSQL($order_by).'` '.pSQL($order_way).'
		LIMIT '.(int)$start.','.(int)$limit;

		$this->_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		$manager = StockManagerFactory::getManager();
		
		foreach ($this->_list as &$row)
		{
			if(!$row['need_details'])
				$this->addRowActionSkipList('details', $row['id_stock']);
			$row['real_quantity'] = $manager->getProductRealQuantities($row['id_product'], $row['id_product_attribute'], array($this->getCurrentWarehouseId()), true);
		}
		$this->_listTotal = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');
	}
}

