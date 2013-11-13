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
		Configuration::updateValue('DASHACTIVITY_CART_ACTIVE', 30);
		Configuration::updateValue('DASHACTIVITY_CART_ABANDONED_MIN', 24);
		Configuration::updateValue('DASHACTIVITY_CART_ABANDONED_MAX', 48);
		Configuration::updateValue('DASHACTIVITY_VISITOR_ONLINE', 24);
		
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
		{
			if (method_exists($this->context->controller, 'addJquery'))
				$this->context->controller->addJquery();

			$this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
			$this->context->controller->addJs(_PS_JS_DIR_.'date.js');
		}
	}

	public function hookDashboardZoneOne($params)
	{
		if (!Module::isInstalled('gapi'))
			$gapi_mode = 'install';
		elseif (($gapi = Module::getInstanceByName('gapi')) && Validate::isLoadedObject($gapi) && $gapi->isConfigured())
			$gapi_mode = false;
		else
			$gapi_mode = 'configure';

		$this->context->smarty->assign(array_merge(array(
			'gapi_mode' => $gapi_mode,
			'dashactivity_config_form' => $this->renderConfigForm(),
			'date_subtitle' => $this->l('(from %s to %s)'),
			'date_format' => $this->context->language->date_format_lite,
			'link' => $this->context->link,
		), $this->getConfigFieldsValues()));
		return $this->display(__FILE__, 'dashboard_zone_one.tpl');
	}
	
	public function hookDashboardData($params)
	{
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$visits = $unique_visitors = $online_visitor = 0;
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

		// Online visitors is only available with Analytics Real Time still in private beta at this time (October 18th, 2013).
		// if ($result = $gapi->requestReportData('', 'ga:activeVisitors', null, null, null, null, 1, 1))
			// $online_visitor = $result[0]['metrics']['activeVisitors'];
		if ($maintenance_ips = Configuration::get('PS_MAINTENANCE_IP'))
			$maintenance_ips = implode(',', array_map('ip2long', array_map('trim', explode(',', $maintenance_ips))));
		if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
			$sql = 'SELECT COUNT(DISTINCT c.id_connections)
					FROM `'._DB_PREFIX_.'connections` c
					LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
					WHERE TIME_TO_SEC(TIMEDIFF(NOW(), cp.`time_start`)) < '.((int)Configuration::get('DASHACTIVITY_VISITOR_ONLINE')*60).'
					AND cp.`time_end` IS NULL
					'.Shop::addSqlRestriction(false, 'c').'
					'.($maintenance_ips ? 'AND c.ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '');
		else
			$sql = 'SELECT COUNT(*)
					FROM `'._DB_PREFIX_.'connections`
					WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `date_add`)) < '.((int)Configuration::get('DASHACTIVITY_VISITOR_ONLINE')*60).'
					'.Shop::addSqlRestriction(false).'
					'.($maintenance_ips ? 'AND ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '');
		$online_visitor = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

		$pending_orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (o.current_state = os.id_order_state)
		WHERE os.paid = 1 AND os.shipped = 0
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));

		$abandoned_cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'cart`
		WHERE `date_upd` BETWEEN "'.pSQL(date('Y-m-d H:i:s', strtotime('-'.(int)Configuration::get('DASHACTIVITY_CART_ABANDONED_MAX').' MIN'))).'" AND "'.pSQL(date('Y-m-d H:i:s', strtotime('-'.(int)Configuration::get('DASHACTIVITY_CART_ABANDONED_MIN').' MIN'))).'"
		AND id_cart NOT IN (SELECT id_cart FROM `'._DB_PREFIX_.'orders`)
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		
		$return_exchanges = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_return` or2 ON o.id_order = or2.id_order
		WHERE or2.`date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));

		$products_out_of_stock = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT SUM(IF(IFNULL(stock.quantity, 0) > 0, 0, 1))
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
		'.Product::sqlStock('p', 'pa'));
		
		$new_messages = AdminStatsController::getPendingMessages();

		$active_shopping_cart = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'cart`
		WHERE date_upd > "'.pSQL(date('Y-m-d H:i:s', strtotime('-'.(int)Configuration::get('DASHACTIVITY_CART_ACTIVE').' MIN'))).'"
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
			$product_reviews += Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
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
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$websites = array();
			if ($result = $gapi->requestReportData('ga:source', 'ga:visitors', $date_from, $date_to, '-ga:visitors', null, 1, 3))
			foreach ($result as $row)
				$websites[$row['dimensions']['source']] = $row['metrics']['visitors'];
		}
		else
		{
			$directLink = $this->l('Direct link');
			$websites = array($directLink => 0);

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT http_referer
			FROM '._DB_PREFIX_.'connections
			WHERE date_add BETWEEN "'.$date_from.'" AND "'.$date_to.'"
			'.Shop::addSqlRestriction().'
			LIMIT '.(int)$limit);
			foreach ($result as $row)
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
		}
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
					'class' => 'btn btn-default submit_dash_config',
					'reset' => array(
						'title' => $this->l('Cancel'),
						'class' => 'btn btn-default cancel_dash_config',
					)
				)
			),
		);
			
		$fields_form['form']['input'][] = array(
			'label' => $this->l('Cart as active'),
			'desc' => $this->l('Default time range to consider a Shopping cart as active (default 30, max 120)'),
			'name' => 'DASHACTIVITY_CART_ACTIVE',
			'type' => 'select',
			'options' => array(
				'query' => array(
					array('id' => 15, 'name' => 15),
					array('id' => 30, 'name' => 30),
					array('id' => 45, 'name' => 45),
					array('id' => 60, 'name' => 60),
					array('id' => 90, 'name' => 90),
					array('id' => 120, 'name' => 120),
					),
				'id' => 'id',
				'name' => 'name',
			),
		);
		$fields_form['form']['input'][] = array(
			'label' => $this->l('Visitor online'),
			'desc' => $this->l('Default time range to consider a Visitor as online (default 30, max 120)'), 
			'name' => 'DASHACTIVITY_VISITOR_ONLINE',
			'type' => 'select',
			'options' => array(
				'query' => array(
					array('id' => 15, 'name' => 15),
					array('id' => 30, 'name' => 30),
					array('id' => 45, 'name' => 45),
					array('id' => 60, 'name' => 60),
					array('id' => 90, 'name' => 90),
					array('id' => 120, 'name' => 120),
					),
				'id' => 'id',
				'name' => 'name',
			),
		);
		$fields_form['form']['input'][] = array(
				'label' => $this->l('Cart abandoned (min)'),
				'desc' => $this->l('Default time range (min) to consider a Shopping cart as abandoned (default 24hrs)'),
				'name' => 'DASHACTIVITY_CART_ABANDONED',
				'type' => 'text',
				'suffix' => $this->l('hrs'),
				);
		$fields_form['form']['input'][] = array(
				'label' => $this->l('Cart abandoned (max)'),
				'desc' => $this->l('Default time range (max) to consider a Shopping cart as abandoned (default 48hrs)'),
				'name' => 'DASHACTIVITY_CART_ABANDONED',
				'type' => 'text',
				'suffix' => $this->l('hrs'),
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
			'DASHACTIVITY_CART_ACTIVE' => Tools::getValue('DASHACTIVITY_CART_ACTIVE', Configuration::get('DASHACTIVITY_CART_ACTIVE')),
			'DASHACTIVITY_CART_ABANDONED_MIN' => Tools::getValue('DASHACTIVITY_CART_ABANDONED_MIN', Configuration::get('DASHACTIVITY_CART_ABANDONED_MIN')),
			'DASHACTIVITY_CART_ABANDONED_MAX' => Tools::getValue('DASHACTIVITY_CART_ABANDONED_MAX', Configuration::get('DASHACTIVITY_CART_ABANDONED_MAX')),
			'DASHACTIVITY_VISITOR_ONLINE' => Tools::getValue('DASHACTIVITY_VISITOR_ONLINE', Configuration::get('DASHACTIVITY_VISITOR_ONLINE')),
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