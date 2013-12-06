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

class Dashgoals extends Module
{
	protected static $month_labels = array();
	protected static $types = array('traffic', 'conversion', 'avg_cart_value');

	public function __construct()
	{
		$this->name = 'dashgoals';
		$this->displayName = 'Dashboard Goals';
		$this->tab = '';
		$this->version = '0.1';
		$this->author = 'PrestaShop';

		parent::__construct();
		
		Dashgoals::$month_labels = array(
			'01' => $this->l('January'),
			'02' => $this->l('February'),
			'03' => $this->l('March'),
			'04' => $this->l('April'),
			'05' => $this->l('May'),
			'06' => $this->l('June'),
			'07' => $this->l('July'),
			'08' => $this->l('August'),
			'09' => $this->l('September'),
			'10' => $this->l('October'),
			'11' => $this->l('November'),
			'12' => $this->l('December')
		);
	}

	public function install()
	{
		Configuration::updateValue('PS_DASHGOALS_CURRENT_YEAR', date('Y'));
		for ($month = '01'; $month <= 12; $month = sprintf('%02d', $month + 1))
		{
			$key = strtoupper('dashgoals_traffic_'.$month.'_'.date('Y'));
			if (!ConfigurationKPI::get($key))
				ConfigurationKPI::updateValue($key, 600);
			$key = strtoupper('dashgoals_conversion_'.$month.'_'.date('Y'));
			if (!ConfigurationKPI::get($key))
				ConfigurationKPI::updateValue($key, 2);
			$key = strtoupper('dashgoals_avg_cart_value_'.$month.'_'.date('Y'));
			if (!ConfigurationKPI::get($key))
				ConfigurationKPI::updateValue($key, 80);
		}

		// Prepare tab
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = "AdminDashgoals";
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'Dashgoals';
		$tab->id_parent = -1;
		$tab->module = $this->name;

		return (
			$tab->add()
			&& parent::install()
			&& $this->registerHook('dashboardZoneTwo')
			&& $this->registerHook('dashboardData')
			&& $this->registerHook('actionAdminControllerSetMedia')
		);
	}

	public function uninstall()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminDashgoals');
		if ($id_tab)
		{
			$tab = new Tab($id_tab);
			$tab->delete();
		}
		return parent::uninstall();
	}
	
	public function hookActionAdminControllerSetMedia()
	{
		if (get_class($this->context->controller) == 'AdminDashboardController')
			$this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
	}
	
	public function setMonths($year)
	{
		$months = array();
		for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1))
			$months[$i.'_'.$year] = array('label' => Dashgoals::$month_labels[$i], 'values' => array());

		foreach (Dashgoals::$types as $type)
			foreach ($months as $month => &$month_row)
			{
				$key = 'dashgoals_'.$type.'_'.$month;
				if (Tools::isSubmit('submitDashGoals'))
					ConfigurationKPI::updateValue(strtoupper($key), (float)Tools::getValue($key));
				$month_row['values'][$type] = ConfigurationKPI::get(strtoupper($key));
			}
		return $months;
	}

	public function hookDashboardZoneTwo($params)
	{
		$year = Configuration::get('PS_DASHGOALS_CURRENT_YEAR');
		$months = $this->setMonths($year);

		$this->context->smarty->assign(array(
			'currency' => $this->context->currency,
			'goals_year' => $year,
			'goals_months' => $months,
			'dashgoals_ajax_link' => $this->context->link->getAdminLink('AdminDashgoals')
		));
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}

	public function hookDashboardData($params)
	{
		$year = ((isset($params['extra']) && $params['extra'] > 1970 && $params['extra'] < 2999) ? $params['extra'] : Configuration::get('PS_DASHGOALS_CURRENT_YEAR'));
		return array('data_chart' => array('dash_goals_chart1' => $this->getChartData($year)));
	}
	
	public function getChartData($year)
	{
		// Retrieve gross data from AdminStatsController
		$visits = AdminStatsController::getVisits(false, $year.date('-01-01'), $year.date('-12-31'), 'month');
		$orders = AdminStatsController::getOrders($year.date('-01-01'), $year.date('-12-31'), 'month');
		$sales = AdminStatsController::getTotalSales($year.date('-01-01'), $year.date('-12-31'), 'month');

		// There are stream types (different charts) and for each types there are 3 available zones (one color for the goal, one if you over perform and one if you under perfom) 
		$stream_types = array('traffic', 'conversion', 'avg_cart_value', 'sales');
		$stream_zones = array('real', 'more', 'less');

		// We initialize all the streams types for all the zones
		$streams = array();
		$average_goals = array();
		foreach ($stream_types as $stream_type)
		{
			$streams[$stream_type] = array();
			foreach ($stream_zones as $stream_zone)
				$streams[$stream_type][$stream_zone] = array(
					'key' => $stream_type.'_'.$stream_zone,
					'color' => ($stream_zone == 'more' ? '#333333' : ($stream_zone == 'less' ? '#DDDDDD' : '#999999')),
					'values' => array(),
					'disabled' => $stream_type == 'sales' ? false : true
				);

			$average_goals[$stream_type] = 0;
		}

		// We need to calculate the average value of each goals for the year, this will be the base rate for "100%"
		for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1))
		{
			$average_goals['traffic'] += ConfigurationKPI::get('DASHGOALS_TRAFFIC_'.$i.'_'.$year);
			$average_goals['conversion'] += ConfigurationKPI::get('DASHGOALS_CONVERSION_'.$i.'_'.$year) / 100;
			$average_goals['avg_cart_value'] += ConfigurationKPI::get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);
		}
		foreach ($average_goals as &$average_goal)
			$average_goal /= 12;
		$average_goals['sales'] = $average_goals['traffic'] * $average_goals['conversion'] * $average_goals['avg_cart_value'];

		// Now we can calculate the value for every months
		for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1))
		{
			$timestamp = strtotime($year.'-'.$i.'-01');

			$month_goal = ConfigurationKPI::get('DASHGOALS_TRAFFIC_'.$i.'_'.$year);
			$value = (isset($visits[$timestamp]) ? $visits[$timestamp] : 0);
			$stream_values = $this->getValuesFromGoals($average_goals['traffic'], $month_goal, $value, Dashgoals::$month_labels[$i]);
			foreach ($stream_zones as $stream_zone)
				$streams['traffic'][$stream_zone]['values'][] = $stream_values[$stream_zone];

			$month_goal = ConfigurationKPI::get('DASHGOALS_CONVERSION_'.$i.'_'.$year);
			$value = 100 * ((isset($visits[$timestamp]) && $visits[$timestamp]) ? ($orders[$timestamp] / $visits[$timestamp]) : 0);
			$stream_values = $this->getValuesFromGoals($average_goals['conversion'], $month_goal, $value, Dashgoals::$month_labels[$i]);
			foreach ($stream_zones as $stream_zone)
				$streams['conversion'][$stream_zone]['values'][] = $stream_values[$stream_zone];

			$month_goal = ConfigurationKPI::get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);
			$value = ((isset($orders[$timestamp]) && $orders[$timestamp]) ? ($sales[$timestamp] / $orders[$timestamp]) : 0);
			$stream_values = $this->getValuesFromGoals($average_goals['avg_cart_value'], $month_goal, $value, Dashgoals::$month_labels[$i]);
			foreach ($stream_zones as $stream_zone)
				$streams['avg_cart_value'][$stream_zone]['values'][] = $stream_values[$stream_zone];

			$month_goal = ConfigurationKPI::get('DASHGOALS_TRAFFIC_'.$i.'_'.$year) * ConfigurationKPI::get('DASHGOALS_CONVERSION_'.$i.'_'.$year) / 100 * ConfigurationKPI::get('DASHGOALS_AVG_CART_VALUE_'.$i.'_'.$year);
			$stream_values = $this->getValuesFromGoals($average_goals['sales'], $month_goal, isset($sales[$timestamp]) ? $sales[$timestamp] : 0, Dashgoals::$month_labels[$i]);
			foreach ($stream_zones as $stream_zone)
				$streams['sales'][$stream_zone]['values'][] = $stream_values[$stream_zone];
		}

		// Merge all the streams before sending
		$all_streams = array();
		foreach ($stream_types as $stream_type)
			foreach ($stream_zones as $stream_zone)
				$all_streams[] = $streams[$stream_type][$stream_zone];

		return array('chart_type' => 'bar_chart_goals', 'data' => $all_streams);
	}
	
	protected function getValuesFromGoals($average_goal, $month_goal, $value, $label)
	{
		// Initialize value for each zone
		$stream_values = array('real' => array('x' => $label, 'y' => 0), 'less' => array('x' => $label, 'y' => 0), 'more' => array('x' => $label, 'y' => 0));
		
		// Calculate the percentage of fullfilment of the goal
		$fullfilment = 0;
		if ($value && $month_goal)
			$fullfilment = round($value / $month_goal, 2);

		// Base rate is essential here : it determines the value of the goal compared to the "100%" of the chart legend
		$base_rate = 0;
		if ($average_goal && $month_goal)
			$base_rate = $month_goal / $average_goal;

		// Fullfilment of 1 means that we performed exactly anticipated
		if ($fullfilment == 1)
			$stream_values['real'] = array('x' => $label, 'y' => $base_rate);
		// Fullfilment lower than 1 means that we UNDER performed
		elseif ($fullfilment < 1) // 
		{
			$stream_values['real'] = array('x' => $label, 'y' => $fullfilment * $base_rate);
			$stream_values['less'] = array('x' => $label, 'y' => $base_rate - ($fullfilment * $base_rate));
		}
		// Fullfilment greater than 1 means that we OVER performed
		elseif ($fullfilment > 1)
		{
			$stream_values['real'] = array('x' => $label, 'y' => $base_rate);
			$stream_values['more'] = array('x' => $label, 'y' => ($fullfilment * $base_rate) - $base_rate);
		}

		return $stream_values;
	}
}