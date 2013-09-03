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
		
		parent::__construct();
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('dashboardZoneOne') || !$this->registerHook('dashboardData'))
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
		if (Validate::isLoadedObject($gapi))
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
			SELECT COUNT(`id_connections`) as visits, COUNT(DISTINCT `id_guest`) as unique_visitors
			FROM `'._DB_PREFIX_.'connections`
			WHERE `date_add` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
			'.Shop::addSqlRestriction(false));
			extract($row);
		}
		
		$order_nbr = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(o.`id_order`)
		FROM `'._DB_PREFIX_.'orders` o
		WHERE o.`invoice_date` BETWEEN "'.pSQL($params['date_from']).'" AND "'.pSQL($params['date_to']).'"
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o'));

		return array(
			'data_value' => array(
				'order_nbr' => $order_nbr,
				'pending_orders' => 120,
				'return_exchanges' => 35,
				'abandoned_cart' => 12,
				'products_out_of_stock' => 4,
				'new_messages' => 42,
				'order_inquires' => 13,
				'product_reviews' => 56,
				'new_customers' => 42,
				'online_visitor' => 200,
				'new_registrations' => 125,
				'total_suscribers' => 13500,
				'visits' => $visits,
				'unique_visitors' => $unique_visitors,
			),
			'data_trends' => array(
				'orders_trends' => array('way' => 'down', 'value' => 0.42),
			)
		);
	}
}