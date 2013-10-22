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
	public function __construct()
	{
		$this->name = 'dashgoals';
		$this->displayName = 'Dashboard Goals';
		$this->tab = '';
		$this->version = '0.1';
		$this->author = 'PrestaShop';

		parent::__construct();
	}

	public function install()
	{
		return (
			parent::install()
			&& $this->registerHook('dashboardZoneTwo')
			&& $this->registerHook('dashboardData')
			&& $this->registerHook('displayBackOfficeHeader')
		);
	}
	
	public function hookDisplayBackOfficeHeader()
	{
		if (get_class($this->context->controller) == 'AdminDashboardController')
			$this->context->controller->addJs($this->_path.'views/js/'.$this->name.'.js');
	}

	public function hookDashboardZoneTwo($params)
	{
		$year = date('Y');
		$this->context->smarty->assign('goals_months', array(
			'01_'.$year => sprintf($this->l('January, %s'), $year),
			'02_'.$year => sprintf($this->l('February, %s'), $year),
			'03_'.$year => sprintf($this->l('March, %s'), $year),
			'04_'.$year => sprintf($this->l('April, %s'), $year),
			'05_'.$year => sprintf($this->l('May, %s'), $year),
			'06_'.$year => sprintf($this->l('June, %s'), $year),
			'07_'.$year => sprintf($this->l('July, %s'), $year),
			'08_'.$year => sprintf($this->l('August, %s'), $year),
			'09_'.$year => sprintf($this->l('September, %s'), $year),
			'10_'.$year => sprintf($this->l('October, %s'), $year),
			'11_'.$year => sprintf($this->l('November, %s'), $year),
			'12_'.$year => sprintf($this->l('December, %s'), $year),
		));
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}

	public function hookDashboardData($params)
	{
		return array();
			// 'data_chart' => array('dash_goals_chart1' => $this->getChartData()),
		// );
	}
	
	public function getChartData()
	{
		return array();
	}

	public function renderConfigForm()
	{

	}
}