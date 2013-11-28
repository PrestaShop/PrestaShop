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
			&& $this->registerHook('displayBackOfficeHeader')
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
	
	public function hookDisplayBackOfficeHeader()
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
		$visits = AdminStatsController::getVisits(false, $year.date('-01-01'), $year.date('-12-31'), 'month');
		$orders = AdminStatsController::getOrders($year.date('-01-01'), $year.date('-12-31'), 'month');
		$sales = AdminStatsController::getTotalSales($year.date('-01-01'), $year.date('-12-31'), 'month');

		$stream1 = array('key' => $this->l('Traffic'), 'values' => array(), 'disabled' => true);
		$stream2 = array('key' => $this->l('Conversion Rate'), 'values' => array());
		$stream3 = array('key' => $this->l('Average Cart Value'), 'values' => array());
		$stream4 = array('key' => $this->l('Sales'), 'values' => array());

		for ($i = '01'; $i <= 12; $i = sprintf('%02d', $i + 1))
		{
			$timestamp = strtotime($year.'-'.$i.'-01');
			
			$goal = ConfigurationKPI::get(strtoupper('dashgoals_traffic_'.$i.'_'.$year));
			$value = 0;
			if ($goal && isset($visits[$timestamp]))
				$value = round($visits[$timestamp] / $goal, 2);
			$stream1['values'][] = array('x' => Dashgoals::$month_labels[$i], 'y' => $value);

			$goal = ConfigurationKPI::get(strtoupper('dashgoals_conversion_'.$i.'_'.$year));
			$value = 0;
			if ($goal && isset($visits[$timestamp]) && $visits[$timestamp] && isset($orders[$timestamp]) && $orders[$timestamp])
				$value = round((100 * $orders[$timestamp] / $visits[$timestamp]) / $goal, 2);
			$stream2['values'][] = array('x' => Dashgoals::$month_labels[$i], 'y' => $value);
			
			$goal = ConfigurationKPI::get(strtoupper('dashgoals_avg_cart_value_'.$i.'_'.$year));
			$value = 0;
			if ($goal && isset($orders[$timestamp]) && $orders[$timestamp] && isset($sales[$timestamp]) && $sales[$timestamp])
				$value = round(($sales[$timestamp] / $orders[$timestamp]) / $goal, 2);
			$stream3['values'][] = array('x' => Dashgoals::$month_labels[$i], 'y' => $value);
			
			$goal = ConfigurationKPI::get(strtoupper('dashgoals_traffic_'.$i.'_'.$year))
			* ConfigurationKPI::get(strtoupper('dashgoals_conversion_'.$i.'_'.$year)) / 100
			* ConfigurationKPI::get(strtoupper('dashgoals_avg_cart_value_'.$i.'_'.$year));
			$value = 0;
			if ($goal && isset($sales[$timestamp]) && $sales[$timestamp])
				$value = round($sales[$timestamp] / $goal, 2);
			$stream4['values'][] = array('x' => Dashgoals::$month_labels[$i], 'y' => $value);
		}
		return array('chart_type' => 'bar_chart_goals', 'data' => array($stream1, $stream2, $stream3, $stream4));
	}
}