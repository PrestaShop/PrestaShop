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

class AdminStatsControllerCore extends AdminStatsTabController
{
	public static function getUniqueVisitors($date_from, $date_to, $granularity = false)
	{
		$visitors = array();
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			if ($result = $gapi->requestReportData($granularity ? 'ga:date' : '', 'ga:visitors', $date_from, $date_to, null, null, 1, 30))
				foreach ($result as $row)
					if ($granularity == 'day')
						$visitors[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-$3', $row['dimensions']['date']))] = $row['metrics']['visitors'];
					elseif ($granularity == false)
						$visitors = $row['metrics']['visitors'];
		}
		else
		{
			if ($granularity == 'day')
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT LEFT(`date_add`, 10) as date, COUNT(DISTINCT id_guest) as visitors
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction(false).'
				GROUP BY LEFT(`date_add`, 10)');
				foreach ($result as $row)
					$visitors[strtotime($row['date'])] = $row['visitors'];
			}
			else
				$visitors = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(DISTINCT id_guest) as visitors
				FROM `'._DB_PREFIX_.'connections`
				WHERE `date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction(false));
		}
		return $visitors;
	}
	
	public static function getAbandonedCarts($date_from, $date_to, $granularity = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT id_guest)
		FROM `'._DB_PREFIX_.'cart`
		WHERE `date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"
		AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
	}
	
	public static function getPercentProductStock()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(IFNULL(stock.quantity, 0) > 0, 1, 0)) as with_stock, COUNT(*) as products
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Product::sqlStock('p', 'pa'));
		return round($row['products'] ? 100 * $row['with_stock'] / $row['products'] : 0, 2).'%';
	}
	
	public static function getProductAverageGrossMargin()
	{
		$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT AVG((IFNULL(product_attribute_shop.price, product_shop.price) - IFNULL(product_attribute_shop.wholesale_price, product_shop.wholesale_price)) / IFNULL(product_attribute_shop.price, product_shop.price))
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Shop::addSqlAssociation('product_attribute', 'pa'));
		return round(100 * $value, 2).'%';
	}
	
	public static function getDisabledCategories()
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE c.active = 0');
	}
	
	public static function getDisabledProducts()
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		WHERE product_shop.active = 0');
	}
	
	public static function getTotalSales($date_from, $date_to)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(total_paid_tax_excl / o.conversion_rate)
		FROM `'._DB_PREFIX_.'orders` o
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));
	}
	
	public static function get8020SalesCatalog($date_from, $date_to)
	{
		$total_sales = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(total_price_tax_excl / o.conversion_rate) as product_sales
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));
		if (!$total_sales)
			return '0%';

		$total_products = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p'));

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT SUM(total_price_tax_excl / o.conversion_rate) as product_sales
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
		GROUP BY od.product_id, od.product_attribute_id
		ORDER BY SUM(total_price_tax_excl) DESC');

		$products = 0;
		$products_sales = 0;
		foreach ($result as $row)
		{
			++$products;
			$products_sales += $row['product_sales'];
			if ($products_sales > $total_sales)
				break;
		}
		return round(100 * $products / $total_products).'%';
	}

	public static function getOrders($date_from, $date_to, $granularity = false)
	{
		if ($granularity == 'day')
		{
			$orders = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT LEFT(`invoice_date`, 10) as date, COUNT(*) as orders
			FROM `'._DB_PREFIX_.'orders`
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER).'
			GROUP BY LEFT(`invoice_date`, 10)');
			foreach ($result as $row)
				$orders[strtotime($row['date'])] = $row['orders'];
		}
		else
			$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*) as orders
			FROM `'._DB_PREFIX_.'orders`
			WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		return $orders;
	}
	
	public static function getEmptyCategories()
	{
		$total = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		AND c.active = 1
		AND c.nright = c.nleft + 1');
		$used = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT cp.id_category)
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON c.id_category = cp.id_category
		'.Shop::addSqlAssociation('category', 'cp').'
		AND c.active = 1
		AND c.nright = c.nleft + 1');
		return intval($total - $used);
	}
	
	public static function getCustomerMainGender()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT SUM(IF(g.id_gender IS NOT NULL, 1, 0)) as total, SUM(IF(type = 0, 1, 0)) as male, SUM(IF(type = 1, 1, 0)) as female, SUM(IF(type = 2, 1, 0)) as neutral
		FROM `'._DB_PREFIX_.'customer` c
		'.Shop::addSqlAssociation('customer', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'gender` g ON c.id_gender = g.id_gender
		WHERE c.active = 1');
		if (!$row['total'])
			$value = $this->l('No customers');
		elseif ($row['male'] > $row['female'] && $row['male'] > $row['neutral'])
			$value = sprintf($this->l('%d%% Men Customers'), round(100 * $row['male'] / $row['total']));
		elseif ($row['female'] > $row['male'] && $row['female'] > $row['neutral'])
			$value = sprintf($this->l('%d%% Women Customers'), round(100 * $row['female'] / $row['total']));
		else
			$value = sprintf($this->l('%d%% Neutral Customers'), round(100 * $row['neutral'] / $row['total']));
		return $value;
	}
	
	public static function getAverageCustomerAge()
	{
		$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT AVG(birthday)
		FROM `'._DB_PREFIX_.'customer` c
		'.Shop::addSqlAssociation('customer', 'c').'
		WHERE active = 1
		AND birthday IS NOT NULL AND birthday != "0000-00-00"');
		return round((time() - strtotime($value)) / 86400 / 365, 1);
	}

	public function displayAjaxGetKpi()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		switch (Tools::getValue('kpi'))
		{
			case 'conversion_rate':
				$visitors = AdminStatsController::getUniqueVisitors(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), 'day');
				$orders = AdminStatsController::getOrders(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), 'day');

				$data = array();
				$from = strtotime(date('Y-m-d 00:00:00', strtotime('-31 day')));
				$to = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
				for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
					if (isset($visitors[$date]) && $visitors[$date])
						$data[$date] = round(100 * ((isset($orders[$date]) && $orders[$date]) ? $orders[$date] : 0) / $visitors[$date], 2);
					else
						$data[$date] = 0;

				$visits_sum = array_sum($visitors);
				$orders_sum = array_sum($orders);
				if ($visits_sum)
					$value = round(100 * $orders_sum / $visits_sum, 2);
				elseif ($orders_sum)
					$value = '&infin;';
				else
					$value = 0;
				$value .= '%';
				
				ConfigurationKPI::updateValue('CONVERSION_RATE_CHART', Tools::jsonEncode($data));
				ConfigurationKPI::updateValue('CONVERSION_RATE', $value);
				ConfigurationKPI::updateValue('CONVERSION_RATE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'abandoned_cart':
				$value = AdminStatsController::getAbandonedCarts(date('Y-m-d H:i:s', strtotime('-2 day')), date('Y-m-d H:i:s', strtotime('-1 day')));
				ConfigurationKPI::updateValue('ABANDONED_CARTS', $value);
				ConfigurationKPI::updateValue('ABANDONED_CARTS_EXPIRE', strtotime('+10 min'));
				break;

			case 'percent_product_stock':
				$value = AdminStatsController::getPercentProductStock();
				ConfigurationKPI::updateValue('PERCENT_PRODUCT_STOCK', $value);
				ConfigurationKPI::updateValue('PERCENT_PRODUCT_STOCK_EXPIRE', strtotime('+4 hour'));
				break;

			case 'product_avg_gross_margin':
				$value = AdminStatsController::getProductAverageGrossMargin();
				ConfigurationKPI::updateValue('PRODUCT_AVG_GROSS_MARGIN', $value);
				ConfigurationKPI::updateValue('PRODUCT_AVG_GROSS_MARGIN_EXPIRE', strtotime('+6 hour'));
				break;

			case 'disabled_categories':
				$value = AdminStatsController::getDisabledCategories();
				ConfigurationKPI::updateValue('DISABLED_CATEGORIES', $value);
				ConfigurationKPI::updateValue('DISABLED_CATEGORIES_EXPIRE', strtotime('+2 hour'));
				break;

			case 'disabled_products':
				$value = AdminStatsController::getDisabledProducts();
				ConfigurationKPI::updateValue('DISABLED_PRODUCTS', $value);
				ConfigurationKPI::updateValue('DISABLED_PRODUCTS_EXPIRE', strtotime('+2 hour'));
				break;

			case '8020_sales_catalog':
				$value = AdminStatsController::get8020SalesCatalog(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')));
				$value .= ' '.$this->l('of your Catalog');
				ConfigurationKPI::updateValue('8020_SALES_CATALOG', $value);
				ConfigurationKPI::updateValue('8020_SALES_CATALOG_EXPIRE', strtotime('+12 hour'));
				break;

			case 'empty_categories':
				$value = AdminStatsController::getEmptyCategories();
				ConfigurationKPI::updateValue('EMPTY_CATEGORIES', $value);
				ConfigurationKPI::updateValue('EMPTY_CATEGORIES_EXPIRE', strtotime('+2 hour'));
				break;

			case 'customer_main_gender':
				$value = AdminStatsController::getCustomerMainGender();
				ConfigurationKPI::updateValue('CUSTOMER_MAIN_GENDER', $value);
				ConfigurationKPI::updateValue('CUSTOMER_MAIN_GENDER_EXPIRE', strtotime('+1 day'));
				break;

			case 'avg_customer_age':
				$value = sprintf($this->l('%.1f years'), AdminStatsController::getAverageCustomerAge(), 1);
				ConfigurationKPI::updateValue('AVG_CUSTOMER_AGE', $value);
				ConfigurationKPI::updateValue('AVG_CUSTOMER_AGE_EXPIRE', strtotime('+1 day'));
				break;

			case 'newsletter_registrations':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'customer`
				WHERE newsletter = 1
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				if (Module::isInstalled('blocknewsletter'))
				{
					$value += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
					SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'newsletter`
					WHERE active = 1
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				}

				ConfigurationKPI::updateValue('NEWSLETTER_REGISTRATIONS', $value);
				ConfigurationKPI::updateValue('NEWSLETTER_REGISTRATIONS_EXPIRE', strtotime('+6 hour'));
				break;

			case 'orders_per_customer':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'customer`
				'.Shop::addSqlAssociation('customer').'
				WHERE active = 1');
				if ($value)
				{
					$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
					SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'orders`
					'.Shop::addSqlAssociation('orders').'
					WHERE valid = 1');
					$value = round($orders / $value, 2);
				}

				ConfigurationKPI::updateValue('ORDERS_PER_CUSTOMER', $value);
				ConfigurationKPI::updateValue('ORDERS_PER_CUSTOMER_EXPIRE', strtotime('+1 day'));
				break;

			case 'average_order_value':
				$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT
					COUNT(`id_order`) as orders,
					SUM(`total_paid_tax_excl` / `conversion_rate`) as total_paid_tax_excl
				FROM `'._DB_PREFIX_.'orders`
				WHERE `invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				$value = Tools::displayPrice($row['orders'] ? $row['total_paid_tax_excl'] / $row['orders'] : 0, $currency);
				ConfigurationKPI::updateValue('AVG_ORDER_VALUE', $value);
				ConfigurationKPI::updateValue('AVG_ORDER_VALUE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'netprofit_visitor':
				$date_from = date('Y-m-d', strtotime('-31 day'));
				$date_to = date('Y-m-d', strtotime('-1 day'));
				$visitors = AdminStatsController::getUniqueVisitors($date_from, $date_to);
				$total_sales = AdminStatsController::getTotalSales($date_from, $date_to);
				$total_purchase = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(od.`product_quantity` * od.`purchase_supplier_price` / `conversion_rate`) as total_purchase_price
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));

				$net_profits = $total_sales;
				$net_profits -= $total_purchase;
				// Todo : Add profitability calculation

				if ($visitors)
					$value = Tools::displayPrice($net_profits / $visitors, $currency);
				elseif ($net_profits)
					$value = '&infin;';
				else
					$value = Tools::displayPrice(0, $currency);
	
				ConfigurationKPI::updateValue('NETPROFIT_VISITOR', $value);
				ConfigurationKPI::updateValue('NETPROFIT_VISITOR_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			default:
				$value = false;
		}
		if ($value !== false)
		{
			$array = array('value' => $value);
			if (isset($data))
				$array['data'] = $data;
			die(Tools::jsonEncode($array));
		}
		die(Tools::jsonEncode(array('has_errors' => true)));
	}
}