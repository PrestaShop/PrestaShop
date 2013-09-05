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

class Dashactivity extends Module
{
	public function __construct()
	{
		$this->name = 'dashactivity';
		$this->displayName = 'Dashboard Activity';
		$this->tab = '';
		$this->version = '0.1';
		$this->author = 'PrestaShop';
		$this->push_filename = _PS_CACHE_DIR_.'push/activity';
		$this->allow_push = true;
		$this->push_time_limit = 180;

		parent::__construct();
	}

	public function install()
	{
		if (!parent::install() 
			|| !$this->registerHook('dashboardZoneOne') 
			|| !$this->registerHook('dashboardData')
			|| !$this->registerHook('actionObjectOrderAddAfter')
			|| !$this->registerHook('actionObjectCustomerAddAfter')
			|| !$this->registerHook('actionObjectCustomerMessageAddAfter')
			|| !$this->registerHook('actionObjectCustomerThreadAddAfter')
			|| !$this->registerHook('actionObjectOrderReturnAddAfter')
			
		)
			return false;
		return true;
	}

	public function hookDashboardZoneOne($params)
	{
		return $this->display(__FILE__, 'dashboard_zone_one.tpl');
	}
	
	public function hookDashboardData($params)
	{
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$visits = $unique_visitors = 0;
			if ($result = $gapi->requestReportData('', 'ga:visits,ga:visitors', $params['date_from'], $params['date_to'], null, null, 1, 1))
			{
				$visits = $result[0]['metrics']['visits'];
				$unique_visitors = $result[0]['metrics']['visitors'];
			}
		}
		else
		{
			$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT COUNT(*) as visits, COUNT(DISTINCT `id_guest`) as unique_visitors
			FROM `'._DB_PREFIX_.'connections`
			WHERE `date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
			'.Shop::addSqlRestriction(false));
			extract($row);
		}
		
		$order_nbr = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders`
		WHERE `invoice_date` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));

		$abandoned_cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'cart`
		WHERE `date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		
		$return_exchanges = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_return` or2 ON o.id_order = or2.id_order
		WHERE or2.`date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));

		$products_out_of_stock = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		'.Product::sqlStock('p').'
		WHERE IFNULL(stock.quantity, 0) <= 0');
		
		$new_messages = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'customer_thread` ct
		LEFT JOIN `'._DB_PREFIX_.'customer_message` cm ON ct.id_customer_thread = cm.id_customer_thread
		WHERE cm.`date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(false, 'ct'));
		
		if ($maintenance_ips = Configuration::get('PS_MAINTENANCE_IP'))
			$maintenance_ips = implode(',', array_map('ip2long', array_map('trim', explode(',', $maintenance_ips))));
		if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
			$sql = 'SELECT COUNT(DISTINCT c.id_connections)
					FROM `'._DB_PREFIX_.'connections` c
					LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
					WHERE TIME_TO_SEC(TIMEDIFF(NOW(), cp.`time_start`)) < 900
					AND cp.`time_end` IS NULL
					'.Shop::addSqlRestriction(false, 'c').'
					'.($maintenance_ips ? 'AND c.ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '');
		else
			$sql = 'SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'connections`
					WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `date_add`)) < 900
					'.Shop::addSqlRestriction(false).'
					'.($maintenance_ips ? 'AND ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '');
		$online_visitor = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		
		$active_shopping_cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'cart`
		WHERE date_upd > "'.pSQL(date('Y-m-d H:i:s', strtotime('-30 MIN'))).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));

		$new_customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'customer`
		WHERE `date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		
		$new_registrations = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'customer`
		WHERE `newsletter_date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		AND newsletter = 1
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		$total_suscribers = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'customer`
		WHERE newsletter = 1
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		if (Module::isInstalled('blocknewsletter'))
		{
			$new_registrations += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'newsletter`
			WHERE active = 1
			AND `newsletter_date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
			$total_suscribers += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'newsletter`
			WHERE active = 1
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		}

		return array(
			'data_value' => array(
				'order_nbr' => $order_nbr,
				'pending_orders' => 42,
				'return_exchanges' => $return_exchanges,
				'abandoned_cart' => $abandoned_cart,
				'products_out_of_stock' => $products_out_of_stock,
				'new_messages' => $new_messages,
				'order_inquires' => 42,
				'product_reviews' => 42,
				'new_customers' => $new_customers,
				'online_visitor' => $online_visitor,
				'active_shopping_cart' => $active_shopping_cart,
				'new_registrations' => $new_registrations,
				'total_suscribers' => $total_suscribers,
				'visits' => $visits,
				'unique_visitors' => $unique_visitors,
			),
			'data_trends' => array(
				'orders_trends' => array('way' => 'down', 'value' => 0.42),
			)
		);
	}
	
	public function hookActionObjectCustomerMessageAddAfter($params)
	{
		return $this->hookActionObjectOrderAddAfter($params);
	}

	public function hookActionObjectCustomerThreadAddAfter($params)
	{
		return $this->hookActionObjectOrderAddAfter($params);
	}

	public function hookActionObjectCustomerAddAfter($params)
	{
		return $this->hookActionObjectOrderAddAfter($params);
	}

	public function hookActionObjectOrderReturnAddAfter($params)
	{
		return $this->hookActionObjectOrderAddAfter($params);
	}

	public function hookActionObjectOrderAddAfter($params)
	{
		Tools::changeFileMTime($this->push_filename);
	}
}
