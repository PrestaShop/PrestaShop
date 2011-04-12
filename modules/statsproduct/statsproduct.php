<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class StatsProduct extends ModuleGraph
{
    private $_html = '';
	private $_query = '';
	private $_option = 0;
	private $_id_product = 0;

    function __construct()
    {
        $this->name = 'statsproduct';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';

        parent::__construct();
		
        $this->displayName = $this->l('Product details');
        $this->description = $this->l('Get detailed statistics for each product.');
    }
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}
	
	public function getTotalBought($id_product)
	{
		$dateBetween = ModuleGraph::getDateBetween();
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(od.`product_quantity`) AS total
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
		WHERE od.`product_id` = '.(int)($id_product).'
		AND o.valid = 1
		AND o.`date_add` BETWEEN '.$dateBetween.'');
	}
	
	public function getTotalSales($id_product)
	{
		$dateBetween = ModuleGraph::getDateBetween();
		return (float)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(od.`product_quantity` * od.`product_price`) AS total
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
		WHERE od.`product_id` = '.(int)($id_product).'
		AND o.valid = 1
		AND o.`date_add` BETWEEN '.$dateBetween.'');
	}
	
	public function getTotalViewed($id_product)
	{
		$dateBetween = ModuleGraph::getDateBetween();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(pv.`counter`) AS total
		FROM `'._DB_PREFIX_.'page_viewed` pv
		LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
		LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
		LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
		WHERE pt.`name` = \'product.php\'
		AND p.`id_object` = '.(int)($id_product).'
		AND dr.`time_start` BETWEEN '.$dateBetween.'
		AND dr.`time_end` BETWEEN '.$dateBetween.'');
		return isset($result['total']) ? $result['total'] : 0;
	}
	
	private function getProducts($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product`, p.reference, pl.`name`, IFNULL((SELECT SUM(pa.quantity) FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product = p.id_product), p.quantity) as quantity
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON p.`id_product` = pl.`id_product`
		'.(Tools::getValue('id_category') ? 'LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`' : '').'
		WHERE pl.`id_lang` = '.(int)($id_lang).'
		'.(Tools::getValue('id_category') ? 'AND cp.id_category = '.(int)(Tools::getValue('id_category')) : '').'
		ORDER BY pl.`name`');
	}
	
	private function getSales($id_product, $id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT o.date_add, o.id_order, o.id_customer, od.product_quantity, (od.product_price * od.product_quantity) as total, od.tax_name, od.product_name
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE o.date_add BETWEEN '.$this->getDate().' AND o.valid = 1
		AND od.product_id = '.(int)($id_product));
	}
	
	private function getCrossSales($id_product, $id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT pl.name as pname, pl.id_product, SUM(od.product_quantity) as pqty, AVG(od.product_price) as pprice
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = od.product_id AND pl.id_lang = '.(int)($id_lang).')
		WHERE o.id_customer IN (
			SELECT o.id_customer
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
			WHERE o.date_add BETWEEN '.$this->getDate().'
			AND o.valid = 1
			AND od.product_id = '.(int)($id_product).'
		)
		AND o.date_add BETWEEN '.$this->getDate().'
		AND o.valid = 1
		AND od.product_id != '.(int)($id_product).'
		GROUP BY od.product_id
		ORDER BY pqty DESC');
	}
	
	public function hookAdminStatsModules($params)
	{
		global $cookie, $currentIndex;
		$id_category = (int)(Tools::getValue('id_category'));
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		
		if (Tools::getValue('export'))
			if (!Tools::getValue('exportType'))
				$this->csvExport(array('layers' => 2, 'type' => 'line', 'option' => '42'));
		
		$this->_html = '<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>';
		if ($id_product = (int)(Tools::getValue('id_product')))
		{
			if (Tools::getValue('export'))
				if (Tools::getValue('exportType') == 1)
					$this->csvExport(array('layers' => 2, 'type' => 'line', 'option' => '1-'.$id_product));	
				elseif (Tools::getValue('exportType') == 2)
					$this->csvExport(array('type' => 'pie', 'option' => '3-'.$id_product));
			$product = new Product($id_product, false, (int)($cookie->id_lang));
			$totalBought = $this->getTotalBought($product->id);
			$totalSales = $this->getTotalSales($product->id);
			$totalViewed = $this->getTotalViewed($product->id);
			$this->_html .= '<h3>'.$product->name.' - '.$this->l('Details').'</h3>
			<p>'.$this->l('Total bought:').' '.$totalBought.'</p>
			<p>'.$this->l('Sales (-Tx):').' '.Tools::displayprice($totalSales, $currency).'</p>
			<p>'.$this->l('Total viewed:').' '.$totalViewed.'</p>
			<p>'.$this->l('Conversion rate:').' '.number_format($totalViewed ? $totalBought / $totalViewed : 0, 2).'</p>
			<center>'.ModuleGraph::engine(array('layers' => 2, 'type' => 'line', 'option' => '1-'.$id_product)).'</center>
			<br />
			<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=1"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p>';
			if ($hasAttribute = $product->hasAttributes() AND $totalBought)
				$this->_html .= '<h3 class="space">'.$this->l('Attribute sales distribution').'</h3><center>'.ModuleGraph::engine(array('type' => 'pie', 'option' => '3-'.$id_product)).'</center><br />
			<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=2"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br />';
			if ($totalBought)
			{
				$sales = $this->getSales($id_product, $cookie->id_lang);
				$this->_html .= '<br class="clear" />
				<h3>'.$this->l('Sales').'</h3>
				<div style="overflow-y: scroll; height: '.min(400, (count($sales)+1)*32).'px;">
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
				$tokenOrder = Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee));
				$tokenCustomer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee));
				foreach ($sales as $sale)
					$this->_html .= '
					<tr>
						<td>'.Tools::displayDate($sale['date_add'], (int)($cookie->id_lang), false).'</td>
						<td align="center"><a href="?tab=AdminOrders&id_order='.$sale['id_order'].'&vieworder&token='.$tokenOrder.'">'.(int)($sale['id_order']).'</a></td>
						<td align="center"><a href="?tab=AdminCustomers&id_customer='.$sale['id_customer'].'&viewcustomer&token='.$tokenCustomer.'">'.(int)($sale['id_customer']).'</a></td>
						'.($hasAttribute ? '<td>'.$sale['product_name'].'</td>' : '').'
						<td>'.(int)($sale['product_quantity']).'</td>
						<td>'.Tools::displayprice($sale['total'], $currency).'</td>
					</tr>';
				$this->_html .= '</tbody></table></div>';
				
				$crossSelling = $this->getCrossSales($id_product, $cookie->id_lang);
				if (count($crossSelling))
				{
					$this->_html .= '<br class="clear" />
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
					$tokenProducts = Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee));
					foreach ($crossSelling as $selling)
						$this->_html .= '
						<tr>
							<td ><a href="?tab=AdminCatalog&id_product='.(int)($selling['id_product']).'&addproduct&token='.$tokenProducts.'">'.$selling['pname'].'</a></td>
							<td align="center">'.(int)($selling['pqty']).'</td>
							<td align="right">'.Tools::displayprice($selling['pprice'], $currency).'</td>
						</tr>';
					$this->_html .= '</tbody></table></div>';
				}
			}
		}
		else
		{
			$categories = Category::getCategories((int)($cookie->id_lang), true, false);
			$this->_html .= '
			<label>'.$this->l('Choose a category').'</label>
			<div class="margin-form">
				<form action="" method="post" id="categoriesForm">
					<select name="id_category" onchange="$(\'#categoriesForm\').submit();">
						<option value="0">'.$this->l('All').'</option>';
			foreach ($categories as $category)
				$this->_html .= '<option value="'.$category['id_category'].'"'.($id_category == $category['id_category'] ? ' selected="selected"' : '').'>'.$category['name'].'</option>';
			$this->_html .= '
					</select>
				</form>
			</div>
			<div class="clear space"></div>
			'.$this->l('Click on a product to access its statistics.').'
			<div class="clear space"></div>
			<h2>'.$this->l('Products available').'</h2>
			<div style="overflow-y: scroll; height: 600px;">
			<table class="table" border="0" cellspacing="0" cellspacing="0">
			<thead>
				<tr>
					<th>'.$this->l('Ref.').'</th>
					<th>'.$this->l('Name').'</th>
					<th>'.$this->l('Stock').'</th>
				</tr>
			</thead><tbody>';
			foreach ($this->getProducts($cookie->id_lang) AS $product)
				$this->_html .= '<tr><td>'.$product['reference'].'</td><td><a href="'.$currentIndex.'&token='.Tools::getValue('token').'&module='.$this->name.'&id_product='.$product['id_product'].'">'.$product['name'].'</a></td><td>'.$product['quantity'].'</td></tr>';
			$this->_html .= '</tbody></table><br /></div><br />
				<a href="'.$_SERVER['REQUEST_URI'].'&export=1"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a><br />';
		}
		
		$this->_html .= '</fieldset><br />
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
		<h2>'.$this->l('Number of purchases compared to number of viewings').'</h2>
			<p>
				'.$this->l('After choosing a category and selecting a product, informational graphs will appear. Then, you will be able to analyze them.').'
				<ul>
					<li class="bullet">'.$this->l('If you notice that a product is successful and often purchased, but viewed infrequently, you should put it more prominently on your webshop front-office.').'</li>
					<li class="bullet">'.$this->l('On the other hand, if a product has many viewings but is not often purchased, we advise you to check or modify this product\'s information, description and photography again.').'
					</li>
				</ul>
			</p>
		</fieldset>';
		return $this->_html;
	}
	
	public function setOption($option, $layers = 1)
	{
		list($this->_option, $this->_id_product) = explode('-', $option);
		$dateBetween = $this->getDate();
		switch ($this->_option)
		{
			case 1:
				$this->_titles['main'][0] = $this->l('Popularity');
				$this->_titles['main'][1] = $this->l('Sales');
				$this->_titles['main'][2] = $this->l('Visits (x100)');
				$this->_query[0] = '
					SELECT o.`date_add`, SUM(od.`product_quantity`) AS total
					FROM `'._DB_PREFIX_.'order_detail` od
					LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
					WHERE od.`product_id` = '.(int)($this->_id_product).'
					AND o.valid = 1
					AND o.`date_add` BETWEEN '.$dateBetween.'
					GROUP BY o.`date_add`';
				$this->_query[1] = '
					SELECT dr.`time_start` AS date_add, (SUM(pv.`counter`) / 100) AS total
					FROM `'._DB_PREFIX_.'page_viewed` pv
					LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
					LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
					LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
					WHERE pt.`name` = \'product.php\'
					AND p.`id_object` = '.(int)($this->_id_product).'
					AND dr.`time_start` BETWEEN '.$dateBetween.'
					AND dr.`time_end` BETWEEN '.$dateBetween.'
					GROUP BY dr.`time_start`';
				break;
			case 3:
				$this->_query = '
					SELECT product_attribute_id, SUM(od.`product_quantity`) AS total
					FROM `'._DB_PREFIX_.'orders` o
					LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order` = od.`id_order`
					WHERE od.`product_id` = '.(int)($this->_id_product).'
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
		global $cookie;
		if ($this->_option == 42)
		{
			$products = $this->getProducts(intval($cookie->id_lang));
			foreach ($products AS $product)
			{
				$this->_values[0][] =  $product['reference'];
				$this->_values[1][] =  $product['name'];
				$this->_values[2][] =  $product['quantity'];
				$this->_legend[] = $product['id_product'];
			}
		}
		elseif ($this->_option != 3)
			$this->setDateGraph($layers, true);
		else
		{
			$product = new Product($this->_id_product, false, (int)($this->getLang()));
			
			$combArray = array();
			$assocNames = array();
			$combinaisons = $product->getAttributeCombinaisons((int)($this->getLang()));
			foreach ($combinaisons AS $k => $combinaison)
				$combArray[$combinaison['id_product_attribute']][] = array('group' => $combinaison['group_name'], 'attr' => $combinaison['attribute_name']);
			foreach ($combArray AS $id_product_attribute => $product_attribute)
			{
				$list = '';
				foreach ($product_attribute AS $attribute)
					$list .= trim($attribute['group']).' - '.trim($attribute['attr']).', ';
				$list = rtrim($list, ', ');
				$assocNames[$id_product_attribute] = $list;
			}
		
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query);
			foreach ($result as $row)
			{
			    $this->_values[] = $row['total'];
			    $this->_legend[] = @$assocNames[$row['product_attribute_id']];
			}
		}
	}
	
	protected function setYearValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query[$i]);
			foreach ($result AS $row)
			    $this->_values[$i][(int)(substr($row['date_add'], 5, 2))] += $row['total'];
		}
	}
	
	protected function setMonthValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query[$i]);
			foreach ($result AS $row)
			    $this->_values[$i][(int)(substr($row['date_add'], 8, 2))] += $row['total'];
		}
	}

	protected function setDayValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query[$i]);
			foreach ($result AS $row)
			    $this->_values[$i][(int)(substr($row['date_add'], 11, 2))] += $row['total'];
		}
	}
}



