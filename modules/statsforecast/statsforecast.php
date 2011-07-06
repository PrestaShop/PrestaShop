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
*  @version  Release: $Revision: 7104 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_CAN_LOAD_FILES_'))
	exit;

class StatsForecast extends Module
{
	private $_html = '';
	private $t1 = 0;
	private $t2 = 0;
	private $t3 = 0;
	private $t4 = 0;
	private $t5 = 0;
	private $t6 = 0;
	private $t7 = 0;
	private $t8 = 0;

    public function __construct()
    {
        $this->name = 'statsforecast';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
        parent::__construct();
		
        $this->displayName = $this->l('Stats Dashboard');
        $this->description = '';
    }

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}
	
	public function getContent()
	{
		Tools::redirectAdmin('index.php?tab=AdminStats&module=statsforecast&token='.Tools::getAdminTokenLite('AdminStats'));
	}
	
	public function hookAdminStatsModules()
	{
		global $cookie, $currentIndex;
		$ru = $currentIndex.'&module='.$this->name.'&token='.Tools::getValue('token');
		
		$db = Db::getInstance();
		
		if (!isset($cookie->stats_granularity))
			$cookie->stats_granularity = 10;
		if (Tools::isSubmit('submitIdZone'))
			$cookie->stats_id_zone = Tools::getValue('stats_id_zone');
		if (Tools::isSubmit('submitGranularity'))
			$cookie->stats_granularity = Tools::getValue('stats_granularity');
		
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$employee = new Employee((int)($cookie->id_employee));
		
		// Prepare SQL clause to filter per shop
		$whereOrder = $whereConnection = $whereCustomer = $whereCart = '';
		if ($this->shopID || $this->shopGroupID)
		{
			if ($this->shopID)
			{
				$whereOrder = ' AND o.id_shop = '.$this->shopID;
				$whereConnection = ' AND c.id_shop = '.$this->shopID;
				$whereCustomer = ' AND id_shop = '.$this->shopID;
				$whereCart = ' AND id_shop = '.$this->shopID;
			}
			else if ($this->shopGroupID)
			{
				$whereOrder = ' AND o.id_group_shop = '.$this->shopGroupID;
				$whereConnection = ' AND c.id_group_shop = '.$this->shopGroupID;
				$whereCustomer = ' AND id_group_shop = '.$this->shopGroupID;
				$whereCart = ' AND id_group_shop = '.$this->shopGroupID;
			}
		}
		
		// @todo use PHP functions to get timestamp ...
		$result = $db->getRow('SELECT UNIX_TIMESTAMP(\'2009-06-05 00:00:00\') as t1, UNIX_TIMESTAMP(\''.$employee->stats_date_from.' 00:00:00\') as t2');
		$from = max($result['t1'], $result['t2']);
		$to = strtotime($employee->stats_date_to.' 23:59:59');
		$result2 = $db->getRow('SELECT UNIX_TIMESTAMP(NOW()) as t1, UNIX_TIMESTAMP(\''.$employee->stats_date_to.' 23:59:59\') as t2');
		$to2 = min($result2['t1'], $result2['t2']);
		$interval = ($to - $from) / 60 / 60 / 24;
		$interval2 = ($to2 - $from) / 60 / 60 / 24;
		$prop30 = $interval / $interval2;
		
		if ($cookie->stats_granularity == 7)
			$intervalAvg = $interval2 / 30;
		if ($cookie->stats_granularity == 4)
			$intervalAvg = $interval2 / 365;
		if ($cookie->stats_granularity == 10)
			$intervalAvg = $interval2;
		if ($cookie->stats_granularity == 42)
			$intervalAvg = $interval2 / 7;
		
		define('PS_BASE_URI', '/');
		$result = $db->getRow('SELECT UNIX_TIMESTAMP(\'2009-06-05\') as t1, UNIX_TIMESTAMP(\''.$employee->stats_date_from.'\') as t2');
		$from = max($result['t1'], $result['t2']);
		$to = strtotime($employee->stats_date_to.'');

		$dateFromGAdd = ($cookie->stats_granularity != 42
			? 'SUBSTRING(date_add, 1, '.(int)$cookie->stats_granularity.')'
			: 'IFNULL(MAKEDATE(YEAR(date_add),DAYOFYEAR(date_add)-WEEKDAY(date_add)), CONCAT(YEAR(date_add),"-01-01*"))');
		$dateFromGInvoice = ($cookie->stats_granularity != 42
			? 'SUBSTRING(invoice_date, 1, '.(int)$cookie->stats_granularity.')'
			: 'IFNULL(MAKEDATE(YEAR(invoice_date),DAYOFYEAR(invoice_date)-WEEKDAY(invoice_date)), CONCAT(YEAR(invoice_date),"-01-01*"))');
		
		$sql = 'SELECT
					'.$dateFromGInvoice.' as fix_date,
					COUNT(DISTINCT o.id_order) as countOrders,
					SUM(od.product_quantity) as countProducts,
					SUM(od.product_price * od.product_quantity / o.conversion_rate) as totalProducts
				FROM '._DB_PREFIX_.'orders o
				LEFT JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
				LEFT JOIN '._DB_PREFIX_.'product p ON od.product_id = p.id_product
				WHERE o.valid = 1
					AND o.invoice_date BETWEEN '.ModuleGraph::getDateBetween().'
					'.$whereOrder.'
				GROUP BY '.$dateFromGInvoice.'
				ORDER BY fix_date';
		$result = $db->ExecuteS($sql, false);

		$dataTable = array();
		if ($cookie->stats_granularity == 10)
		{
			$dateEnd = strtotime($employee->stats_date_to.' 23:59:59');
			$dateToday = time();
			for ($i = strtotime($employee->stats_date_from.' 00:00:00'); $i <= $dateEnd AND $i <= $dateToday; $i += 86400)
				$dataTable[$i] = array('fix_date' => date('Y-m-d', $i), 'countOrders' => 0, 'countProducts' => 0, 'totalProducts' => 0);
		}

		while ($row = $db->nextRow($result))
			$dataTable[strtotime($row['fix_date'])] = $row;
		
		$this->_html .= '<div style="float:left;width:660px">
		<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			<p style="float:left">'.$this->l('All amounts are without taxes.').'</p>
			<form id="granularity" action="'.$ru.'#granularity" method="post" style="float:right">
				<input type="hidden" name="submitGranularity" value="1" />
				'.$this->l('Mode:').' <select name="stats_granularity" onchange="this.form.submit();" style="width:100px">
					<option value="10">'.$this->l('Day').'</option>
					<option value="42" '.($cookie->stats_granularity == '42' ? 'selected="selected"' : '').'>'.$this->l('Week').'</option>
					<option value="7" '.($cookie->stats_granularity == '7' ? 'selected="selected"' : '').'>'.$this->l('Month').'</option>
					<option value="4" '.($cookie->stats_granularity == '4' ? 'selected="selected"' : '').'>'.$this->l('Year').'</option>
				</select>
			</form>
			<div class="clear">&nbsp;</div>
			<table class="table" border="0" cellspacing="0" cellspacing="0">
				<tr>
					<th style="width:70px;text-align:center"></th>
					<th style="text-align:center">'.$this->l('Visits').'</th>
					<th style="text-align:center">'.$this->l('Reg.').'</th>
					<th style="text-align:center">'.$this->l('Orders').'</th>
					<th style="text-align:center">'.$this->l('Items').'</th>
					<th style="text-align:center">'.$this->l('% Reg.').'</th>
					<th style="text-align:center">'.$this->l('% Orders').'</th>
					<th style="width:80px;text-align:center">'.$this->l('Coupons').'</th>
					<th style="width:100px;text-align:center">'.$this->l('Products Sales').'</th>
				</tr>';

		$visitArray = array();
		$sql = 'SELECT '.$dateFromGAdd.' as fix_date, COUNT(*) as visits
				FROM '._DB_PREFIX_.'connections c
				WHERE c.date_add BETWEEN '.ModuleGraph::getDateBetween().'
				'.$whereConnection.'
				GROUP BY '.$dateFromGAdd;
		$visits = Db::getInstance()->ExecuteS($sql, false);
		while ($row = $db->nextRow($visits))
			$visitArray[$row['fix_date']] = $row['visits'];

		$discountArray = array();
		$sql = 'SELECT '.$dateFromGInvoice.' as fix_date, SUM(od.value) as total
				FROM '._DB_PREFIX_.'orders o
				LEFT JOIN '._DB_PREFIX_.'order_discount od ON o.id_order = od.id_order
				WHERE o.valid = 1
					AND o.total_paid_real > 0
					AND o.invoice_date BETWEEN '.ModuleGraph::getDateBetween()
					.$whereOrder.'
				GROUP BY '.$dateFromGInvoice;
		$discounts = Db::getInstance()->ExecuteS($sql, false);
		while ($row = $db->nextRow($discounts))
			$discountArray[$row['fix_date']] = $row['total'];

		$today = date('Y-m-d');
		foreach ($dataTable as $row)
		{
			$discountToday = (isset($discountArray[$row['fix_date']]) ? $discountArray[$row['fix_date']] : 0);
			$visitsToday = (int)(isset($visitArray[$row['fix_date']]) ? $visitArray[$row['fix_date']] : 0);
			
			$dateFromGReg = ($cookie->stats_granularity != 42
				? 'LIKE \''.$row['fix_date'].'%\''
				: 'BETWEEN \''.substr($row['fix_date'], 0, 10).' 00:00:00\' AND DATE_ADD(\''.substr($row['fix_date'], 0, 8).substr($row['fix_date'], 8, 2).' 23:59:59\', INTERVAL 7 DAY)');
			$sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'customer
					WHERE date_add BETWEEN '.ModuleGraph::getDateBetween().'
						AND date_add '.$dateFromGReg
						.$whereCustomer;
			$row['registrations'] = Db::getInstance()->getValue($sql);
			$totalHT = $row['totalProducts'] - $discountToday;

			$this->_html .= '
			<tr>
				<td>'.$row['fix_date'].'</td>
				<td align="center">'.$visitsToday.'</td>
				<td align="center">'.(int)($row['registrations']).'</td>
				<td align="center">'.(int)($row['countOrders']).'</td>
				<td align="center">'.(int)($row['countProducts']).'</td>
				<td align="center">'.($visitsToday ? round(100 * (int)($row['registrations']) / $visitsToday, 2).' %' : '-').'</td>
				<td align="center">'.($visitsToday ? round(100 * (int)($row['countOrders']) / $visitsToday, 2).' %' : '-').'</td>
				<td align="right">'.Tools::displayPrice($discountToday, $currency).'</td>
				<td align="right" >'.Tools::displayPrice($totalHT, $currency).'</td>
			</tr>';
			
			$this->t1 += $visitsToday;
			$this->t2 += (int)($row['registrations']);
			$this->t3 += (int)($row['countOrders']);
			$this->t4 += (int)($row['countProducts']);
			$this->t7 += $discountToday;
			$this->t8 += $totalHT;
		}

		$this->_html .= '
				<tr>
					<th style="width:70px;text-align:center"></th>
					<th style="text-align:center">'.$this->l('Visits').'</th>
					<th style="text-align:center">'.$this->l('Reg.').'</th>
					<th style="text-align:center">'.$this->l('Orders').'</th>
					<th style="text-align:center">'.$this->l('Items').'</th>
					<th style="text-align:center">'.$this->l('% Reg.').'</th>
					<th style="text-align:center">'.$this->l('% Orders').'</th>
					<th style="width:80px;text-align:center">'.$this->l('Coupons').'</th>
					<th style="width:100px;text-align:center">'.$this->l('Products Sales').'</th>
				</tr>
				<tr>
					<th>'.$this->l('Total').'</th>
					<td style="font-weight: 700" align="center">'.(int)($this->t1).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t2).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t3).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t4).'</td>
					<td style="font-weight: 700" align="center">--</td>
					<td style="font-weight: 700" align="center">--</td>
					<td style="font-weight: 700" align="right">'.Tools::displayPrice($this->t7, $currency).'</td>
					<td style="font-weight: 700" align="right">'.Tools::displayPrice($this->t8, $currency).'</td>
				</tr>
				<tr>
					<th>'.$this->l('Average').'</th>
					<td style="font-weight: 700" align="center">'.(int)($this->t1 / $intervalAvg).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t2 / $intervalAvg).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t3 / $intervalAvg).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t4 / $intervalAvg).'</td>
					<td style="font-weight: 700" align="center">'.($this->t1 ? round(100 * $this->t2 / $this->t1, 2) .' %' : '-').'</td>
					<td style="font-weight: 700" align="center">'.($this->t1 ? round(100 * $this->t3 / $this->t1, 2) .' %' : '-').'</td>
					<td style="font-weight: 700" align="right">'.Tools::displayPrice($this->t7 / $intervalAvg, $currency).'</td>
					<td style="font-weight: 700" align="right">'.Tools::displayPrice($this->t8 / $intervalAvg, $currency).'</td>
				</tr>
				<tr>
					<th>'.$this->l('Forecast').'</th>
					<td style="font-weight: 700" align="center">'.(int)($this->t1 * $prop30).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t2 * $prop30).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t3 * $prop30).'</td>
					<td style="font-weight: 700" align="center">'.(int)($this->t4 * $prop30).'</td>
					<td style="font-weight: 700" align="center">--</td>
					<td style="font-weight: 700" align="center">--</td>
					<td style="font-weight: 700" align="right">'.Tools::displayPrice($this->t7 * $prop30, $currency).'</td>
					<td style="font-weight: 700" align="right">'.Tools::displayPrice($this->t8 * $prop30, $currency).'</td>
				</tr>
			</table>
		</fieldset>';

		$ca = $this->getRealCA();

		$sql = 'SELECT COUNT(DISTINCT c.id_guest)
				FROM '._DB_PREFIX_.'connections c
				WHERE c.date_add BETWEEN '.ModuleGraph::getDateBetween()
					.$whereConnection;
		$visitors = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(DISTINCT id_customer)
				FROM '._DB_PREFIX_.'connections c
				INNER JOIN '._DB_PREFIX_.'guest g ON c.id_guest = g.id_guest
				WHERE c.id_customer != 0
					AND c.date_add BETWEEN '.ModuleGraph::getDateBetween()
					.$whereConnection;
		$customers = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'cart
				WHERE id_cart IN (
						SELECT id_cart FROM '._DB_PREFIX_.'cart_product
					) AND (
						date_add BETWEEN '.ModuleGraph::getDateBetween().' OR date_upd BETWEEN '.ModuleGraph::getDateBetween().'
					)'.$whereCart;
		$carts = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'cart
				WHERE id_cart IN (
						SELECT id_cart FROM '._DB_PREFIX_.'cart_product
					) AND id_address_invoice != 0
					AND (
						date_add BETWEEN '.ModuleGraph::getDateBetween().' OR date_upd BETWEEN '.ModuleGraph::getDateBetween().'
					)'.$whereCart;
		$fullcarts = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'orders o
				WHERE o.valid = 1
					AND o.date_add BETWEEN '.ModuleGraph::getDateBetween()
					.$whereOrder;
		$orders = Db::getInstance()->getValue($sql);
		
		$this->_html .= '<div class="clear">&nbsp;</div>
		<fieldset><legend><img src="../modules/'.$this->name.'/funnel.png" /> '.$this->l('Conversion').'</legend>
			<span style="float:left;text-align:center;margin-right:10px;padding-top:15px">'.$this->l('Visitors').'<br />'.$visitors.'</span>
			<span style="float:left;text-align:center;margin-right:10px">
				<img src="../modules/'.$this->name.'/next.png"><br />'.round(100 * $customers / max(1, $visitors)).' %<br />
				<img src="../modules/'.$this->name.'/next.png"><br />'.round(100 * $carts / max(1, $visitors)).' %
			</span>
			<span style="float:left;text-align:center;margin-right:10px">
				'.$this->l('Accounts').'<br />'.$customers.'<br />
				'.$this->l('Carts').'<br />'.$carts.'
			</span>
			<span style="float:left;text-align:center;margin-right:10px">
				<img src="../modules/'.$this->name.'/next.png"><br />'.round(100 * $fullcarts / max(1, $customers)).' %<br />
				<img src="../modules/'.$this->name.'/next.png"><br />'.round(100 * $fullcarts / max(1, $carts)).' %<br />
			</span>
			<span style="float:left;text-align:center;margin-right:10px;padding-top:15px">'.$this->l('Full carts').'<br />'.$fullcarts.'</span>
			<span style="float:left;text-align:center;margin-right:10px;padding-top:15px"><img src="../modules/'.$this->name.'/next.png"><br />'.round(100 * $orders / max(1, $fullcarts)).' %</span>
			<span style="float:left;text-align:center;margin-right:10px;padding-top:15px">'.$this->l('Orders').'<br />'.$orders.'</span>
			<br class="clear" /><br class="clear" />
			<span style="float:left;text-align:center;margin-right:10px">'.$this->l('Registered visitors').'</span>
			<span style="float:left;text-align:center;margin-right:10px">
				<img src="../modules/'.$this->name.'/next.png"> '.round(100 * $orders / max(1, $customers), 2).' % <img src="../modules/'.$this->name.'/next.png">
			</span>
			<span style="float:left;text-align:center;margin-right:10px">'.$this->l('Orders').'</span>
			<br class="clear" />
			<span style="float:left;text-align:center;margin-right:10px">'.$this->l('Visitors').'</span>
			<span style="float:left;text-align:center;margin-right:10px">
				<img src="../modules/'.$this->name.'/next.png"> <b>'.round(100 * $orders / max(1, $visitors), 2).' %</b> <img src="../modules/'.$this->name.'/next.png">
			</span>
			<span style="float:left;text-align:center;margin-right:10px">'.$this->l('Orders').'</span>
			<div class="clear">&nbsp;</div>
			'.$this->l('Turn your visitors into money:').'
			<br />'.$this->l('Each visitor yields').' <b>'.Tools::displayPrice($ca['ventil']['total'] / max(1, $visitors), $currency).'.</b>
			<br />'.$this->l('Each registered visitor yields').' <b>'.Tools::displayPrice($ca['ventil']['total'] / max(1, $customers), $currency).'</b>.
		</fieldset>';		
		
		$from = strtotime($employee->stats_date_from.' 00:00:00');
		$to = strtotime($employee->stats_date_to.' 23:59:59');
		$interval = ($to - $from) / 60 / 60 / 24;
		$prop5000 = 5000 / 30 * $interval;
		
		$this->_html .= '
		<div class="clear">&nbsp;</div>';
		$this->_html .= '<fieldset><legend id="payment"><img src="../img/t/AdminPayment.gif" />'.$this->l('Payment distibution').'</legend>
			<form id="cat" action="'.$ru.'#payment" method="post" style="float:right">
				<input type="hidden" name="submitIdZone" value="1" />
				'.$this->l('Zone:').' <select name="stats_id_zone" onchange="this.form.submit();">
					<option value="0">'.$this->l('-- No filter --').'</option>';
		foreach (Zone::getZones() as $zone)
			$this->_html .= '<option value="'.(int)$zone['id_zone'].'" '.($cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
		$this->_html .= '</select>
			</form>
			<table class="table float" border="0" cellspacing="0" cellspacing="0">
				<tr><th>'.$this->l('Module').'</th><th>'.$this->l('Count').'</th><th>'.$this->l('Total').'</th><th>'.$this->l('Cart').'</th></tr>';
			foreach ($ca['payment'] as $payment)
				$this->_html .= '
					<tr>
						<td>'.$payment['module'].'</td>
						<td style="text-align:center;padding:4px">'.(int)($payment['nb']).'<br />'.number_format((100 * $payment['nb'] / $ca['ventil']['nb']), 1, '.', ' ').' %</td>
						<td style="text-align:center;padding:4px">'.Tools::displayPrice($payment['total'], $currency).'<br />'.number_format((100 * $payment['total'] / $ca['ventil']['total']), 1, '.', ' ').' %</td>
						<td style="text-align:center;padding:4px">'.Tools::displayPrice($payment['cart'], $currency).'</td>
					</tr>';
			$this->_html .= '
			</table>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<fieldset><legend><img src="../img/t/AdminCatalog.gif" /> '.$this->l('Category distribution').'</legend>
			<form id="cat" action="'.$ru.'#cat" method="post" style="float:right">
				<input type="hidden" name="submitIdZone" value="1" />
				'.$this->l('Zone:').' <select name="stats_id_zone" onchange="this.form.submit();">
					<option value="0">'.$this->l('-- No filter --').'</option>';
		foreach (Zone::getZones() as $zone)
			$this->_html .= '<option value="'.(int)$zone['id_zone'].'" '.($cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
		$this->_html .= '	</select>
			</form>
			<table class="table float" border="0" cellspacing="0" cellspacing="0">
				<tr><th style="width:50px">'.$this->l('Category').'</th><th>'.$this->l('Count').'</th><th>'.$this->l('Sales').'</th><th>'.$this->l('% Count').'</th><th>'.$this->l('% Sales').'</th><th>'.$this->l('Avg price').'</th></tr>';
			foreach ($ca['cat'] as $catrow)
				$this->_html .= '
				<tr>
					<td>'.(empty($catrow['name']) ? $this->l('Unknown') : $catrow['name']).'</td>
					<td align="right">'.$catrow['orderQty'].'</td>
					<td align="right">'.Tools::displayPrice($catrow['orderSum'], $currency).'</td>
					<td align="right">'.number_format((100 * $catrow['orderQty'] / $this->t4), 1, '.', ' ').'%</td>
					<td align="right">'.number_format((100 * $catrow['orderSum'] / $ca['ventil']['total']), 1, '.', ' ').'%</td>
					<td align="right">'.Tools::displayPrice($catrow['priveAvg'], $currency).'</td>
				</tr>';
			$this->_html .= '
			</table>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<fieldset><legend><img src="../img/t/AdminLanguages.gif" /> '.$this->l('Language distribution').'</legend>
			<table class="table" border="0" cellspacing="0" cellspacing="0">
				<tr><th>'.$this->l('Customers').'</th><th>'.$this->l('Sales').'</th><th>'.$this->l('%').'</th><th colspan="2">'.$this->l('Growth').'</th></tr>';
		foreach ($ca['lang'] as $ophone => $amount)
		{
			$percent = (int)($ca['langprev'][$ophone]) ? number_format((100 * $amount / $ca['langprev'][$ophone]) - 100, 1, '.', ' ') : '&#x221e;';
			$this->_html .= '
				<tr '.(($percent < 0) ? 'class="alt_row"' : '').'>
					<td>'.$ophone.'</td>
					<td align="right">'.Tools::displayPrice($amount, $currency).'</td>
					<td align="right">'.($ca['ventil']['total'] ? number_format((100 * $amount / $ca['ventil']['total']), 1, '.', ' ').'%' : '-').'</td>
					<td>'.(($percent > 0 OR $percent == '&#x221e;') ? '<img src="../img/admin/arrow_up.png" />' : '<img src="../img/admin/arrow_down.png" /> ').'</td>
					<td align="right">'.(($percent > 0 OR $percent == '&#x221e;') ? '+' : '').$percent.'%</td>
				</tr>';
		}
		$this->_html .= '
			</table>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<fieldset><legend><img src="../img/t/AdminLanguages.gif" />'.$this->l('Zone distribution').'</legend>
			<table class="table" border="0" cellspacing="0" cellspacing="0">
				<tr><th>'.$this->l('Zone').'</th><th>'.$this->l('Count').'</th><th>'.$this->l('Total').'</th><th>'.$this->l('% Count').'</th><th>'.$this->l('% Sales').'</th></tr>';
		foreach ($ca['zones'] as $zone)
			$this->_html .= '
				<tr>
					<td>'.(isset($zone['name']) ? $zone['name'] : $this->l('Undefined')).'</td>
					<td align="right">'.(int)($zone['nb']).'</td>
					<td align="right">'.Tools::displayPrice($zone['total'], $currency).'</td>
					<td align="right">'.number_format((100 * $zone['nb'] / $ca['ventil']['nb']), 1, '.', ' ').'%</td>
					<td align="right">'.number_format((100 * $zone['total'] / $ca['ventil']['total']), 1, '.', ' ').'%</td>
				</tr>';
		$this->_html .= '
			</table>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<fieldset><legend id="currencies"><img src="../img/t/AdminCurrencies.gif" />'.$this->l('Currency distribution').'</legend>
			<form id="cat" action="'.$ru.'#currencies" method="post" style="float:right">
				<input type="hidden" name="submitIdZone" value="1" />
				'.$this->l('Zone:').' <select name="stats_id_zone" onchange="this.form.submit();">
					<option value="0">'.$this->l('-- No filter --').'</option>';
		foreach (Zone::getZones() as $zone)
			$this->_html .= '<option value="'.(int)$zone['id_zone'].'" '.($cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
		$this->_html .= '</select>
			</form>
			<table class="table" border="0" cellspacing="0" cellspacing="0">
				<tr><th>'.$this->l('Currency').'</th><th>'.$this->l('Count').'</th><th>'.$this->l('Sales (converted)').'</th><th>'.$this->l('% Count').'</th><th>'.$this->l('% Sales').'</th></tr>';
			foreach ($ca['currencies'] as $currencyRow)
				$this->_html .= '
					<tr>
						<td>'.$currencyRow['name'].'</td>
						<td align="right">'.(int)($currencyRow['nb']).'</td>
						<td align="right">'.Tools::displayPrice($currencyRow['total'], $currency).'</td>
						<td align="right">'.number_format((100 * $currencyRow['nb'] / $ca['ventil']['nb']), 1, '.', ' ').'%</td>
						<td align="right">'.number_format((100 * $currencyRow['total'] / $ca['ventil']['total']), 1, '.', ' ').'%</td>
					</tr>';
			$this->_html .= '
			</table>
		</fieldset>
		<div class="clear">&nbsp;</div>
		<fieldset><legend><img src="../img/t/AdminCatalog.gif" />'.$this->l('Attribute distribution').'</legend>
			<table class="table" border="0" cellspacing="0" cellspacing="0">
				<tr><th>'.$this->l('Group').'</th><th>'.$this->l('Attribute').'</th><th>'.$this->l('Count').'</th></tr>';
		foreach ($ca['attributes'] as $attribut)
			$this->_html .= '
				<tr>
					<td>'.$attribut['gname'].'</td>
					<td>'.$attribut['aname'].'</td>
					<td align="right">'.(int)($attribut['total']).'</td>
				</tr>';
		$this->_html .= '</table>
		</fieldset>
		</div>';
		
		return $this->_html;
	}

	private function getRealCA()
	{
		global $cookie;

		$employee = new Employee($cookie->id_employee);
		$ca = array();

		// Prepare SQL clause to filter per shop
		$whereOrder = $where = $join = $joinLang = $whereLang = '';
		if ($this->shopID || $this->shopGroupID)
		{
			$joinLang = ' LEFT JOIN '._DB_PREFIX_.'lang_shop ls ON ls.id_lang = l.id_lang ';
			if ($this->shopID)
			{
				$whereOrder = ' AND o.id_shop = '.$this->shopID;
				$whereLang = ' AND ls.id_shop = '.$this->shopID;
			}
			else if ($this->shopGroupID)
			{
				$whereOrder = ' AND o.id_group_shop = '.$this->shopGroupID;
				$whereLang = ' AND ls.id_shop IN (SELECT id_shop FROM '._DB_PREFIX_.'shop WHERE id_group_shop = '.$this->shopGroupID.')';
			}
		}

		if ((int)$cookie->stats_id_zone)
		{
			$join =  ' LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_invoice = a.id_address LEFT JOIN `'._DB_PREFIX_.'country` co ON co.id_country = a.id_country';
			$where = ' AND co.id_zone = '.$cookie->stats_id_zone.' ';
		}

		$sql = 'SELECT SUM(od.`product_price` * od.`product_quantity` / o.conversion_rate) as orderSum, COUNT(*) AS orderQty, cl.name, AVG(od.`product_price` / o.conversion_rate) as priveAvg
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = od.product_id
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.id_category_default = cl.id_category AND cl.id_lang = '.(int)($cookie->id_lang).')
				'.$join.'
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$where.'
					'.$whereOrder.'
				GROUP BY p.id_category_default';
		$ca['cat'] = Db::getInstance()->ExecuteS($sql);
		uasort($ca['cat'], 'statsforecast_sort');

		$langValues = '';
		$sql = 'SELECT l.id_lang, l.iso_code
				FROM `'._DB_PREFIX_.'lang` l
				'.$joinLang.'
				WHERE l.active = 1
					'.$whereLang;
		$languages = Db::getInstance()->ExecuteS($sql);
		foreach ($languages as $language)
			$langValues .= 'SUM(IF(o.id_lang = '.(int)$language['id_lang'].', total_products / o.conversion_rate, 0)) as '.pSQL($language['iso_code']).',';
		$langValues = rtrim($langValues, ',');

		if ($langValues)
		{
			$sql = 'SELECT '.$langValues.'
					FROM `'._DB_PREFIX_.'orders` o
					WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$whereOrder;
			$ca['lang'] = Db::getInstance()->getRow($sql);
			arsort($ca['lang']);
			
			$sql = 'SELECT '.$langValues.'
					FROM `'._DB_PREFIX_.'orders` o
					WHERE o.valid = 1
						AND ADDDATE(o.`invoice_date`, interval 30 day) BETWEEN \''.$employee->stats_date_from.' 00:00:00\' AND \''.min(date('Y-m-d H:i:s'), $employee->stats_date_to.' 23:59:59').'\'
						'.$whereOrder;
			$ca['langprev'] = Db::getInstance()->getRow($sql);
		}
		else
		{
			$ca['lang'] = array();
			$ca['langprev'] = array();
		}

		$sql = 'SELECT module, SUM(total_products / o.conversion_rate) as total, COUNT(*) as nb, AVG(total_products / o.conversion_rate) as cart
				FROM `'._DB_PREFIX_.'orders` o
				'.$join.'
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$where.'
					'.$whereOrder.'
				GROUP BY o.module
				ORDER BY total DESC';
		$ca['payment'] = Db::getInstance()->ExecuteS($sql);

		$sql = 'SELECT z.name, SUM(o.total_products / o.conversion_rate) as total, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_invoice = a.id_address
				LEFT JOIN `'._DB_PREFIX_.'country` c ON c.id_country = a.id_country
				LEFT JOIN `'._DB_PREFIX_.'zone` z ON z.id_zone = c.id_zone
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$whereOrder.'
				GROUP BY c.id_zone
				ORDER BY total DESC';
		$ca['zones'] = Db::getInstance()->ExecuteS($sql);

		$sql = 'SELECT cu.name, SUM(o.total_products / o.conversion_rate) as total, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'currency` cu ON o.id_currency = cu.id_currency
				'.$join.'
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$where.'
					'.$whereOrder.'
				GROUP BY o.id_currency
				ORDER BY total DESC';
		$ca['currencies'] = Db::getInstance()->ExecuteS($sql);

		$sql = 'SELECT SUM(total_products / o.conversion_rate) as total, COUNT(*) AS nb
				FROM `'._DB_PREFIX_.'orders` o
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$whereOrder;
		$ca['ventil'] = Db::getInstance()->getRow($sql);

		$sql = 'SELECT /*pac.id_attribute,*/ agl.name as gname, al.name as aname, COUNT(*) as total
				FROM '._DB_PREFIX_.'orders o
				LEFT JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
				INNER JOIN '._DB_PREFIX_.'product_attribute_combination pac ON od.product_attribute_id = pac.id_product_attribute
				INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
				INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '.(int)($cookie->id_lang).')
				INNER JOIN '._DB_PREFIX_.'attribute_lang al ON (a.id_attribute = al.id_attribute AND al.id_lang = '.(int)($cookie->id_lang).')
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$whereOrder.'
				GROUP BY pac.id_attribute';
		$ca['attributes'] = Db::getInstance()->ExecuteS($sql);

		return $ca;
	}
}

function statsforecast_sort($a, $b)
{
	if ($a['orderSum'] == $b['orderSum'])
		return 0;
	return ($a['orderSum'] > $b['orderSum']) ? -1 : 1;
}