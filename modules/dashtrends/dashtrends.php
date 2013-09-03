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
		if (!parent::install() || !$this->registerHook('dashboardZoneTwo') || !$this->registerHook('dashboardData'))
			return false;
		return true;
	}

	public function hookDashboardZoneTwo($params)
	{
		return $this->display(__FILE__, 'dashboard_zone_two.tpl');
	}
	
	public function hookDashboardData($params)
	{
		$gapi = Module::isInstalled('gapi') ? Module::getInstanceByName('gapi') : false;
		if (Validate::isLoadedObject($gapi) && $gapi->isConfigured())
		{
			$visits_score = 0;
			if ($result = $gapi->requestReportData('', 'ga:visits', $params['date_from'], $params['date_to'], null, null, 1, 1))
				$visits_score = $result[0]['metrics']['visits'];
		}
		else
		{
			$visits_score = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(`id_connections`)
			FROM `'._DB_PREFIX_.'connections`
			WHERE `date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
			'.Shop::addSqlRestriction(false));
		}
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(`id_order`) as orders_score, SUM(`total_paid_tax_excl` / `conversion_rate`) as sales_score
		FROM `'._DB_PREFIX_.'orders`
		WHERE `invoice_date` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER));
		extract($row);

		return array(
			'data_value' => array(
				'sales_score' => Tools::displayPrice((float)$sales_score),
				'orders_score' => $orders_score,
				'cart_value_score' => Tools::displayPrice($orders_score ? $sales_score / $orders_score : 0),
				'visits_score' => $visits_score,
				'convertion_rate_score' => $visits_score ? 100 * $orders_score / $visits_score : 0,
				'net_profits_score' => Tools::displayPrice(0),
			),
			'data_trends' => array(
				'sales_score_trends' => array('way' => 'up', 'value' => 0.42),
				'orders_score_trends' => array('way' => 'down', 'value' => 0.42),
				'cart_value_score_trends' => array('way' => 'up', 'value' => 0.42),
				'visits_score_trends' => array('way' => 'down', 'value' => 0.42),
				'convertion_rate_score_trends' => array('way' => 'up', 'value' => 0.42),
				'net_profits_score_trends' => array('way' => 'up', 'value' => 0.42)
			)
		);
	}
}