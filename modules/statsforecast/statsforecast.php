<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
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
		$this->description = 'This is the main module for the Stats dashboard. It displays a summary of all your current statistics.';
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function getContent()
	{
		Tools::redirectAdmin('index.php?controller=AdminStats&module=statsforecast&token='.Tools::getAdminTokenLite('AdminStats'));
	}

	public function hookAdminStatsModules()
	{
		$ru = AdminController::$currentIndex.'&module='.$this->name.'&token='.Tools::getValue('token');

		$db = Db::getInstance();

		if (!isset($this->context->cookie->stats_granularity))
			$this->context->cookie->stats_granularity = 10;
		if (Tools::isSubmit('submitIdZone'))
			$this->context->cookie->stats_id_zone = (int)Tools::getValue('stats_id_zone');
		if (Tools::isSubmit('submitGranularity'))
			$this->context->cookie->stats_granularity = Tools::getValue('stats_granularity');

		$currency = $this->context->currency;
		$employee = $this->context->employee;

		$from = max(strtotime(_PS_CREATION_DATE_.' 00:00:00'), strtotime($employee->stats_date_from.' 00:00:00'));
		$to = strtotime($employee->stats_date_to.' 23:59:59');
		$to2 = min(time(), $to);
		$interval = ($to - $from) / 60 / 60 / 24;
		$interval2 = ($to2 - $from) / 60 / 60 / 24;
		$prop30 = $interval / $interval2;

		if ($this->context->cookie->stats_granularity == 7)
			$intervalAvg = $interval2 / 30;
		if ($this->context->cookie->stats_granularity == 4)
			$intervalAvg = $interval2 / 365;
		if ($this->context->cookie->stats_granularity == 10)
			$intervalAvg = $interval2;
		if ($this->context->cookie->stats_granularity == 42)
			$intervalAvg = $interval2 / 7;

		$dataTable = array();
		if ($this->context->cookie->stats_granularity == 10)
			for ($i = $from; $i <= $to2; $i = strtotime('+1 day', $i))
				$dataTable[date('Y-m-d', $i)] = array('fix_date' => date('Y-m-d', $i), 'countOrders' => 0, 'countProducts' => 0, 'totalSales' => 0);

		$dateFromGAdd = ($this->context->cookie->stats_granularity != 42
			? 'LEFT(date_add, '.(int)$this->context->cookie->stats_granularity.')'
			: 'IFNULL(MAKEDATE(YEAR(date_add),DAYOFYEAR(date_add)-WEEKDAY(date_add)), CONCAT(YEAR(date_add),"-01-01*"))');

		$dateFromGInvoice = ($this->context->cookie->stats_granularity != 42
			? 'LEFT(invoice_date, '.(int)$this->context->cookie->stats_granularity.')'
			: 'IFNULL(MAKEDATE(YEAR(invoice_date),DAYOFYEAR(invoice_date)-WEEKDAY(invoice_date)), CONCAT(YEAR(invoice_date),"-01-01*"))');

		$result = $db->query('
		SELECT
			'.$dateFromGInvoice.' as fix_date,
			COUNT(*) as countOrders,
			SUM((SELECT SUM(od.product_quantity) FROM '._DB_PREFIX_.'order_detail od WHERE o.id_order = od.id_order)) as countProducts,
			SUM(o.total_paid_tax_excl / o.conversion_rate) as totalSales
		FROM '._DB_PREFIX_.'orders o
		WHERE o.valid = 1
		AND o.invoice_date BETWEEN '.ModuleGraph::getDateBetween().'
		'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
		GROUP BY '.$dateFromGInvoice);
		while ($row = $db->nextRow($result))
			$dataTable[$row['fix_date']] = $row;

		$this->_html .= '<div>
			<div class="panel-heading"><i class="icon-dashboard"></i> '.$this->displayName.'</div>
			<div class="alert alert-info">'.$this->l('All amounts listed do not include tax.').'</div>
			<form id="granularity" action="'.Tools::safeOutput($ru).'#granularity" method="post" class="form-horizontal">
				<div class="row row-margin-bottom">
					<label class="control-label col-lg-3">
						'.$this->l('Display:').'
					</label>
					<div class="col-lg-2">
						<input type="hidden" name="submitGranularity" value="1" />
						<select name="stats_granularity" onchange="this.form.submit();">
							<option value="10">'.$this->l('Per Day').'</option>
							<option value="42" '.($this->context->cookie->stats_granularity == '42' ? 'selected="selected"' : '').'>'.$this->l('Per week').'</option>
							<option value="7" '.($this->context->cookie->stats_granularity == '7' ? 'selected="selected"' : '').'>'.$this->l('Per month').'</option>
							<option value="4" '.($this->context->cookie->stats_granularity == '4' ? 'selected="selected"' : '').'>'.$this->l('Per year').'</option>
						</select>
					</div>
				</div>
			</form>

			<table class="table">
				<thead>
					<tr>
						<th></th>
						<th class="center"><span class="title_box active">'.$this->l('Visits').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('Registrations').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('orders').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('Items').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('% Registrations').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('% Orders').'</span></th>
						<th class="center"><span class="title_box active">'.$this->l('Sales').'</span></th>
					</tr>
				</thead>';

		$visitArray = array();
		$sql = 'SELECT '.$dateFromGAdd.' as fix_date, COUNT(*) as visits
				FROM '._DB_PREFIX_.'connections c
				WHERE c.date_add BETWEEN '.ModuleGraph::getDateBetween().'
				'.Shop::addSqlRestriction(false, 'c').'
				GROUP BY '.$dateFromGAdd;
		$visits = Db::getInstance()->query($sql);
		while ($row = $db->nextRow($visits))
			$visitArray[$row['fix_date']] = $row['visits'];

		$today = date('Y-m-d');
		foreach ($dataTable as $row)
		{
			$visitsToday = (int)(isset($visitArray[$row['fix_date']]) ? $visitArray[$row['fix_date']] : 0);

			$dateFromGReg = ($this->context->cookie->stats_granularity != 42
				? 'LIKE \''.$row['fix_date'].'%\''
				: 'BETWEEN \''.substr($row['fix_date'], 0, 10).' 00:00:00\' AND DATE_ADD(\''.substr($row['fix_date'], 0, 8).substr($row['fix_date'], 8, 2).' 23:59:59\', INTERVAL 7 DAY)');
			$row['registrations'] = Db::getInstance()->getValue('
			SELECT COUNT(*) FROM '._DB_PREFIX_.'customer
			WHERE date_add BETWEEN '.ModuleGraph::getDateBetween().'
			AND date_add '.$dateFromGReg
			.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER));

			$this->_html .= '
			<tr>
				<td>'.$row['fix_date'].'</td>
				<td align="center">'.$visitsToday.'</td>
				<td align="center">'.(int)($row['registrations']).'</td>
				<td align="center">'.(int)($row['countOrders']).'</td>
				<td align="center">'.(int)($row['countProducts']).'</td>
				<td align="center">'.($visitsToday ? round(100 * (int)($row['registrations']) / $visitsToday, 2).' %' : '-').'</td>
				<td align="center">'.($visitsToday ? round(100 * (int)($row['countOrders']) / $visitsToday, 2).' %' : '-').'</td>
				<td align="right">'.Tools::displayPrice($row['totalSales'], $currency).'</td>
			</tr>';

			$this->t1 += $visitsToday;
			$this->t2 += (int)($row['registrations']);
			$this->t3 += (int)($row['countOrders']);
			$this->t4 += (int)($row['countProducts']);
			$this->t8 += $row['totalSales'];
		}

		$this->_html .= '
				<tr>
					<th></th>
					<th class="center"><span class="title_box active">'.$this->l('Visits').'</span></th>
					<th class="center"><span class="title_box active">'.$this->l('Registrations').'</span></th>
					<th class="center"><span class="title_box active">'.$this->l('orders').'</span></th>
					<th class="center"><span class="title_box active">'.$this->l('Items').'</span></th>
					<th class="center"><span class="title_box active">'.$this->l('% Registrations').'</span></th>
					<th class="center"><span class="title_box active">'.$this->l('% Orders').'</span></th>
					<th class="center"><span class="title_box active">'.$this->l('Sales').'</span></th>
				</tr>
				<tr>
					<td>'.$this->l('Total').'</td>
					<td class="center">'.(int)($this->t1).'</td>
					<td class="center">'.(int)($this->t2).'</td>
					<td class="center">'.(int)($this->t3).'</td>
					<td class="center">'.(int)($this->t4).'</td>
					<td class="center">--</td>
					<td class="center">--</td>
					<td align="right">'.Tools::displayPrice($this->t8, $currency).'</td>
				</tr>
				<tr>
					<td>'.$this->l('Average').'</td>
					<td class="center">'.(int)($this->t1 / $intervalAvg).'</td>
					<td class="center">'.(int)($this->t2 / $intervalAvg).'</td>
					<td class="center">'.(int)($this->t3 / $intervalAvg).'</td>
					<td class="center">'.(int)($this->t4 / $intervalAvg).'</td>
					<td class="center">'.($this->t1 ? round(100 * $this->t2 / $this->t1, 2) .' %' : '-').'</td>
					<td class="center">'.($this->t1 ? round(100 * $this->t3 / $this->t1, 2) .' %' : '-').'</td>
					<td align="right">'.Tools::displayPrice($this->t8 / $intervalAvg, $currency).'</td>
				</tr>
				<tr>
					<td>'.$this->l('Forecast').'</td>
					<td class="center">'.(int)($this->t1 * $prop30).'</td>
					<td class="center">'.(int)($this->t2 * $prop30).'</td>
					<td class="center">'.(int)($this->t3 * $prop30).'</td>
					<td class="center">'.(int)($this->t4 * $prop30).'</td>
					<td class="center">--</td>
					<td class="center"class="center">--</td>
					<td align="right">'.Tools::displayPrice($this->t8 * $prop30, $currency).'</td>
				</tr>
			</table>';

		$ca = $this->getRealCA();

		$sql = 'SELECT COUNT(DISTINCT c.id_guest)
				FROM '._DB_PREFIX_.'connections c
				WHERE c.date_add BETWEEN '.ModuleGraph::getDateBetween()
					.Shop::addSqlRestriction(false, 'c');
		$visitors = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(DISTINCT g.id_customer)
				FROM '._DB_PREFIX_.'connections c
				INNER JOIN '._DB_PREFIX_.'guest g ON c.id_guest = g.id_guest
				WHERE g.id_customer != 0
					AND c.date_add BETWEEN '.ModuleGraph::getDateBetween()
					.Shop::addSqlRestriction(false, 'c');
		$customers = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'cart
				WHERE id_cart IN (
						SELECT id_cart FROM '._DB_PREFIX_.'cart_product
					) AND (
						date_add BETWEEN '.ModuleGraph::getDateBetween().' OR date_upd BETWEEN '.ModuleGraph::getDateBetween().'
					)'.Shop::addSqlRestriction();
		$carts = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'cart
				WHERE id_cart IN (
						SELECT id_cart FROM '._DB_PREFIX_.'cart_product
					) AND id_address_invoice != 0
					AND (
						date_add BETWEEN '.ModuleGraph::getDateBetween().' OR date_upd BETWEEN '.ModuleGraph::getDateBetween().'
					)'.Shop::addSqlRestriction();
		$fullcarts = Db::getInstance()->getValue($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'orders o
				WHERE o.valid = 1
					AND o.date_add BETWEEN '.ModuleGraph::getDateBetween()
					.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o');
		$orders = Db::getInstance()->getValue($sql);

		$this->_html .= '
		<div class="row row-margin-bottom">
			<h4><i class="icon-filter"></i> '.$this->l('Conversion').'</h4>
		</div>
		<div class="row row-margin-bottom">
			<table class="table">
				<tbody>
					<tr>
						<td rowspan="2" class="center" valign="middle">
							<p>'.$this->l('Visitors').'</p>
							<p>'.$visitors.'</p>
						</td>
						<td class="center">
							<p><i class="icon-chevron-right"></i></p>
							<p>'.round(100 * $customers / max(1, $visitors)).' %</p>
						</td>
						<td class="center">
							<p>'.$this->l('Accounts').'</p>
							<p>'.$customers.'</p>
						</td>
						<td class="center">
							<p><i class="icon-chevron-right"></i></p>
							<p>'.round(100 * $fullcarts / max(1, $customers)).' %</p>
						</td>
						<td rowspan="2" class="center">
							<p>'.$this->l('Full carts').'</p>
							<p>'.$fullcarts.'</p>
						</td>
						<td rowspan="2" class="center">
							<p><i class="icon-chevron-right"></i></p>
							<p>'.round(100 * $orders / max(1, $fullcarts)).' %</p>
						</td>
						<td rowspan="2" class="center">
							<p>'.$this->l('Orders').'</p>
							<p>'.$orders.'</p>
						</td>
						<td rowspan="2" class="center">
							<p>'.$this->l('Registered visitors').'</p>
						</td>
						<td rowspan="2" class="center">
							<i class="icon-chevron-right"></i>
						</td>
						<td rowspan="2" class="center">
							<p>'.round(100 * $orders / max(1, $customers), 2).' %</p>
						</td>
						<td rowspan="2" class="center">
							<i class="icon-chevron-right"></i>
						</td>
						<td rowspan="2" class="center">
							<p>'.$this->l('Orders').'</p>
						</td>
						<td rowspan="2" class="center">
							<p>'.$this->l('Visitors').'</p>
						</td>
						<td rowspan="2" class="center">
							<i class="icon-chevron-right"></i>
						</td>
						<td rowspan="2" class="center">
							<p>'.round(100 * $orders / max(1, $visitors), 2).' %</p>
						</td>
						<td rowspan="2" class="center">
							<i class="icon-chevron-right"></i>
						</td>
						<td rowspan="2" class="center">
							<p>'.$this->l('orders').'</p>
						</td>
					</tr>
					<tr>
						<td class="center">
							<p><i class="icon-chevron-right"></i></p>
							<p>'.round(100 * $carts / max(1, $visitors)).' %</p>
						</td>
						<td class="center">
							<p>'.$this->l('Carts').'</p>
							<p>'.$carts.'</p>
						</td>
						<td class="center">
							<p><i class="icon-chevron-right"></i></p>
							<p>'.round(100 * $fullcarts / max(1, $carts)).' %</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="alert alert-info">
			<p>'.$this->l('Turn your visitors into money:').'</p>
			<p>'.$this->l('Each visitor yields').' <b>'.Tools::displayPrice($ca['ventil']['total'] / max(1, $visitors), $currency).'.</b></p>
			<p>'.$this->l('Each registered visitor yields').' <b>'.Tools::displayPrice($ca['ventil']['total'] / max(1, $customers), $currency).'</b>.</p>
			</p>
		</div>';

		$from = strtotime($employee->stats_date_from.' 00:00:00');
		$to = strtotime($employee->stats_date_to.' 23:59:59');
		$interval = ($to - $from) / 60 / 60 / 24;
		$prop5000 = 5000 / 30 * $interval;

		$this->_html .= '
			<div class="row row-margin-bottom">
				<h4><i class="icon-money"></i> '.$this->l('Payment distribution').'</h4>
				<div class="alert alert-info">'
					.$this->l('The amounts are with taxes, so you can get an estimation of the commission due to the payment method.').'
				</div>
				<form id="cat" action="'.$ru.'#payment" method="post" class="form-horizontal">
					<div class="row row-margin-bottom">
						<label class="control-label col-lg-3">
							'.$this->l('Zone:').'
						</label>
						<div class="col-lg-3">
							<input type="hidden" name="submitIdZone" value="1" />
							<select name="stats_id_zone" onchange="this.form.submit();">
								<option value="0">'.$this->l('-- No filter --').'</option>';
					foreach (Zone::getZones() as $zone)
						$this->_html .= '<option value="'.(int)$zone['id_zone'].'" '.($this->context->cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
					$this->_html .= '
							</select>
						</div>
					</div>
				</form>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Module').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Transactions').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Total').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Average Cart Value').'</span></th>
						</tr>
					</thead>
					<tbody>';
					foreach ($ca['payment'] as $payment)
					$this->_html .= '
						<tr>
							<td class="center">'.$payment['payment_method'].'</td>
							<td class="center">'.(int)$payment['nb'].'</td>
							<td align="right">'.Tools::displayPrice($payment['total'], $currency).'</td>
							<td align="right">'.Tools::displayPrice($payment['cart'], $currency).'</td>
						</tr>';
					$this->_html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-sitemap"></i> '.$this->l('Category distribution').'</h4>
				<form id="cat" action="'.$ru.'#cat" method="post" class="form-horizontal">
					<div class="row row-margin-bottom">
						<label class="control-label col-lg-3">
							'.$this->l('Zone').'
						</label>
						<div class="col-lg-3">
							<input type="hidden" name="submitIdZone" value="1" />
							<select name="stats_id_zone" onchange="this.form.submit();">
								<option value="0">'.$this->l('-- No filter --').'</option>';
					foreach (Zone::getZones() as $zone)
						$this->_html .= '<option value="'.(int)$zone['id_zone'].'" '.($this->context->cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
					$this->_html .= '
							</select>
						</div>
					</div>
				</form>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Category').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Products Sold').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Sales').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('% Count').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('% Sales').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Average price').'</span></th>
						</tr>
					</thead>
					<tbody>';
					foreach ($ca['cat'] as $catrow)
						$this->_html .= '
						<tr>
							<td class="center">'.(empty($catrow['name']) ? $this->l('Unknown') : $catrow['name']).'</td>
							<td class="center">'.$catrow['orderQty'].'</td>
							<td align="right">'.Tools::displayPrice($catrow['orderSum'], $currency).'</td>
							<td class="center">'.number_format((100 * $catrow['orderQty'] / $this->t4), 1, '.', ' ').'%</td>
							<td class="center">'.((int)$ca['ventil']['total'] ? number_format((100 * $catrow['orderSum'] / $ca['ventil']['total']), 1, '.', ' ') : '0').'%</td>
							<td align="right">'.Tools::displayPrice($catrow['priveAvg'], $currency).'</td>
						</tr>';
					$this->_html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-flag"></i> '.$this->l('Language distribution').'</h4>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Language').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Sales').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('%').'</span></th>
							<th class="center" colspan="2"><span class="title_box active">'.$this->l('Growth').'</span></th>
						</tr>
					</thead>
					<tbody>';
			foreach ($ca['lang'] as $ophone => $amount)
			{
				$percent = (int)($ca['langprev'][$ophone]) ? number_format((100 * $amount / $ca['langprev'][$ophone]) - 100, 1, '.', ' ') : '&#x221e;';
				$this->_html .= '
					<tr '.(($percent < 0) ? 'class="alt_row"' : '').'>
						<td class="center">'.$ophone.'</td>
						<td align="right">'.Tools::displayPrice($amount, $currency).'</td>
						<td class="center">'.((int)$ca['ventil']['total'] ? number_format((100 * $amount / $ca['ventil']['total']), 1, '.', ' ').'%' : '-').'</td>
						<td class="center">'.(($percent > 0 OR $percent == '&#x221e;') ? '<img src="../img/admin/arrow_up.png" />' : '<img src="../img/admin/arrow_down.png" /> ').'</td>
						<td class="center">'.(($percent > 0 OR $percent == '&#x221e;') ? '+' : '').$percent.'%</td>
					</tr>';
			}
			$this->_html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-map-marker"></i> '.$this->l('Zone distribution').'</h4>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Zone').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Count').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Total').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('% Count').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('% Sales').'</span></th>
						</tr>
					</thead>
					<tbody>';
			foreach ($ca['zones'] as $zone)
				$this->_html .= '
					<tr>
						<td class="center">'.(isset($zone['name']) ? $zone['name'] : $this->l('Undefined')).'</td>
						<td class="center">'.(int)($zone['nb']).'</td>
						<td align="right">'.Tools::displayPrice($zone['total'], $currency).'</td>
						<td class="center">'.($ca['ventil']['nb'] ? number_format((100 * $zone['nb'] / $ca['ventil']['nb']), 1, '.', ' ') : '0').'%</td>
						<td class="center">'.((int)$ca['ventil']['total'] ? number_format((100 * $zone['total'] / $ca['ventil']['total']), 1, '.', ' ') : '0').'%</td>
					</tr>';
			$this->_html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-money"></i> '.$this->l('Currency distribution').'</h4>
				<form id="cat" action="'.$ru.'#currencies" method="post" class="form-horizontal">
					<div class="row row-margin-bottom">
						<label class="control-label col-lg-3">
							'.$this->l('Zone:').'
						</label>
						<div class="col-lg-3">
							<input type="hidden" name="submitIdZone" value="1" />
							<select name="stats_id_zone" onchange="this.form.submit();">
								<option value="0">'.$this->l('-- No filter --').'</option>';
					foreach (Zone::getZones() as $zone)
						$this->_html .= '<option value="'.(int)$zone['id_zone'].'" '.($this->context->cookie->stats_id_zone == $zone['id_zone'] ? 'selected="selected"' : '').'>'.$zone['name'].'</option>';
					$this->_html .= '
							</select>
						</div>
					</div>
				</form>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Currency').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Count').'</span></th>
							<th align="right"><span class="title_box active">'.$this->l('Sales (converted)').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('% Count').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('% Sales').'</span></th>
						</tr>
					</thead>
					<tbody>';
				foreach ($ca['currencies'] as $currencyRow)
					$this->_html .= '
						<tr>
							<td class="center">'.$currencyRow['name'].'</td>
							<td class="center">'.(int)($currencyRow['nb']).'</td>
							<td align="right">'.Tools::displayPrice($currencyRow['total'], $currency).'</td>
							<td class="center">'.($ca['ventil']['nb'] ? number_format((100 * $currencyRow['nb'] / $ca['ventil']['nb']), 1, '.', ' ') : '0').'%</td>
							<td class="center">'.((int)$ca['ventil']['total'] ? number_format((100 * $currencyRow['total'] / $ca['ventil']['total']), 1, '.', ' ') : '0').'%</td>
						</tr>';
				$this->_html .= '
					</tbody>
				</table>
			</div>
			<div class="row row-margin-bottom">
				<h4><i class="icon-ticket"></i> '.$this->l('Attribute distribution').'</h4>
				<table class="table">
					<thead>
						<tr>
							<th class="center"><span class="title_box active">'.$this->l('Group').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Attribute').'</span></th>
							<th class="center"><span class="title_box active">'.$this->l('Count').'</span></th>
						</tr>
					</thead>
					<tbody>';
				foreach ($ca['attributes'] as $attribut)
					$this->_html .= '
						<tr>
							<td class="center">'.$attribut['gname'].'</td>
							<td class="center">'.$attribut['aname'].'</td>
							<td class="center">'.(int)($attribut['total']).'</td>
						</tr>';
				$this->_html .= '
					</tbody>
				</table>
			</div>';

		return $this->_html;
	}

	private function getRealCA()
	{
		$employee = $this->context->employee;
		$ca = array();

		$where = $join = '';
		if ((int)$this->context->cookie->stats_id_zone)
		{
			$join =  ' LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_invoice = a.id_address LEFT JOIN `'._DB_PREFIX_.'country` co ON co.id_country = a.id_country';
			$where = ' AND co.id_zone = '.(int)$this->context->cookie->stats_id_zone.' ';
		}

		$sql = 'SELECT SUM(od.`product_price` * od.`product_quantity` / o.conversion_rate) as orderSum, SUM(od.product_quantity) as orderQty, cl.name, AVG(od.`product_price` / o.conversion_rate) as priveAvg
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
				LEFT JOIN `'._DB_PREFIX_.'product` p ON p.id_product = od.product_id
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (product_shop.id_category_default = cl.id_category AND cl.id_lang = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
				'.$join.'
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$where.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY product_shop.id_category_default';
		$ca['cat'] = Db::getInstance()->executeS($sql);
		uasort($ca['cat'], 'statsforecast_sort');

		$langValues = '';
		$sql = 'SELECT l.id_lang, l.iso_code
				FROM `'._DB_PREFIX_.'lang` l
				'.Shop::addSqlAssociation('lang', 'l').'
				WHERE l.active = 1';
		$languages = Db::getInstance()->executeS($sql);
		foreach ($languages as $language)
			$langValues .= 'SUM(IF(o.id_lang = '.(int)$language['id_lang'].', total_paid_tax_excl / o.conversion_rate, 0)) as '.pSQL($language['iso_code']).',';
		$langValues = rtrim($langValues, ',');

		if ($langValues)
		{
			$sql = 'SELECT '.$langValues.'
					FROM `'._DB_PREFIX_.'orders` o
					WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o');
			$ca['lang'] = Db::getInstance()->getRow($sql);
			arsort($ca['lang']);

			$sql = 'SELECT '.$langValues.'
					FROM `'._DB_PREFIX_.'orders` o
					WHERE o.valid = 1
						AND ADDDATE(o.`invoice_date`, interval 30 day) BETWEEN \''.$employee->stats_date_from.' 00:00:00\' AND \''.min(date('Y-m-d H:i:s'), $employee->stats_date_to.' 23:59:59').'\'
						'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o');
			$ca['langprev'] = Db::getInstance()->getRow($sql);
		}
		else
		{
			$ca['lang'] = array();
			$ca['langprev'] = array();
		}

		$sql = 'SELECT op.payment_method, SUM(amount / o.conversion_rate) as total, COUNT(*) as nb, AVG(amount / o.conversion_rate) as cart
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'order_payment` op ON o.reference = op.order_reference
				'.$join.'
				WHERE o.valid = 1
				AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
				'.$where.'
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY op.payment_method
				ORDER BY total DESC';
		$ca['payment'] = Db::getInstance()->executeS($sql);

		$sql = 'SELECT z.name, SUM(o.total_paid_tax_excl / o.conversion_rate) as total, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'address` a ON o.id_address_invoice = a.id_address
				LEFT JOIN `'._DB_PREFIX_.'country` c ON c.id_country = a.id_country
				LEFT JOIN `'._DB_PREFIX_.'zone` z ON z.id_zone = c.id_zone
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY c.id_zone
				ORDER BY total DESC';
		$ca['zones'] = Db::getInstance()->executeS($sql);

		$sql = 'SELECT cu.name, SUM(o.total_paid_tax_excl / o.conversion_rate) as total, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'currency` cu ON o.id_currency = cu.id_currency
				'.$join.'
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.$where.'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY o.id_currency
				ORDER BY total DESC';
		$ca['currencies'] = Db::getInstance()->executeS($sql);

		$sql = 'SELECT SUM(total_paid_tax_excl / o.conversion_rate) as total, COUNT(*) AS nb
				FROM `'._DB_PREFIX_.'orders` o
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o');
		$ca['ventil'] = Db::getInstance()->getRow($sql);

		$sql = 'SELECT /*pac.id_attribute,*/ agl.name as gname, al.name as aname, COUNT(*) as total
				FROM '._DB_PREFIX_.'orders o
				LEFT JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
				INNER JOIN '._DB_PREFIX_.'product_attribute_combination pac ON od.product_attribute_id = pac.id_product_attribute
				INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
				INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '.(int)$this->context->language->id.')
				INNER JOIN '._DB_PREFIX_.'attribute_lang al ON (a.id_attribute = al.id_attribute AND al.id_lang = '.(int)$this->context->language->id.')
				WHERE o.valid = 1
					AND o.`invoice_date` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				GROUP BY pac.id_attribute';
		$ca['attributes'] = Db::getInstance()->executeS($sql);

		return $ca;
	}
}

function statsforecast_sort($a, $b)
{
	if ($a['orderSum'] == $b['orderSum'])
		return 0;
	return ($a['orderSum'] > $b['orderSum']) ? -1 : 1;
}
