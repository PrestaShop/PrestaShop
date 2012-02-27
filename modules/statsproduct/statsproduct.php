<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7307 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StatsProduct extends ModuleGraph
{
	private $html = '';
	private $_query = '';
	private $_option = 0;
	private $_id_product = 0;

	public function __construct()
	{
		$this->name = 'statsproduct';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Product details');
		$this->description = $this->l('Get detailed statistics for each product.');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function getTotalBought($id_product)
	{
		$dateBetween = ModuleGraph::getDateBetween();
		$sql = 'SELECT SUM(od.`product_quantity`) AS total
				FROM `'._DB_PREFIX_.'order_detail` od
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
				WHERE od.`product_id` = '.(int)$id_product.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND o.`date_add` BETWEEN '.$dateBetween;
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getTotalSales($id_product)
	{
		$dateBetween = ModuleGraph::getDateBetween();
		$sql = 'SELECT SUM(od.`product_quantity` * od.`product_price`) AS total
				FROM `'._DB_PREFIX_.'order_detail` od
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
				WHERE od.`product_id` = '.(int)$id_product.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND o.`date_add` BETWEEN '.$dateBetween;
		return (float)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getTotalViewed($id_product)
	{
		$dateBetween = ModuleGraph::getDateBetween();
		$sql = 'SELECT SUM(pv.`counter`) AS total
				FROM `'._DB_PREFIX_.'page_viewed` pv
				LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
				LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
				LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
				WHERE pt.`name` = \'product\'
					'.Shop::addSqlRestriction(false, 'pv').'
					AND p.`id_object` = '.(int)$id_product.'
					AND dr.`time_start` BETWEEN '.$dateBetween.'
					AND dr.`time_end` BETWEEN '.$dateBetween;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return isset($result['total']) ? $result['total'] : 0;
	}

	private function getProducts($id_lang)
	{
		$sql = 'SELECT p.`id_product`, p.reference, pl.`name`, IFNULL(stock.quantity, 0) as quantity
				FROM `'._DB_PREFIX_.'product` p
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON p.`id_product` = pl.`id_product`'.Shop::addSqlRestrictionOnLang('pl').'
				'.Shop::addSqlAssociation('product', 'p').'
				'.(Tools::getValue('id_category') ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`' : '').'
				WHERE pl.`id_lang` = '.(int)$id_lang.'
					'.(Tools::getValue('id_category') ? 'AND cp.id_category = '.(int)Tools::getValue('id_category') : '').'
				ORDER BY pl.`name`';

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	private function getSales($id_product, $id_lang)
	{
		$sql = 'SELECT o.date_add, o.id_order, o.id_customer, od.product_quantity, (od.product_price * od.product_quantity) as total, od.tax_name, od.product_name
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				WHERE o.date_add BETWEEN '.$this->getDate().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND od.product_id = '.(int)$id_product;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	private function getCrossSales($id_product, $id_lang)
	{
		$sql = 'SELECT pl.name as pname, pl.id_product, SUM(od.product_quantity) as pqty, AVG(od.product_price) as pprice
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = od.product_id AND pl.id_lang = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				WHERE o.id_customer IN (
						SELECT o.id_customer
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
						WHERE o.date_add BETWEEN '.$this->getDate().'
						AND o.valid = 1
						AND od.product_id = '.(int)$id_product.'
					)
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.date_add BETWEEN '.$this->getDate().'
					AND o.valid = 1
					AND od.product_id != '.(int)$id_product.'
				GROUP BY od.product_id
				ORDER BY pqty DESC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	public function hookAdminStatsModules($params)
	{
		$id_category = (int)Tools::getValue('id_category');
		$currency = Context::getContext()->currency;

		if (Tools::getValue('export'))
			if (!Tools::getValue('exportType'))
				$this->csvExport(array('layers' => 2, 'type' => 'line', 'option' => '42'));

		$this->html = '<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>';
		if ($id_product = (int)Tools::getValue('id_product'))
		{
			if (Tools::getValue('export'))
				if (Tools::getValue('exportType') == 1)
					$this->csvExport(array('layers' => 2, 'type' => 'line', 'option' => '1-'.$id_product));
				else if (Tools::getValue('exportType') == 2)
					$this->csvExport(array('type' => 'pie', 'option' => '3-'.$id_product));
			$product = new Product($id_product, false, $this->context->language->id);
			$totalBought = $this->getTotalBought($product->id);
			$totalSales = $this->getTotalSales($product->id);
			$totalViewed = $this->getTotalViewed($product->id);
			$this->html .= '<h3>'.$product->name.' - '.$this->l('Details').'</h3>
			<p>'.$this->l('Total bought:').' '.$totalBought.'</p>
			<p>'.$this->l('Sales (-Tx):').' '.Tools::displayprice($totalSales, $currency).'</p>
			<p>'.$this->l('Total viewed:').' '.$totalViewed.'</p>
			<p>'.$this->l('Conversion rate:').' '.number_format($totalViewed ? $totalBought / $totalViewed : 0, 2).'</p>
			<center>'.$this->engine(array('layers' => 2, 'type' => 'line', 'option' => '1-'.$id_product)).'</center>
			<br />
			<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=1"><span>'.$this->l('CSV Export').'</span></a></p>';
			if ($hasAttribute = $product->hasAttributes() && $totalBought)
				$this->html .= '<h3 class="space">'.$this->l('Attribute sales distribution').'</h3><center>'.$this->engine(array('type' => 'pie', 'option' => '3-'.$id_product)).'</center><br />
			<p><a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=2"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br />';
			if ($totalBought)
			{
				$sales = $this->getSales($id_product, $this->context->language->id);
				$this->html .= '
				<h3>'.$this->l('Sales').'</h3>
				<div style="overflow-y: scroll; height: '.min(400, (count($sales) + 1) * 32).'px;">
				<table class="table" border="0" cellspacing="0" cellspacing="0">
				<thead>
					<tr>
						<th>'.$this->l('Date').'</th>
						<th>'.$this->l('Order').'</th>
						<th>'.$this->l('Customer').'</th>
						'.($hasAttribute ? '<th>'.$this->l('Attribute').'</th>' : '').'
						<th>'.$this->l('Qty').'</th>
						<th>'.$this->l('Price').'</th>
					</tr>
				</thead><tbody>';
				$tokenOrder = Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$this->context->employee->id);
				$tokenCustomer = Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id);
				foreach ($sales as $sale)
					$this->html .= '
					<tr>
						<td>'.Tools::displayDate($sale['date_add'], (int)$this->context->language->id, false).'</td>
						<td align="center"><a href="?tab=AdminOrders&id_order='.$sale['id_order'].'&vieworder&token='.$tokenOrder.'">'.(int)($sale['id_order']).'</a></td>
						<td align="center"><a href="?tab=AdminCustomers&id_customer='.$sale['id_customer'].'&viewcustomer&token='.$tokenCustomer.'">'.(int)($sale['id_customer']).'</a></td>
						'.($hasAttribute ? '<td>'.$sale['product_name'].'</td>' : '').'
						<td>'.(int)$sale['product_quantity'].'</td>
						<td>'.Tools::displayprice($sale['total'], $currency).'</td>
					</tr>';
				$this->html .= '</tbody></table></div>';

				$crossSelling = $this->getCrossSales($id_product, $this->context->language->id);
				if (count($crossSelling))
				{
					$this->html .= '<br class="clear" />
					<h3>'.$this->l('Cross Selling').'</h3>
					<div style="overflow-y: scroll; height: 200px;">
					<table class="table" border="0" cellspacing="0" cellspacing="0">
					<thead>
						<tr>
							<th>'.$this->l('Product name').'</th>
							<th>'.$this->l('Quantity sold').'</th>
							<th>'.$this->l('Average price').'</th>
						</tr>
					</thead><tbody>';
					$tokenProducts = Tools::getAdminToken('AdminProducts'.(int)Tab::getIdFromClassName('AdminProducts').(int)$this->context->employee->id);
					foreach ($crossSelling as $selling)
						$this->html .= '
						<tr>
							<td ><a href="?tab=AdminProducts&id_product='.(int)$selling['id_product'].'&addproduct&token='.$tokenProducts.'">'.$selling['pname'].'</a></td>
							<td align="center">'.(int)$selling['pqty'].'</td>
							<td align="right">'.Tools::displayprice($selling['pprice'], $currency).'</td>
						</tr>';
					$this->html .= '</tbody></table></div>';
				}
			}
		}
		else
		{
			$categories = Category::getCategories((int)$this->context->language->id, true, false);
			$this->html .= '
			<div class="margin-form">
				<form action="" method="post" id="categoriesForm">
				<label class="t">'.$this->l('Choose a category').'</label>
					<select name="id_category" onchange="$(\'#categoriesForm\').submit();">
						<option value="0">'.$this->l('All').'</option>';
			foreach ($categories as $category)
				$this->html .= '<option value="'.$category['id_category'].'"'.($id_category == $category['id_category'] ? ' selected="selected"' : '').'>'.$category['name'].'</option>';
			$this->html .= '
					</select>
				</form>
			</div>
						<p>'.$this->l('Click on a product to access its statistics.').'</p>
				
			<h2>'.$this->l('Products available').'</h2>
			<div>
			<table class="table" border="0" cellspacing="0" cellspacing="0">
			<thead>
				<tr>
					<th>'.$this->l('Ref.').'</th>
					<th>'.$this->l('Name').'</th>
					<th>'.$this->l('Available quantity for sale').'</th>
				</tr>
			</thead><tbody>';

			foreach ($this->getProducts($this->context->language->id) as $product)
				$this->html .= '
				<tr>
					<td>'.$product['reference'].'</td>
					<td>
						<a href="'.AdminController::$currentIndex.'&token='.Tools::safeOutput(Tools::getValue('token')).'&module='.$this->name.'&id_product='.$product['id_product'].'">'.$product['name'].'</a>
					</td>
					<td>'.$product['quantity'].'</td>
				</tr>';

			$this->html .= '</tbody></table><br /></div><br />
				<a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a><br />';
		}

		$this->html .= '</div><br />
		<div class="blocStats"><h2 class="icon-guide"><span></span>'.$this->l('Guide').'</h2>
		<h2>'.$this->l('Number of purchases compared to number of viewings').'</h2>
			<p>
				'.$this->l('After choosing a category and selecting a product, informational graphs will appear. Then, you will be able to analyze them.').'
				<ul>
					<li class="bullet">'.$this->l('If you notice that a product is successful and often purchased, but viewed infrequently, you should put it more prominently on your webshop front-office.').'</li>
					<li class="bullet">'.$this->l('On the other hand, if a product has many viewings but is not often purchased,
						we advise you to check or modify this product\'s information, description and photography again.').'
					</li>
				</ul>
			</p>
		</div>';
		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		$options = explode('-', $option);
		if (count($options) === 2)
			list($this->_option, $this->_id_product) = $options;
		else
			$this->_option = $option;
		$dateBetween = $this->getDate();
		switch ($this->_option)
		{
			case 1:
				$this->_titles['main'][0] = $this->l('Popularity');
				$this->_titles['main'][1] = $this->l('Sales');
				$this->_titles['main'][2] = $this->l('Visits (x100)');
				$this->_query[0] = 'SELECT o.`date_add`, SUM(od.`product_quantity`) AS total
						FROM `'._DB_PREFIX_.'order_detail` od
						LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
						WHERE od.`product_id` = '.(int)$this->_id_product.'
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
							AND o.valid = 1
							AND o.`date_add` BETWEEN '.$dateBetween.'
						GROUP BY o.`date_add`';

				$this->_query[1] = 'SELECT dr.`time_start` AS date_add, (SUM(pv.`counter`) / 100) AS total
						FROM `'._DB_PREFIX_.'page_viewed` pv
						LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
						LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
						LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
						WHERE pt.`name` = \'product\'
							'.Shop::addSqlRestriction(false, 'pv').'
							AND p.`id_object` = '.(int)$this->_id_product.'
							AND dr.`time_start` BETWEEN '.$dateBetween.'
							AND dr.`time_end` BETWEEN '.$dateBetween.'
						GROUP BY dr.`time_start`';
			break;

			case 3:
				$this->_query = 'SELECT product_attribute_id, SUM(od.`product_quantity`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order` = od.`id_order`
						WHERE od.`product_id` = '.(int)$this->_id_product.'
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
							AND o.valid = 1
							AND o.`date_add` BETWEEN '.$dateBetween.'
						GROUP BY od.`product_attribute_id`';
				$this->_titles['main'] = $this->l('Attributes');
			break;

			case 42:
				$this->_titles['main'][1] = $this->l('Ref.');
				$this->_titles['main'][2] = $this->l('Name');
				$this->_titles['main'][3] = $this->l('Stock');
				break;
		}
	}

	protected function getData($layers)
	{
		if ($this->_option == 42)
		{
			$products = $this->getProducts($this->context->language->id);
			foreach ($products as $product)
			{
				$this->_values[0][] = $product['reference'];
				$this->_values[1][] = $product['name'];
				$this->_values[2][] = $product['quantity'];
				$this->_legend[] = $product['id_product'];
			}
		}
		else if ($this->_option != 3)
			$this->setDateGraph($layers, true);
		else
		{
			$product = new Product($this->_id_product, false, (int)$this->getLang());

			$combArray = array();
			$assocNames = array();
			$combinations = $product->getAttributeCombinations((int)$this->getLang());
			foreach ($combinations as $k => $combination)
				$combArray[$combination['id_product_attribute']][] = array('group' => $combination['group_name'], 'attr' => $combination['attribute_name']);
			foreach ($combArray as $id_product_attribute => $product_attribute)
			{
				$list = '';
				foreach ($product_attribute as $attribute)
					$list .= trim($attribute['group']).' - '.trim($attribute['attr']).', ';
				$list = rtrim($list, ', ');
				$assocNames[$id_product_attribute] = $list;
			}

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query);
			foreach ($result as $row)
			{
				$this->_values[] = $row['total'];
				$this->_legend[] = @$assocNames[$row['product_attribute_id']];
			}
		}
	}

	protected function setAllTimeValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i]);
			foreach ($result as $row)
				$this->_values[$i][(int)(substr($row['date_add'], 0, 4))] += $row['total'];
		}
	}

	protected function setYearValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i]);
			foreach ($result as $row)
				$this->_values[$i][(int)(substr($row['date_add'], 5, 2))] += $row['total'];
		}
	}

	protected function setMonthValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i]);
			foreach ($result as $row)
				$this->_values[$i][(int)(substr($row['date_add'], 8, 2))] += $row['total'];
		}
	}

	protected function setDayValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i]);
			foreach ($result as $row)
				$this->_values[$i][(int)(substr($row['date_add'], 11, 2))] += $row['total'];
		}
	}
}
