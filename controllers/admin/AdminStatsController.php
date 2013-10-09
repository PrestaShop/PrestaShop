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
	
	public static function getAbandonedCarts($date_from, $date_to)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT id_guest)
		FROM `'._DB_PREFIX_.'cart`
		WHERE `date_add` BETWEEN "'.pSQL($date_from).'" AND "'.pSQL($date_to).'"
		AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
	}
	
	public static function getInstalledModules()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'module` m
		'.Shop::addSqlAssociation('module', 'm'));
	}
	
	public static function getDisabledModules()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'module` m
		'.Shop::addSqlAssociation('module', 'm').'
		WHERE `active` = 0');
	}
	
	public static function getModulesToUpdate()
	{
		$context = Context::getContext();
		$logged_on_addons = false;
		if (isset($context->cookie->username_addons) && isset($context->cookie->password_addons)
		&& !empty($context->cookie->username_addons) && !empty($context->cookie->password_addons))
			$logged_on_addons = true;
		$modules = Module::getModulesOnDisk(true, $logged_on_addons, $context->employee->id);
		$upgrade_available = 0;
		foreach ($modules as $km => $module)
			if ($module->installed && isset($module->version_addons) && (int)(string)$module->version_addons) // SimpleXMLElement 
				++$upgrade_available;
		return $upgrade_available;
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
		'.Shop::addSqlAssociation('category', 'c').'
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
			return false;
		elseif ($row['male'] > $row['female'] && $row['male'] > $row['neutral'])
			return array('type' => 'male', 'value' => round(100 * $row['male'] / $row['total']));
		elseif ($row['female'] > $row['male'] && $row['female'] > $row['neutral'])
			return array('type' => 'female', 'value' => round(100 * $row['female'] / $row['total']));
		return array('type' => 'neutral', 'value' => round(100 * $row['neutral'] / $row['total']));
	}
	
	public static function getMainCountry($date_from, $date_to)
	{
		$total_orders = AdminStatsController::getOrders($date_from, $date_to);
		if (!$total_orders)
			return false;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT a.id_country, COUNT(*) as orders
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_delivery = a.id_address
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		$row['orders'] = round(100 * $row['orders'] / $total_orders, 1);
		return $row;
	}
	
	public static function getAverageCustomerAge()
	{
		$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT AVG(DATEDIFF(NOW(), birthday))
		FROM `'._DB_PREFIX_.'customer` c
		'.Shop::addSqlAssociation('customer', 'c').'
		WHERE active = 1
		AND birthday IS NOT NULL AND birthday != "0000-00-00"');
		return round($value / 365);
	}

	public static function getPendingMessages()
	{
		return CustomerThread::getTotalCustomerThreads('status LIKE "%pending%" OR status = "open"');
	}

	public static function getAverageMessageResponseTime($date_from, $date_to)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT MIN(cm1.date_add) as question, MIN(cm2.date_add) as reply
		FROM `'._DB_PREFIX_.'customer_message` cm1
		INNER JOIN `'._DB_PREFIX_.'customer_message` cm2 ON (cm1.id_customer_thread = cm2.id_customer_thread AND cm1.date_add < cm2.date_add)
		WHERE cm1.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		AND cm1.id_employee = 0 AND cm2.id_employee != 0
		GROUP BY cm1.id_customer_thread');
		$total_questions = $total_replies = $threads = 0;
		foreach ($result as $row)
		{
			++$threads;
			$total_questions += strtotime($row['question']);
			$total_replies += strtotime($row['reply']);
		}
		if (!$threads)
			return 0;
		return round(($total_replies - $total_questions) / $threads / 3600, 1);
	}

	public static function getMessagesPerThread($date_from, $date_to)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT COUNT(*) as messages
		FROM `'._DB_PREFIX_.'customer_thread` ct
		LEFT JOIN `'._DB_PREFIX_.'customer_message` cm ON (ct.id_customer_thread = cm.id_customer_thread)
		WHERE ct.`date_add` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		AND status = "closed"
		GROUP BY ct.id_customer_thread');
		$threads = $messages = 0;
		foreach ($result as $row)
		{
			++$threads;
			$messages += $row['messages'];
		}
		if (!$threads)
			return 0;
		return round($messages / $threads, 1);
	}
	
	public static function getExpenses($date_from, $date_to)
	{
		$secs_per_year = 365.25 * 86400;
		$secs_per_month = 30.4375 * 86400;
		$total_secs = (strtotime($date_to) - min(strtotime($date_from), time())) / 86400;
		$expenses = Configuration::get('CONF_MONTHLY_FEES') * $total_secs / $secs_per_month + Configuration::get('CONF_YEARLY_FEES') * $total_secs / $secs_per_year;

		$orders = Db::getInstance()->ExecuteS('
		SELECT
			total_paid_tax_incl / o.conversion_rate as total_paid_tax_incl,
			total_shipping_tax_excl / o.conversion_rate as total_shipping_tax_excl,
			module
		FROM `'._DB_PREFIX_.'orders` o
		WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));
		foreach ($orders as $order)
		{
			// Add flat fees for this order
			$expenses += Configuration::get('CONF_ORDER_FIXED_FEES') + Configuration::get('CONF_'.strtoupper($order['module']).'_FIXED_FEE');
			// Add variable fees for this order
			$expenses += $order['total_paid_tax_incl'] * (Configuration::get('CONF_ORDER_VAR_FEES') + Configuration::get('CONF_'.strtoupper($order['module']).'_VAR_FEE')) / 100;
			// Add shipping fees for this order
			$expenses += $order['total_shipping_tax_excl'] * (100 - Configuration::get('CONF_SHIPPING_MARGIN')) / 100;
		}
		return $expenses;
	}

	public function displayAjaxGetKpi()
	{
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		switch (Tools::getValue('kpi'))
		{
			case 'conversion_rate':
				$visitors = AdminStatsController::getUniqueVisitors(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), false /*'day'*/);
				$orders = AdminStatsController::getOrders(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day')), false /*'day'*/);

				// $data = array();
				// $from = strtotime(date('Y-m-d 00:00:00', strtotime('-31 day')));
				// $to = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
				// for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
					// if (isset($visitors[$date]) && $visitors[$date])
						// $data[$date] = round(100 * ((isset($orders[$date]) && $orders[$date]) ? $orders[$date] : 0) / $visitors[$date], 2);
					// else
						// $data[$date] = 0;

				$visits_sum = $visitors; //array_sum($visitors);
				$orders_sum = $orders; //array_sum($orders);
				if ($visits_sum)
					$value = round(100 * $orders_sum / $visits_sum, 2);
				elseif ($orders_sum)
					$value = '&infin;';
				else
					$value = 0;
				$value .= '%';

				// ConfigurationKPI::updateValue('CONVERSION_RATE_CHART', Tools::jsonEncode($data));
				ConfigurationKPI::updateValue('CONVERSION_RATE', $value);
				ConfigurationKPI::updateValue('CONVERSION_RATE_EXPIRE', strtotime(date('Y-m-d 00:00:00', strtotime('+1 day'))));
				break;

			case 'abandoned_cart':
				$value = AdminStatsController::getAbandonedCarts(date('Y-m-d H:i:s', strtotime('-2 day')), date('Y-m-d H:i:s', strtotime('-1 day')));
				ConfigurationKPI::updateValue('ABANDONED_CARTS', $value);
				ConfigurationKPI::updateValue('ABANDONED_CARTS_EXPIRE', strtotime('+1 hour'));
				break;

			case 'installed_modules':
				$value = AdminStatsController::getInstalledModules();
				ConfigurationKPI::updateValue('INSTALLED_MODULES', $value);
				ConfigurationKPI::updateValue('INSTALLED_MODULES_EXPIRE', strtotime('+2 min'));
				break;

			case 'disabled_modules':
				$value = AdminStatsController::getDisabledModules();
				ConfigurationKPI::updateValue('DISABLED_MODULES', $value);
				ConfigurationKPI::updateValue('DISABLED_MODULES_EXPIRE', strtotime('+2 min'));
				break;

			case 'update_modules':
				$value = AdminStatsController::getModulesToUpdate();
				ConfigurationKPI::updateValue('UPDATE_MODULES', $value);
				ConfigurationKPI::updateValue('UPDATE_MODULES_EXPIRE', strtotime('+2 min'));
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
				
				if ($value === false)
					$value = $this->l('No customers');
				elseif ($value['type'] == 'male')
					$value = sprintf($this->l('%d%% Men Customers'), $value['value']);
				elseif ($value['type'] == 'female')
					$value = sprintf($this->l('%d%% Women Customers'), $value['value']);
				else
					$value = sprintf($this->l('%d%% Neutral Customers'), $value['value']);
				
				ConfigurationKPI::updateValue('CUSTOMER_MAIN_GENDER', $value);
				ConfigurationKPI::updateValue('CUSTOMER_MAIN_GENDER_EXPIRE', strtotime('+1 day'));
				break;

			case 'avg_customer_age':
				$value = sprintf($this->l('%d years'), AdminStatsController::getAverageCustomerAge(), 1);
				ConfigurationKPI::updateValue('AVG_CUSTOMER_AGE', $value);
				ConfigurationKPI::updateValue('AVG_CUSTOMER_AGE_EXPIRE', strtotime('+1 day'));
				break;

			case 'pending_messages':
				$value = (int)AdminStatsController::getPendingMessages();
				ConfigurationKPI::updateValue('PENDING_MESSAGES', $value);
				ConfigurationKPI::updateValue('PENDING_MESSAGES_EXPIRE', strtotime('+5 min'));
				break;

			case 'avg_msg_response_time':
				$value = sprintf($this->l('%.1f hours'), AdminStatsController::getAverageMessageResponseTime(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day'))));
				ConfigurationKPI::updateValue('AVG_MSG_RESPONSE_TIME', $value);
				ConfigurationKPI::updateValue('AVG_MSG_RESPONSE_TIME_EXPIRE', strtotime('+4 hour'));
				break;

			case 'messages_per_thread':
				$value = round(AdminStatsController::getMessagesPerThread(date('Y-m-d', strtotime('-31 day')), date('Y-m-d', strtotime('-1 day'))), 1);
				ConfigurationKPI::updateValue('MESSAGES_PER_THREAD', $value);
				ConfigurationKPI::updateValue('MESSAGES_PER_THREAD_EXPIRE', strtotime('+12 hour'));
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

			case 'enabled_languages':
				$value = Language::countActiveLanguages();
				ConfigurationKPI::updateValue('ENABLED_LANGUAGES', $value);
				ConfigurationKPI::updateValue('ENABLED_LANGUAGES_EXPIRE', strtotime('+1 min'));
				break;

			case 'frontoffice_translations':
				$themes = Theme::getThemes();
				$languages = Language::getLanguages();
				$total = $translated = 0;
				foreach ($themes as $theme)
					foreach ($languages as $language)
					{
						$kpi_key = substr(strtoupper($theme->name.'_'.$language['iso_code']), 0, 16);
						$total += ConfigurationKPI::get('TRANSLATE_TOTAL_'.$kpi_key);
						$translated += ConfigurationKPI::get('TRANSLATE_DONE_'.$kpi_key);
					}
				$value = 0;
				if ($translated)
					$value = round(100 * $translated / $total, 1);
				$value .= '%';
				ConfigurationKPI::updateValue('FRONTOFFICE_TRANSLATIONS', $value);
				ConfigurationKPI::updateValue('FRONTOFFICE_TRANSLATIONS_EXPIRE', strtotime('+2 min'));
				break;

			case 'main_country':
				if (!($row = AdminStatsController::getMainCountry(date('Y-m-d', strtotime('-30 day')), date('Y-m-d'))))
					$value = $this->l('No orders');
				else
				{
					$country = new Country($row['id_country'], $this->context->language->id);
					$value = sprintf($this->l('%d%% %s'), $row['orders'], $country->name);
				}

				ConfigurationKPI::updateValue('MAIN_COUNTRY', array($this->context->language->id => $value));				
				ConfigurationKPI::updateValue('MAIN_COUNTRY_EXPIRE', array($this->context->language->id => strtotime('+1 day')));
				break;

			case 'orders_per_customer':
				$value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'customer` c
				'.Shop::addSqlAssociation('customer', 'c').'
				WHERE active = 1');
				if ($value)
				{
					$orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
					SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'orders` o
					'.Shop::addSqlAssociation('orders', 'o').'
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
				$total_visitors = AdminStatsController::getUniqueVisitors($date_from, $date_to);
				$total_sales = AdminStatsController::getTotalSales($date_from, $date_to);
				$total_expenses = AdminStatsController::getExpenses($date_from, $date_to);
				$total_purchases = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(od.`product_quantity` * od.`purchase_supplier_price` / `conversion_rate`) as total_purchase_price
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				WHERE `invoice_date` BETWEEN "'.pSQL($date_from).' 00:00:00" AND "'.pSQL($date_to).' 23:59:59"
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));

				$net_profits = $total_sales;
				$net_profits -= $total_purchases;
				$net_profits -= $total_expenses;

				if ($total_visitors)
					$value = Tools::displayPrice($net_profits / $total_visitors, $currency);
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