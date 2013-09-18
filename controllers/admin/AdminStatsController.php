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
	public function displayAjaxGetKpi()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		switch (Tools::getValue('kpi'))
		{
			case 'conversion_rate':
				$visits = array();
				$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
				if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
				{
					if ($result = $gapi->requestReportData('ga:date', 'ga:visits', date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), null, null, 1, 30))
						foreach ($result as $row)
							$visits[strtotime(preg_replace('/^([0-9]{4})([0-9]{2})([0-9]{2})$/', '$1-$2-$3', $row['dimensions']['date']))] = $row['metrics']['visits'];
				}
				else
				{
					$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT LEFT(`date_add`, 10) as date, COUNT(*) as visits
					FROM `'._DB_PREFIX_.'connections`
					WHERE `date_add` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
					'.Shop::addSqlRestriction(false).'
					GROUP BY LEFT(`date_add`, 10)');
					foreach ($result as $row)
						$visits[strtotime($row['date'])] = $row['visits'];
				}
				$orders = array();
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT LEFT(`invoice_date`, 10) as date, COUNT(*) as orders
				FROM `'._DB_PREFIX_.'orders`
				WHERE `invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER).'
				GROUP BY LEFT(`invoice_date`, 10)');
				foreach ($result as $row)
					$orders[strtotime($row['date'])] = $row['orders'];

				$data = array();
				$from = strtotime(date('Y-m-d 00:00:00', strtotime('-31 day')));
				$to = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
				for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
					if (isset($visits[$date]) && $visits[$date])
						$data[$date] = round(100 * ((isset($orders[$date]) && $orders[$date]) ? $orders[$date] : 0) / $visits[$date], 2);
					else
						$data[$date] = 0;

				$visits_sum = array_sum($visits);
				$orders_sum = array_sum($orders);
				if ($visits_sum)
					$value = round(100 * $orders_sum / $visits_sum, 2);
				elseif ($orders_sum)
					$value = '&infin;';
				else
					$value = 0;
				$value .= '%';
				
				Configuration::updateValue('PS_KPI_CONVERSION_RATE_CHART', Tools::jsonEncode($data));
				Configuration::updateValue('PS_KPI_CONVERSION_RATE', $value);
				Configuration::updateValue('PS_KPI_CONVERSION_RATE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'abandoned_cart':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'cart`
				WHERE `date_add` BETWEEN "'.pSQL(date('Y-m-d')).' 00:00:00" AND "'.pSQL(date('Y-m-d')).' 23:59:59"
				AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				Configuration::updateValue('PS_KPI_ABANDONED_CARTS', $value);
				Configuration::updateValue('PS_KPI_ABANDONED_CARTS_EXPIRE', strtotime('+10 min'));
				break;

			case 'products_stock':
				$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT SUM(IF(IFNULL(stock.quantity, 0) > 0, 1, 0)) as stock, COUNT(*) as total
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				'.Product::sqlStock('p'));
				$value = round($row['total'] ? $row['stock'] / $row['total'] : 0, 2).'%';
				Configuration::updateValue('PS_KPI_PRODUCTS_STOCK', $value);
				Configuration::updateValue('PS_KPI_PRODUCTS_STOCK_EXPIRE', strtotime('+10 min'));
				break;

			case 'avg_gross_margin':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT AVG((product_shop.price - product_shop.wholesale_price) / product_shop.price)
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p'));
				$value = round(100 * $value, 2).'%';
				Configuration::updateValue('PS_KPI_AVG_GROSS_MARGIN', $value);
				Configuration::updateValue('PS_KPI_AVG_GROSS_MARGIN_EXPIRE', strtotime('+6 hour'));
				break;

			case 'disabled_categories':
				$value = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'category` c
				'.Shop::addSqlAssociation('category', 'c').'
				WHERE c.active = 0');
				Configuration::updateValue('PS_KPI_DISABLED_CATS', $value);
				Configuration::updateValue('PS_KPI_DISABLED_CATS_EXPIRE', strtotime('+2 hour'));
				break;

			case 'empty_categories':
				$total = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'category` c
				'.Shop::addSqlAssociation('category', 'c'));
				$used = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(DISTINCT cp.id_category)
				FROM `'._DB_PREFIX_.'category_product` cp
				'.Shop::addSqlAssociation('category', 'cp'));
				$value = intval($total - $used);
				Configuration::updateValue('PS_KPI_EMPTY_CATS', $value);
				Configuration::updateValue('PS_KPI_EMPTY_CATS_EXPIRE', strtotime('+2 hour'));
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
				Configuration::updateValue('PS_KPI_AVG_ORDER_VALUE', $value);
				Configuration::updateValue('PS_KPI_AVG_ORDER_VALUE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'netprofit_visitor':
				$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
				if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
				{
					$visitors = 0;
					if ($result = $gapi->requestReportData('', 'ga:visitors', date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), null, null, 1, 1))
						$visitors = $result[0]['metrics']['visitors'];
				}
				else
				{
					$visitors = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
					SELECT COUNT(DISTINCT id_guest)
					FROM `'._DB_PREFIX_.'connections`
					WHERE `date_add` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
					'.Shop::addSqlRestriction(false));
				}

				$row_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT
					SUM(od.`total_price_tax_excl` / `conversion_rate`) as total_product_price_tax_excl,
					SUM(od.`product_quantity` * od.`purchase_supplier_price` / `conversion_rate`) as total_purchase_price
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				WHERE `invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));
				extract($row_products);
				
				$total_discounts_tax_excl = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(`total_discounts_tax_excl` / `conversion_rate`) as total_discounts_tax_excl
				FROM `'._DB_PREFIX_.'orders`
				WHERE `invoice_date` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
				
				$total_credit_tax_excl = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(os.`amount` / o.`conversion_rate`) as total_credit_tax_excl
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_slip` os ON o.id_order = os.id_order
				WHERE os.`date_add` BETWEEN "'.pSQL(date('Y-m-d', strtotime('-31 day'))).' 00:00:00" AND "'.pSQL(date('Y-m-d', strtotime('-1 day'))).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));

				$net_profits = 0;
				$net_profits += $total_product_price_tax_excl;
				$net_profits -= $total_discounts_tax_excl;
				$net_profits -= $total_purchase_price;
				$net_profits -= $total_credit_tax_excl;

				if ($visitors)
					$value = Tools::displayPrice($net_profits / $visitors, $currency);
				elseif ($net_profits)
					$value = '&infin;';
				else
					$value = Tools::displayPrice(0, $currency);
	
				Configuration::updateValue('PS_KPI_NETPROFIT_VISITOR', $value);
				Configuration::updateValue('PS_KPI_NETPROFIT_VISITOR_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
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