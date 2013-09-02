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
		if (!parent::install() || !$this->registerHook('dashboardZoneTwo') || !$this->registerHook('dashboardDatas'))
			return false;
		return true;
	}

	public function hookDashboardZoneTwo($params)
	{
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}
	
	public function hookDashboardDatas($params)
	{
		return array(
			'data_value' => array(
				'sales_score' => Tools::displayPrice(151.365),
				'orders_score' => 120,
				'cart_value_score' => Tools::displayPrice(35),
				'visits_score' => 12,
				'convertion_rate_score' => 4,
				'net_profits_score' => Tools::displayPrice(42),
				),
			'data_trends' => array(
				'sales_score_trends' => array('way' => 'up', 'value' => 0.66),
				'orders_score_trends' => array('way' => 'down', 'value' => 0.66),
				'cart_value_score_trends' => array('way' => 'up', 'value' => 0.66),
				'visits_score_trends' => array('way' => 'down', 'value' => 0.66),
				'convertion_rate_score_trends' => array('way' => 'up', 'value' => 0.66),
				'net_profits_score_trends' => array('way' => 'up', 'value' => 0.66)
				)
			);
	}
}