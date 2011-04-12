<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class StatsLive extends Module
{
    function __construct()
    {
        $this->name = 'statslive';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		
        parent::__construct();
		
        $this->displayName = $this->l('Visitors online');
        $this->description = $this->l('Display the list of customers and visitors currently online.');
    }
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}
	
	private function getCustomersOnline()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT u.id_customer, u.firstname, u.lastname, pt.name as page
		FROM `'._DB_PREFIX_.'connections` c
		LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
		LEFT JOIN `'._DB_PREFIX_.'page` p ON p.id_page = cp.id_page
		LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON p.id_page_type = pt.id_page_type
		INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
		INNER JOIN `'._DB_PREFIX_.'customer` u ON u.id_customer = g.id_customer
		WHERE cp.`time_end` IS NULL
		AND TIME_TO_SEC(TIMEDIFF(NOW(), cp.`time_start`)) < 900
		GROUP BY c.id_connections
		ORDER BY u.firstname, u.lastname');
	}
	
	private function getVisitorsOnline()
	{
		if (Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT c.id_guest, c.ip_address, c.date_add, c.http_referer, pt.name as page
			FROM `'._DB_PREFIX_.'connections` c
			LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
			LEFT JOIN `'._DB_PREFIX_.'page` p ON p.id_page = cp.id_page
			LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON p.id_page_type = pt.id_page_type
			INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
			WHERE (g.id_customer IS NULL OR g.id_customer = 0)
			AND cp.`time_end` IS NULL
			AND TIME_TO_SEC(TIMEDIFF(NOW(), cp.`time_start`)) < 900
			GROUP BY c.id_connections
			ORDER BY c.date_add DESC');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_guest, c.ip_address, c.date_add, c.http_referer
		FROM `'._DB_PREFIX_.'connections` c
		INNER JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
		WHERE (g.id_customer IS NULL OR g.id_customer = 0)
		AND TIME_TO_SEC(TIMEDIFF(NOW(), c.`date_add`)) < 900
		ORDER BY c.date_add DESC');
	}
	
	public function hookAdminStatsModules($params)
	{
		global $cookie;
		
		$customers = $this->getCustomersOnline();
		$totalCustomers = Db::getInstance()->NumRows();
		$visitors = $this->getVisitorsOnline();
		$totalVisitors = Db::getInstance()->NumRows();
		$irow = 0;
		
		echo '<script type="text/javascript" language="javascript">
			$("#calendar").next().remove();
			$("#calendar").remove();
		</script>';
		if (!Configuration::get('PS_STATSDATA_CUSTOMER_PAGESVIEWS'))
			echo '<div class="warn width3">'.$this->l('You must activate the option "pages views for each customer" in the "Stats datamining" module in order to see the pages currently viewed by your customers.').'</div>';
		echo '
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Customers online').'</legend>';
		if ($totalCustomers)
		{
			echo $this->l('Total:').' '.(int)($totalCustomers).'
			<table cellpadding="0" cellspacing="0" class="table space">
				<tr><th>'.$this->l('ID').'</th><th>'.$this->l('Name').'</th><th>'.$this->l('Current Page').'</th><th>'.$this->l('View').'</th></tr>';
			foreach ($customers as $customer)
				echo '
				<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
					<td>'.$customer['id_customer'].'</td>
					<td style="width: 200px;">'.$customer['firstname'].' '.$customer['lastname'].'</td>
					<td style="width: 200px;">'.$customer['page'].'</td>
					<td style="text-align: right; width: 25px;">
						<a href="index.php?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'" target="_blank">
							<img src="../modules/'.$this->name.'/logo.gif" />
						</a>
					</td>
				</tr>';
			echo '</table>';
		}
		else
			echo $this->l('There are no cusomers online.');
		echo '</fieldset>
		<fieldset class="width3 space"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Visitors online').'</legend>';
		if ($totalVisitors)
		{
			echo $this->l('Total:').' '.(int)($totalVisitors).'
			<div style="overflow-y: scroll; height: 600px;">
			<table cellpadding="0" cellspacing="0" class="table space">
				<tr><th>'.$this->l('Guest').'</th><th>'.$this->l('IP').'</th><th>'.$this->l('Since').'</th><th>'.$this->l('Current page').'</th><th>'.$this->l('Referrer').'</th></tr>';
			foreach ($visitors as $visitor)
				echo '<tr'.($irow++ % 2 ? ' class="alt_row"' : '').'>
					<td>'.$visitor['id_guest'].'</td>
					<td style="width: 80px;">'.long2ip($visitor['ip_address']).'</td>
					<td style="width: 100px;">'.substr($visitor['date_add'], 11).'</td>
					<td style="width: 200px;">'.(isset($visitor['page']) ? $visitor['page'] : $this->l('Undefined')).'</td>
					<td style="width: 200px;">'.(empty($visitor['http_referer']) ? $this->l('none') : parse_url($visitor['http_referer'], PHP_URL_HOST)).'</td>
				</tr>';
			echo '</table></div>';
		}
		else
			echo $this->l('There are no visitors online.');
		echo '</fieldset>';
	}
}


