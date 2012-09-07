<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7307 $
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
				GROUP BY c.id_connections
				ORDER BY u.firstname, u.lastname';
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
					GROUP BY c.id_connections
					ORDER BY c.date_add DESC';
		}
		else
		{
			$sql = 'SELECT c.id_guest, c.ip_address, c.date_add, c.http_referer
					FROM `'._DB_PREFIX_.'connections` c
					INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
					WHERE (g.id_customer IS NULL OR g.id_customer = 0)
						'.Shop::addSqlRestriction(false, 'c').'
						AND TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)) < 900
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
			$this->html .= '<div class="warn">'.
				$this->l('You must activate the option "pages views for each customer" in the "Stats datamining" module in order to see the pages currently viewed by your customers.').'
			</div>';
		$this->html .= '
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Customers online').'</legend>';
		if ($totalCustomers)
		{
			$this->html .= $this->l('Total:').' '.(int)$totalCustomers.'
			<table cellpadding="0" cellspacing="0" class="table space">
				<tr><th>'.$this->l('ID').'</th><th>'.$this->l('Name').'</th><th>'.$this->l('Current Page').'</th><th>'.$this->l('View').'</th></tr>';
			foreach ($customers as $customer)
				$this->html .= '
				<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
					<td>'.$customer['id_customer'].'</td>
					<td style="width: 200px;">'.$customer['firstname'].' '.$customer['lastname'].'</td>
					<td style="width: 200px;">'.$customer['page'].'</td>
					<td style="text-align: right; width: 25px;">
						<a href="index.php?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id).'"
							target="_blank">
							<img src="../modules/'.$this->name.'/logo.gif" />
						</a>
					</td>
				</tr>';
			$this->html .= '</table>';
		}
		else
			$this->html .= $this->l('There are no customers online.');
		$this->html .= '</fieldset>
		<br />
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Visitors online').'</legend>';
		if ($totalVisitors)
		{
			$this->html .= $this->l('Total:').' '.(int)($totalVisitors).'
			<div style="overflow-y: scroll; height: 600px;">
			<table cellpadding="0" cellspacing="0" class="table space">
				<tr><th>'.$this->l('Guest').'</th><th>'.$this->l('IP').'</th><th>'.$this->l('Since').'</th><th>'.$this->l('Current page').'</th><th>'.$this->l('Referrer').'</th></tr>';
			foreach ($visitors as $visitor)
				$this->html .= '<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
					<td>'.$visitor['id_guest'].'</td>
					<td style="width: 80px;">'.long2ip($visitor['ip_address']).'</td>
					<td style="width: 100px;">'.substr($visitor['date_add'], 11).'</td>
					<td style="width: 200px;">'.(isset($visitor['page']) ? $visitor['page'] : $this->l('Undefined')).'</td>
					<td style="width: 200px;">'.(empty($visitor['http_referer']) ? $this->l('none') : parse_url($visitor['http_referer'], PHP_URL_HOST)).'</td>
				</tr>';
			$this->html .= '</table></div>';
		}
		else
			$this->html .= $this->l('There are no visitors online.');
		$this->html .= '</fieldset>';

		return $this->html;
	}
}


