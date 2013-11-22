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

class Dashtrends extends Module
{
	protected $dashboard_data;
	protected $dashboard_data_compare;
	protected $dashboard_data_sum;
	protected $dashboard_data_sum_compare;
	protected $data_trends;

	public function __construct()
	{
		$this->name = 'dashtrends';
		$this->displayName = 'Dashboard Trends';
		$this->tab = '';
		$this->version = '0.1';
		$this->author = 'PrestaShop';

		parent::__construct();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('dashboardZoneTwo') || !$this->registerHook('dashboardData') || !$this->registerHook('displayBackOfficeHeader'))
			return false;
		return true;
	}
	
	public function hookDisplayBackOfficeHeader()
	{
		if (get_class($this->context->controller) == 'AdminDashboardController')
			$this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
	}

	public function hookDashboardZoneTwo($params)
	{
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}

	protected function getData($date_from, $date_to)
	{
		// We need the following figures to calculate our stats
		$tmp_data = array(
			'visits' => array(),
			'orders' => array(),
			'total_paid_tax_excl' => array(),
			'total_purchases' => array(),
			'total_expenses' => array()
		);

		if (Configuration::get('PS_DASHBOARD_SIMULATION'))
		{
			$from = strtotime($date_from.' 00:00:00');
			$to = min(time(), strtotime($date_to.' 23:59:59'));
			for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
			{
				$tmp_data['visits'][$date] = round(rand(2000, 20000));
				$tmp_data['conversion_rate'][$date] = rand(80, 250) / 100;
				$tmp_data['average_cart_value'][$date] = round(rand(60, 200), 2);
				$tmp_data['orders'][$date] = round($tmp_data['visits'][$date] * $tmp_data['conversion_rate'][$date] / 100);
				$tmp_data['total_paid_tax_excl'][$date] = $tmp_data['orders'][$date] * $tmp_data['average_cart_value'][$date];
				$tmp_data['total_purchases'][$date] = $tmp_data['total_paid_tax_excl'][$date] * rand(50, 70) / 100;
				$tmp_data['total_expenses'][$date] = $tmp_data['total_paid_tax_excl'][$date] * rand(0, 10) / 100;
			}
		}
		else
		{
			$tmp_data['visits'] = AdminStatsControllerCore::getVisits(false, $date_from, $date_to, 'day');
			$tmp_data['orders'] = AdminStatsControllerCore::getOrders($date_from, $date_to, 'day');
			$tmp_data['total_paid_tax_excl'] = AdminStatsControllerCore::getTotalSales($date_from, $date_to, 'day');
			$tmp_data['total_purchases'] = AdminStatsControllerCore::getPurchases($date_from, $date_to, 'day');
			$tmp_data['total_expenses'] = AdminStatsControllerCore::getExpenses($date_from, $date_to, 'day');
		}

		return $tmp_data;
	}

	protected function refineData($date_from, $date_to, $gross_data)
	{
		$refined_data = array(
			'sales' => array(),
			'orders' => array(),
			'average_cart_value' => array(),
			'visits' => array(),
			'conversion_rate' => array(),
			'net_profits' => array()
		);

		$from = strtotime($date_from.' 00:00:00');
		if (!Configuration::get('PS_DASHBOARD_SIMULATION'))
			$from = max(strtotime(_PS_CREATION_DATE_.' 00:00:00'), $from);
		$to = min(time(), strtotime($date_to.' 23:59:59'));
		for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date))
		{
			$refined_data['sales'][$date] = 0;
			if (isset($gross_data['total_paid_tax_excl'][$date]))
				$refined_data['sales'][$date] += $gross_data['total_paid_tax_excl'][$date];
				
			$refined_data['orders'][$date] = isset($gross_data['orders'][$date]) ? $gross_data['orders'][$date] : 0;
			
			$refined_data['average_cart_value'][$date] = $refined_data['orders'][$date] ? $refined_data['sales'][$date] / $refined_data['orders'][$date] : 0;
			
			$refined_data['visits'][$date] = isset($gross_data['visits'][$date]) ? $gross_data['visits'][$date] : 0;
			
			$refined_data['conversion_rate'][$date] = $refined_data['visits'][$date] ? $refined_data['orders'][$date] / $refined_data['visits'][$date] : 0;

			$refined_data['net_profits'][$date] = 0;
			if (isset($gross_data['total_paid_tax_excl'][$date]))
				$refined_data['net_profits'][$date] += $gross_data['total_paid_tax_excl'][$date];
			if (isset($gross_data['total_purchases'][$date]))
				$refined_data['net_profits'][$date] -= $gross_data['total_purchases'][$date];
			if (isset($gross_data['total_expenses'][$date]))
				$refined_data['net_profits'][$date] -= $gross_data['total_expenses'][$date];
		}
		return $refined_data;
	}
	
	protected function addupData($data)
	{
		$summing = array(
			'sales' => 0,
			'orders' => 0,
			'average_cart_value' => 0,
			'visits' => 0,
			'conversion_rate' => 0,
			'net_profits' => 0
		);

		$summing['sales'] = array_sum($data['sales']);
		$summing['orders'] = array_sum($data['orders']);
		$summing['average_cart_value'] = $summing['sales'] ? $summing['sales'] / $summing['orders'] : 0;
		$summing['visits'] = array_sum($data['visits']);
		$summing['conversion_rate'] = $summing['visits'] ? $summing['orders'] / $summing['visits'] : 0;
		$summing['net_profits'] = array_sum($data['net_profits']);
		return $summing;
	}

	protected function compareData($data1, $data2)
	{
		return array(
			'sales_score_trends' => array(
				'way' => ($data1['sales'] == $data2['sales'] ? 'right' : ($data1['sales'] > $data2['sales'] ? 'up' : 'down')),
				'value' => ($data1['sales'] > $data2['sales'] ? '+' : '').($data2['sales'] ? round(100 * $data1['sales'] / $data2['sales'] - 100, 2).'%' : '&infin;')
			),
			'orders_score_trends' => array(
				'way' => ($data1['orders'] == $data2['orders'] ? 'right' : ($data1['orders'] > $data2['orders'] ? 'up' : 'down')),
				'value' => ($data1['orders'] > $data2['orders'] ? '+' : '').($data2['orders'] ? round(100 * $data1['orders'] / $data2['orders'] - 100, 2).'%' : '&infin;')
			),
			'cart_value_score_trends' => array(
				'way' => ($data1['average_cart_value'] == $data2['average_cart_value'] ? 'right' : ($data1['average_cart_value'] > $data2['average_cart_value'] ? 'up' : 'down')),
				'value' => ($data1['average_cart_value'] > $data2['average_cart_value'] ? '+' : '').($data2['average_cart_value'] ? round(100 * $data1['average_cart_value'] / $data2['average_cart_value'] - 100, 2).'%' : '&infin;')
			),
			'visits_score_trends' => array(
				'way' => ($data1['visits'] == $data2['visits'] ? 'right' : ($data1['visits'] > $data2['visits'] ? 'up' : 'down')),
				'value' => ($data1['visits'] > $data2['visits'] ? '+' : '').($data2['visits'] ? round(100 * $data1['visits'] / $data2['visits'] - 100, 2).'%' : '&infin;')
			),
			'conversion_rate_score_trends' => array(
				'way' => ($data1['conversion_rate'] == $data2['conversion_rate'] ? 'right' : ($data1['conversion_rate'] > $data2['conversion_rate'] ? 'up' : 'down')),
				'value' => ($data1['conversion_rate'] > $data2['conversion_rate'] ? '+' : '').($data2['conversion_rate'] ? round($data1['visits'] - $data2['visits'], 2).$this->l('pts') : '&infin;')
			),
			'net_profits_score_trends' => array(
				'way' => ($data1['net_profits'] == $data2['net_profits'] ? 'right' : ($data1['net_profits'] > $data2['net_profits'] ? 'up' : 'down')),
				'value' => ($data1['net_profits'] > $data2['net_profits'] ? '+' : '').($data2['net_profits'] ? round(100 * $data1['net_profits'] / $data2['net_profits'] - 100, 2).'%' : '&infin;')
			)
		);
	}
	
	public function hookDashboardData($params)
	{
		// Artificially remove the decimals in order to get a cleaner Dashboard
		$currency = clone $this->context->currency;
		$currency->decimals = 0;

		// Retrieve, refine and add up data for the selected period
		$tmp_data = $this->getData($params['date_from'], $params['date_to']);
		$this->dashboard_data = $this->refineData($params['date_from'], $params['date_to'], $tmp_data);
		$this->dashboard_data_sum = $this->addupData($this->dashboard_data);

		if ($params['compare_from'] && $params['compare_from'] != '0000-00-00')
		{
			// Retrieve, refine and add up data for the comparison period
			$tmp_data_compare = $this->getData($params['compare_from'], $params['compare_to']);
			$this->dashboard_data_compare = $this->refineData($params['compare_from'], $params['compare_to'], $tmp_data_compare);
			$this->dashboard_data_sum_compare = $this->addupData($this->dashboard_data_compare);
			
			$this->data_trends = $this->compareData($this->dashboard_data_sum, $this->dashboard_data_sum_compare);
			$this->dashboard_data_compare = $this->translateCompareData($this->dashboard_data, $this->dashboard_data_compare);
		}

		return array(
			'data_value' => array(
				'sales_score' => Tools::displayPrice(round($this->dashboard_data_sum['sales']), $currency),
				'orders_score' => $this->dashboard_data_sum['orders'],
				'cart_value_score' => Tools::displayPrice($this->dashboard_data_sum['average_cart_value'], $currency),
				'visits_score' => $this->dashboard_data_sum['visits'],
				'conversion_rate_score' => round(100 * $this->dashboard_data_sum['conversion_rate'], 2).'%',
				'net_profits_score' => Tools::displayPrice(round($this->dashboard_data_sum['net_profits']), $currency),
			),
			'data_trends' => $this->data_trends,
			'data_chart' => array('dash_trends_chart1' => $this->getChartTrends()),
		);
	}
	
	protected function translateCompareData($normal, $compare)
	{
		$translated_array = array();
		foreach ($compare as $key => $date_array)
		{
			$normal_min = key($normal[$key]);
			end($normal[$key]); // move the internal pointer to the end of the array
			$normal_max = key($normal[$key]);
			reset($normal[$key]);
			$normal_size = $normal_max - $normal_min;
			
			$compare_min = key($compare[$key]);
			end($compare[$key]); // move the internal pointer to the end of the array
			$compare_max = key($compare[$key]);
			reset($compare[$key]);
			$compare_size = $compare_max - $compare_min;

			$translated_array[$key] = array();
			foreach ($date_array as $compare_date => $value)
			{
				$translation = $normal_min + ($compare_date - $compare_min) * ($normal_size / $compare_size);
				$translated_array[$key][number_format($translation, 0, '', '')] = $value;
			}
		}
		return $translated_array;
	}
	
	public function getChartTrends()
	{
		$chart_data = array();
		foreach (array_keys($this->dashboard_data) as $chart_key)
		{
			$chart_data[$chart_key] = $chart_data_compare[$chart_key] = array();

			if (!$count = count($this->dashboard_data[$chart_key]))
				continue;

			// We calibrate 100% to the mean
			$calibration = array_sum($this->dashboard_data[$chart_key]) / $count;

			foreach ($this->dashboard_data[$chart_key] as $key => $value)
				// min(10) is there to limit the growth to 1000%, beyond this limit it becomes unreadable
				$chart_data[$chart_key][] = array(1000 * $key, $calibration ? min(10, $value / $calibration) : 0);

			if ($this->dashboard_data_compare)
				foreach ($this->dashboard_data_compare[$chart_key] as $key => $value)
					// min(10) is there to limit the growth to 1000%, beyond this limit it becomes unreadable
					$chart_data_compare[$chart_key][] = array(1000 * $key, $calibration ? min(10, $value / $calibration) : 0);
		}
		
		$charts = array(
			'sales' => $this->l('Sales'),
			'orders' => $this->l('Orders'),
			'average_cart_value' => $this->l('Average Cart Value'),
			'visits' => $this->l('Visits'),
			'conversion_rate' => $this->l('Conversion Rate'),
			'net_profits' => $this->l('Net Profits')
		);
		
		$data = array('chart_type' => 'line_chart_trends', 'data' => array());
		foreach ($charts as $key => $title)
		{
			$data['data'][] = array('id' => $key, 'key' => $title, 'values' => $chart_data[$key], 'disabled' => ($key == 'sales' ? false : true));
			if ($this->dashboard_data_compare)
				$data['data'][] = array('id' => $key.'_compare', 'key' => sprintf($this->l('%s (previous period)'), $title), 'values' => $chart_data_compare[$key], 'disabled' => ($key == 'sales' ? false : true));
		}
		return $data;
	}
}