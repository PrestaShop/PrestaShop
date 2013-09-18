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

class StatsLive extends Module
{
	private $html = '';

	public function __construct()
	{
		$this->name = 'statslive';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Visitors online');
		$this->description = $this->l('Display the list of customers and visitors currently online.');
	}

	public function install()
	{
		return parent::install() && $this->registerHook('AdminStatsModules');
	}

	/**
	 * Get the number of online customers
	 *
	 * @return array(array, int) array of online customers entries, number of online customers
	 */
	private function getCustomersOnline()
	{
		if ($maintenance_ips = Configuration::get('PS_MAINTENANCE_IP'))
			$maintenance_ips = implode(',', array_map('ip2long', array_map('trim', explode(',', $maintenance_ips))));
		
		if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
		{
			$sql = 'SELECT u.id_customer, u.firstname, u.lastname, pt.name as page
					FROM `'._DB_PREFIX_.'connections` c
					LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
					LEFT JOIN `'._DB_PREFIX_.'page` p ON p.id_page = cp.id_page
					LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON p.id_page_type = pt.id_page_type
					INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
					INNER JOIN `'._DB_PREFIX_.'customer` u ON u.id_customer = g.id_customer
					WHERE cp.`time_end` IS NULL
						'.Shop::addSqlRestriction(false, 'c').'
						AND TIME_TO_SEC(TIMEDIFF(NOW(), cp.`time_start`)) < 900
					'.($maintenance_ips ? 'AND c.ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '').'
					GROUP BY u.id_customer
					ORDER BY u.firstname, u.lastname';
		}
		else
		{
			$sql = 'SELECT u.id_customer, u.firstname, u.lastname, "-" as page
					FROM `'._DB_PREFIX_.'connections` c
					INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
					INNER JOIN `'._DB_PREFIX_.'customer` u ON u.id_customer = g.id_customer
					WHERE TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)) < 900
						'.Shop::addSqlRestriction(false, 'c').'
					'.($maintenance_ips ? 'AND c.ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '').'
					GROUP BY u.id_customer
					ORDER BY u.firstname, u.lastname';
		}
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		return array($results, Db::getInstance()->NumRows());
	}

	/**
	 * Get the number of online visitors
	 *
	 * @return array(array, int) array of online visitors entries, number of online visitors
	 */
	private function getVisitorsOnline()
	{
		if ($maintenance_ips = Configuration::get('PS_MAINTENANCE_IP'))
			$maintenance_ips = implode(',', array_map('ip2long', array_map('trim', explode(',', $maintenance_ips))));

		if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
		{
			$sql = 'SELECT c.id_guest, c.ip_address, c.date_add, c.http_referer, pt.name as page
					FROM `'._DB_PREFIX_.'connections` c
					LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
					LEFT JOIN `'._DB_PREFIX_.'page` p ON p.id_page = cp.id_page
					LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON p.id_page_type = pt.id_page_type
					INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
					WHERE (g.id_customer IS NULL OR g.id_customer = 0)
						'.Shop::addSqlRestriction(false, 'c').'
						AND cp.`time_end` IS NULL
					AND TIME_TO_SEC(TIMEDIFF(NOW(), cp.`time_start`)) < 900
					'.($maintenance_ips ? 'AND c.ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '').'
					GROUP BY c.id_connections
					ORDER BY c.date_add DESC';
		}
		else
		{
			$sql = 'SELECT c.id_guest, c.ip_address, c.date_add, c.http_referer, "-" as page
					FROM `'._DB_PREFIX_.'connections` c
					INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
					WHERE (g.id_customer IS NULL OR g.id_customer = 0)
						'.Shop::addSqlRestriction(false, 'c').'
						AND TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)) < 900
					'.($maintenance_ips ? 'AND c.ip_address NOT IN ('.preg_replace('/[^,0-9]/', '', $maintenance_ips).')' : '').'
					ORDER BY c.date_add DESC';
		}

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		return array($results, Db::getInstance()->NumRows());
	}

	public function hookAdminStatsModules($params)
	{
		list($customers, $totalCustomers) = $this->getCustomersOnline();
		list($visitors, $totalVisitors) = $this->getVisitorsOnline();
		$irow = 0;

		$this->html .= '<script type="text/javascript" language="javascript">
			$("#calendar").remove();
		</script>';
		if (!Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
			$this->html .= '
				<div class="alert alert-info">'.
					$this->l('You must activate the option "pages viewed for each customer" in the "Stats data-mining" module in order to see the pages viewed by your customers.').'
				</div>';
		$this->html .= '
			<h4> '.$this->l('Customers online').'</h4>';
		if ($totalCustomers)
		{
			$this->html .= $this->l('Total:').' '.(int)$totalCustomers.'
			<table class="table">
				<thead>
					<tr>
						<th class="center"><span class="title_box active">'.$this->l('ID').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('Name').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('Current Page').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('View').'</span></th>
					</tr>
				</thead>
				<tbody>';
				foreach ($customers as $customer)
					$this->html .= '
					<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
						<td class="center">'.$customer['id_customer'].'</td>
						<td class="center">'.$customer['firstname'].' '.$customer['lastname'].'</td>
						<td class="center">'.$customer['page'].'</td>
						<td class="center">
							<a href="index.php?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id).'"
								target="_blank">
								<img src="../modules/'.$this->name.'/logo.gif" />
							</a>
						</td>
					</tr>';
				$this->html .= '
				</tbody>
			</table>';
		}
		else
			$this->html .= '<p class="alert alert-warning">'.$this->l('Currently, there are no customers online.').'</p>
			<h4> '.$this->l('Visitors online').'</h4>';
		if ($totalVisitors)
		{
			$this->html .= $this->l('Total:').' '.(int)($totalVisitors).'
			<div>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Guest').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('IP').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Since').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Current page').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Referrer').'</span></th>
						</tr>
					</thead>
					<tbody>';
				foreach ($visitors as $visitor)
					$this->html .= '<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
						<td class="center">'.$visitor['id_guest'].'</td>
						<td class="center">'.long2ip($visitor['ip_address']).'</td>
						<td class="center">'.substr($visitor['date_add'], 11).'</td>
						<td class="center">'.(isset($visitor['page']) ? $visitor['page'] : $this->l('Undefined')).'</td>
						<td class="center">'.(empty($visitor['http_referer']) ? $this->l('None') : parse_url($visitor['http_referer'], PHP_URL_HOST)).'</td>
					</tr>';
				$this->html .= '
					</tbody>
				</table>
			</div>';
		}
		else
			$this->html .= '<p class="alert alert-warning">'.$this->l('There are no visitors online.').'</p>
			<h4>'.$this->l('Notice').'</h4>
			<p class="alert alert-info">'.$this->l('Maintenance IP(s) are excluded from the online visitors.').'</p>
			<a class="btn btn-default" href="index.php?controller=AdminMaintenance&token='.Tools::getAdminTokenLite('AdminMaintenance').'">
				<i class="icon-share-alt"></i> '.$this->l('Add or remove an IP address.').'
			</a>
		';

		return $this->html;
	}
}


