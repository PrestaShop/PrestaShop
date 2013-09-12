<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Dashproducts extends Module
{
	public function __construct()
	{
		$this->name = 'dashproducts';
		$this->displayName = 'Dashboard Products';
		$this->tab = '';
		$this->version = '0.1';
		$this->author = 'PrestaShop';
		
		parent::__construct();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('dashboardZoneTwo') || !$this->registerHook('dashboardData'))
			return false;
		return true;
	}

	public function hookDashboardZoneTwo($params)
	{
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}
	
	public function hookDashboardData($params)
	{
		$table_recent_orders = $this->getTableRecentOrders();
		$table_best_sellers = $this->getTableBestSellers($params['date_from'], $params['date_to']);
		$table_most_viewed = $this->getTableMostViewed($params['date_from'], $params['date_to']);
		$table_top_10_most_search = $this->getTableTop10MostSearch($params['date_from'], $params['date_to']);
		$table_top_5_search = $this->getTableTop5Search();
		$table_best_sales = $this->getTableBestSales();
		
		return array(
			'data_table' => array(
				'table_recent_orders' => $table_recent_orders,
				'table_best_sellers' => $table_best_sellers,
				'table_most_viewed' => $table_most_viewed,
				'table_top_10_most_search' => $table_top_10_most_search,
				'table_top_5_search' => $table_top_5_search,
				'table_best_sales' => $table_best_sales,
			)
		);
	}
	
	public function getTableRecentOrders()
	{
		$header = array(
			array('title' => $this->l('Customer Name'), 'class' => 'text-left'),
			array('title' => $this->l('Products'), 'class' => 'text-center'),
			array('title' => $this->l('Total'), 'class' => 'text-center'),
			array('title' => $this->l('Date'), 'class' => 'text-center'),
			array('title' => $this->l('Action'), 'class' => 'text-center'),
		);
		
		$orders = Order::getOrdersWithInformations(10);
				
		$body = array();
		foreach ($orders as $order)
		{
			$currency = Currency::getCurrency((int)$order['id_currency']);
			$tr = array();
			$tr[] = array(
				'id' => 'firstname_lastname',
				'value' => Tools::htmlentitiesUTF8($order['firstname']).' '.Tools::htmlentitiesUTF8($order['lastname']),
				'class' => 'text-left',
				);
			$tr[] = array(
				'id' => 'state_name',
				'value' => count(OrderDetail::getList((int)$order['id_order'])),
				'class' => 'text-center',
				);
			$tr[] = array(
				'id' => 'total_paid',
				'value' => Tools::displayPrice((float)$order['total_paid'], $currency),
				'class' => 'text-center',
				'wrapper_start' => '<span class="label label-success">',
				'wrapper_end' => '<span>',
				);
			$tr[] = array(
				'id' => 'date_add',
				'value' => Tools::displayDate($order['date_add']),
				'class' => 'text-center',
				);
			$tr[] = array(
				'id' => 'details',
				'value' => $this->l('Details'),
				'class' => 'text-center',
				'wrapper_start' => '<a class="btn btn-default" href="index.php?tab=AdminOrders&id_order='.(int)$order['id_order'].'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders').'" title="'.$this->l('Details').'"><i class="icon-search"></i>',
				'wrapper_end' => '</a>'
				);

			$body[] = $tr;
		}
		return array('header' => $header, 'body' => $body);
	}
	
	public function getTableBestSellers($date_from, $date_to)
	{
		
		$header = array(
			array(
				'id' => 'image',
				'title' => $this->l('Image'),
				'class' => 'text-center',
			),
			array(
				'id' => 'product',
				'title' => $this->l('Product'),
				'class' => 'text-center',
			),
			array(
				'id' => 'category',
				'title' => $this->l('Category'),
				'class' => 'text-center',
			),
			array(
				'id' => 'total_sold',
				'title' => $this->l('Total sold'),
				'class' => 'text-center',
			),
			array(
				'id' => 'sales',
				'title' => $this->l('Sales'),
				'class' => 'text-center',
			),
			array(
				'id' => 'net_profit',
				'title' => $this->l('Net Profit'),
				'class' => 'text-center',
			)
		);
		
		$products = ProductSale::getBestSalesLight($this->context->language->id);
		
		$body = array();
		if (is_array($products) && count($products))
			foreach ($products as $product)
			{
				$product_obj = new Product((int)$product['id_product']);
				if (!Validate::isLoadedObject($product_obj))
					continue;
				
				$tr = array();
				$tr[] = array(
					'id' => 'product',
					'value' => '<img src="..'._PS_TMP_IMG_.'product_mini_'.$product['id_product'].'.jpg'.'" />',
					'class' => 'text-center',
					);
				$tr[] = array(
					'id' => 'product',
					'value' => Tools::htmlentitiesUTF8($product['name']).'<br/>'.Tools::displayPrice(Product::getPriceStatic((int)$product['id_product'])),
					'class' => 'text-center',
					);
				$category = new Category($product_obj->getDefaultCategory(), $this->context->language->id);
				$tr[] = array(
					'id' => 'category',
					'value' => $category->name,
					'class' => 'text-center',
					);
				$tr[] = array(
					'id' => 'total_sold',
					'value' => $product['sales'],
					'class' => 'text-center',
					);
				$tr[] = array(
					'id' => 'sales',
					'value' => Tools::displayPrice($this->getTotalProductSales($date_from, $date_to, (int)$product['id_product'])),
					'class' => 'text-center',
					);
				$tr[] = array(
					'id' => 'net_profit',
					'value' => 'coming soon',
					'class' => 'text-center',
					);
				$body[] = $tr;
			}		
		return array('header' => $header, 'body' => $body);
	}
	
	public function getTableMostViewed($date_from, $date_to)
	{
		$header = array(
			array(
				'id' => 'image',
				'title' => $this->l('Image'),
				'class' => 'text-center',
			),
			array(
				'id' => 'product',
				'title' => $this->l('Product'),
				'class' => 'text-center',
			),
			array(
				'id' => 'views',
				'title' => $this->l('Views'),
				'class' => 'text-center',
			),
			array(
				'id' => 'added_to_cart',
				'title' => $this->l('Added to cart'),
				'class' => 'text-center',
			),
			array(
				'id' => 'purchased',
				'title' => $this->l('Purchased'),
				'class' => 'text-center',
			),
			array(
				'id' => 'rate',
				'title' => $this->l('Rate'),
				'class' => 'text-center',
			)
		);

		$products = $this->getTotalViewed($date_from, $date_to);
		$body = array();
		if (is_array($products) && count($products))
			foreach ($products as $product)
			{
				$product_obj = new Product((int)$product['id_object'], true, $this->context->language->id);
				if (!Validate::isLoadedObject($product_obj))
					continue;

				$tr = array();
				$tr[] = array(
					'id' => 'product',
					'value' => '<img src="..'._PS_TMP_IMG_.'product_mini_'.(int)$product_obj->id.'.jpg'.'" />',
					'class' => 'text-center',
				);
				$tr[] = array(
					'id' => 'product',
					'value' => Tools::htmlentitiesUTF8($product_obj->name).'<br/>'.Tools::displayPrice(Product::getPriceStatic((int)$product_obj->id)),
					'class' => 'text-center',
				);
				$tr[] = array(
					'id' => 'views',
					'value' => $product['counter'],
					'class' => 'text-center',
				);
				$added_cart = $this->getTotalProductAddedCart($date_from, $date_to, (int)$product_obj->id);
				$tr[] = array(
					'id' => 'added_to_cart',
					'value' => $added_cart,
					'class' => 'text-center',
				);
				$purchased = $this->getTotalProductPurchased($date_from, $date_to, (int)$product_obj->id);
				$tr[] = array(
					'id' => 'purchased',
					'value' => $this->getTotalProductPurchased($date_from, $date_to, (int)$product_obj->id),
					'class' => 'text-center',
				);
				$tr[] = array(
					'id' => 'rate',
					'value' => ($product['counter'] ? round(100 * $purchased / $product['counter'], 1).'%' : '-'),
					'class' => 'text-center',
				);
				$body[] = $tr;
			}
		
		return array('header' => $header, 'body' => $body);
	}
	
	public function getTableTop10MostSearch($date_from, $date_to)
	{
		$header = array(
			array(
				'id' => 'reference',
				'title' => $this->l('Term'),
				'class' => 'text-left'
			),
			array(
				'id' => 'name',
				'title' => $this->l('Search'),
				'class' => 'text-center'
			),
			array(
				'id' => 'totalQuantitySold',
				'title' => $this->l('Results'),
				'class' => 'text-center'
			)
		);
		
		$terms = $this->getMostSearchTerms($date_from, $date_to);
		$body = array();
		if (is_array($terms) && count($terms))
			foreach ($terms as $term)
			{
				$tr = array();
				$tr[] = array(
					'id' => 'product',
					'value' => $term['keywords'],
					'class' => 'text-left',
					);
				$tr[] = array(
					'id' => 'product',
					'value' => $term['count_keywords'],
					'class' => 'text-center',
					);
				$tr[] = array(
					'id' => 'product',
					'value' => $term['results'],
					'class' => 'text-center',
					);
				$body[] = $tr;
			}

		return array('header' => $header, 'body' => $body);
	}
	
	public function getTableTop5Search()
	{
		$header = array(
			array(
				'id' => 'reference',
				'title' => $this->l('Product'),
			)
		);
		
		$body = array();
		
		return array('header' => $header, 'body' => $body);
	}
	
	public function getTableBestSales()
	{
		$header = array(
			array(
				'id' => 'reference',
				'title' => $this->l('Ref.'),
			),
			array(
				'id' => 'name',
				'title' => $this->l('Name'),
			),
			array(
				'id' => 'totalQuantitySold',
				'title' => $this->l('Quantity sold'),
			),
			array(
				'id' => 'avg_price_sold',
				'title' => $this->l('Price sold'),
			),
			array(
				'id' => 'total_price_sold',
				'title' => $this->l('Sales'),
			),
			array(
				'id' => 'average_quantity_sold',
				'title' => $this->l('Qty sold in a day.'),
			),
			array(
				'id' => 'total_page_viewed',
				'title' => $this->l('Page views'),
			),
			array(
				'id' => 'quantity',
				'title' => $this->l('Available qty for sale.'),
			)
		);
		
		$body = array();
		
		return array('header' => $header, 'body' => $body);
	}
	
	public function getTotalProductSales($date_from, $date_to, $id_product)
	{
		$sql = 'SELECT SUM(od.`product_quantity` * od.`product_price`) AS total
				FROM `'._DB_PREFIX_.'order_detail` od
				JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
				WHERE od.`product_id` = '.(int)$id_product.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND o.`date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"';
		
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}
	
	public function getTotalProductAddedCart($date_from, $date_to, $id_product)
	{
		$sql = 'SELECT count(`id_product`) as count
				FROM `'._DB_PREFIX_.'cart_product` cp
				WHERE cp.`id_product` = '.(int)$id_product.'
					'.Shop::addSqlRestriction(false, 'cp').'
					AND cp.`date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"';

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}
	
	public function getTotalProductPurchased($date_from, $date_to, $id_product)
	{
		$sql = 'SELECT count(`product_id`) as count
				FROM `'._DB_PREFIX_.'order_detail` od
				JOIN `'._DB_PREFIX_.'orders` o ON o.`id_order` = od.`id_order`
				WHERE od.`product_id` = '.(int)$id_product.'
					'.Shop::addSqlRestriction(false, 'od').'
					AND o.valid = 1
					AND o.`date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"';

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}
	
	public function getTotalViewed($date_from, $date_to)
	{
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$products = array();
			if ($result = $gapi->requestReportData('ga:pagePath', 'ga:visits', $date_from, $date_to, '-ga:visits', 'ga:pagePath=~/([a-z]{2}/)?([a-z]+/)?[0-9][0-9]*\-.*\.html$', 1, 10))
				foreach ($result as $row)
				{
					if (preg_match('@/([a-z]{2}/)?([a-z]+/)?([0-9]+)\-.*\.html$@', $row['dimensions']['pagePath'], $matches))
						$id_object = (int)$matches[3];
					$products[] = array('id_object' => $id_object, 'counter' => $row['metrics']['visits']);
				}
			return $products;
		}
		else
		{
			$sql = 'SELECT p.id_object, pv.counter
					FROM `'._DB_PREFIX_.'page_viewed` pv
					LEFT JOIN `'._DB_PREFIX_.'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
					LEFT JOIN `'._DB_PREFIX_.'page` p ON pv.`id_page` = p.`id_page`
					LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
					WHERE pt.`name` = \'product\'
						'.Shop::addSqlRestriction(false, 'pv').'
						AND dr.`time_start` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"
						AND dr.`time_end` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"';
		
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		}
	}
	
	public function getMostSearchTerms($date_from, $date_to, $limit = 10)
	{
		$sql = 'SELECT `keywords`, count(`id_statssearch`) as count_keywords, `results`
				FROM `'._DB_PREFIX_.'statssearch` ss
				WHERE ss.`date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"
					'.Shop::addSqlRestriction(false, 'ss').'
					GROUP BY ss.`keywords`
					ORDER BY `count_keywords` DESC
					LIMIT 0, '.(int)$limit;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}
}