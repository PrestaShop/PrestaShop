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
		foreach ($this->getConfigFieldsValues() as $conf_name => $conf)
			Configuration::updateValue($conf_name, true);
		
		if (!parent::install() 
			|| !$this->registerHook('dashboardZoneOne') 
			|| !$this->registerHook('dashboardData')
			|| !$this->registerHook('actionObjectOrderAddAfter')
			|| !$this->registerHook('actionObjectCustomerAddAfter')
			|| !$this->registerHook('actionObjectCustomerMessageAddAfter')
			|| !$this->registerHook('actionObjectCustomerThreadAddAfter')
			|| !$this->registerHook('actionObjectOrderReturnAddAfter')
			|| !$this->registerHook('displayBackOfficeHeader')
		)
			return false;
		return true;
	}
	
	public function hookDisplayBackOfficeHeader()
	{
		if (get_class($this->context->controller) == 'AdminDashboardController')
			$this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
	}

	public function hookDashboardZoneOne($params)
	{
		$this->context->smarty->assign(array_merge(array('dashactivity_config_form' => $this->renderConfigForm()), $this->getConfigFieldsValues()));
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
		
		$pending_orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (o.current_state = os.id_order_state)
		WHERE os.paid = 1 AND os.shipped = 0
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
		
		$product_reviews = 0;
		if (Module::isInstalled('productcomments'))
		{
			$new_registrations += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'product_comment` pc
			LEFT JOIN `'._DB_PREFIX_.'product` p ON (pc.id_product = p.id_product)
			'.Shop::addSqlAssociation('product', 'p').'
			WHERE pc.deleted = 0
			AND pc.`date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
			'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		}

		return array(
			'data_value' => array(
				'order_nbr' => $order_nbr,
				'pending_orders' => $pending_orders,
				'return_exchanges' => $return_exchanges,
				'abandoned_cart' => $abandoned_cart,
				'products_out_of_stock' => $products_out_of_stock,
				'new_messages' => $new_messages,
				'order_inquires' => 42,
				'product_reviews' => $product_reviews,
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
			),
			'data_list_small' => array(
				'dash_traffic_source' => $this->getReferer($params['date_from'], $params['date_to']),
			),
			'data_chart' => array(
				'dash_trends_chart1' => $this->getChartTrafficSource($params['date_from'], $params['date_to']),
			),
		);
	}
	
	public function getChartTrafficSource($date_from, $date_to)
	{
		$referers = $this->getReferer($date_from, $date_to);
		$return = array('chart_type' => 'pie_chart_trends', 'data' => array());
		foreach ($referers as $referer_name => $nbr)
			$return['data'][] = array('key' => $referer_name, 'y' => $nbr);

		return $return;
	}
	
	public function getReferer($date_from, $date_to, $limit = 10)
	{
		$directLink = $this->l('Direct link');
		$sql = 'SELECT http_referer
				FROM '._DB_PREFIX_.'connections
				WHERE 1
					'.Shop::addSqlRestriction().'
					AND date_add BETWEEN '.$date_from.' AND '.$date_to.'
					LIMIT 0, '.(int)$limit;
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);
		$websites = array($directLink => 0);
		
		while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($result))
		{
			if (!isset($row['http_referer']) || empty($row['http_referer']))
				++$websites[$directLink];
			else
			{
				$website = preg_replace('/^www./', '', parse_url($row['http_referer'], PHP_URL_HOST));
				if (!isset($websites[$website]))
					$websites[$website] = 1;
				else
					++$websites[$website];
			}
		}
		arsort($websites);
		return $websites;
	}
	
	public function renderConfigForm()
	{
		$fields_form = array(
			'form' => array(
				'id_form' => 'step_carrier_general',
				'input' => array(),
				'submit' => array(
					'title' => $this->l('   Save   '),
					'class' => 'btn submit_dash_config'
					)
				),
			);
			
		$sub_widget = array(
			array('label' => $this->l('Show Pending'), 'config_name' => 'DASHACTIVITY_SHOW_PENDING'),
			array('label' => $this->l('Show Notification'), 'config_name' => 'DASHACTIVITY_SHOW_NOTIFICATION'),
			array('label' => $this->l('Show Customers'), 'config_name' => 'DASHACTIVITY_SHOW_CUSTOMERS'),
			array('label' => $this->l('Show Newsletter'), 'config_name' => 'DASHACTIVITY_SHOW_NEWSLETTER'),
			array('label' => $this->l('Show Traffic'), 'config_name' => 'DASHACTIVITY_SHOW_TRAFFIC'),
			);
		
		foreach($sub_widget as $widget)
			$fields_form['form']['input'][] = array(
				'type' => 'switch',
				'label' => $widget['label'],
				'name' => $widget['config_name'],
				'is_bool' => true,
				'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
				);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitDashConfig';
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'DASHACTIVITY_SHOW_PENDING' => Tools::getValue('DASHACTIVITY_SHOW_PENDING', Configuration::get('DASHACTIVITY_SHOW_PENDING')),
			'DASHACTIVITY_SHOW_NOTIFICATION' => Tools::getValue('DASHACTIVITY_SHOW_NOTIFICATION', Configuration::get('DASHACTIVITY_SHOW_NOTIFICATION')),
			'DASHACTIVITY_SHOW_CUSTOMERS' => Tools::getValue('DASHACTIVITY_SHOW_CUSTOMERS', Configuration::get('DASHACTIVITY_SHOW_CUSTOMERS')),
			'DASHACTIVITY_SHOW_NEWSLETTER' => Tools::getValue('DASHACTIVITY_SHOW_NEWSLETTER', Configuration::get('DASHACTIVITY_SHOW_NEWSLETTER')),
			'DASHACTIVITY_SHOW_TRAFFIC' => Tools::getValue('DASHACTIVITY_SHOW_TRAFFIC', Configuration::get('DASHACTIVITY_SHOW_TRAFFIC')),
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
