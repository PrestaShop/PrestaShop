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

class StatsCheckUp extends Module
{
	private $html = '';

	public function __construct()
	{
		$this->name = 'statscheckup';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Catalog evaluation');
		$this->description = $this->l('Quick evaluation of your catalog quality.');
	}

	public function install()
	{
		$confs = array(
			'CHECKUP_DESCRIPTIONS_LT'=>100,
			'CHECKUP_DESCRIPTIONS_GT'=>400,
			'CHECKUP_IMAGES_LT'=>1,
			'CHECKUP_IMAGES_GT'=>2,
			'CHECKUP_SALES_LT'=>1,
			'CHECKUP_SALES_GT'=>2,
			'CHECKUP_STOCK_LT'=>1,
			'CHECKUP_STOCK_GT'=>3
		);
		foreach ($confs as $confname => $confdefault)
			if (!Configuration::get($confname))
				Configuration::updateValue($confname, (int)$confdefault);
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules()
	{
		if (Tools::isSubmit('submitCheckup'))
		{
			$confs = array(
				'CHECKUP_DESCRIPTIONS_LT',
				'CHECKUP_DESCRIPTIONS_GT',
				'CHECKUP_IMAGES_LT',
				'CHECKUP_IMAGES_GT',
				'CHECKUP_SALES_LT',
				'CHECKUP_SALES_GT',
				'CHECKUP_STOCK_LT',
				'CHECKUP_STOCK_GT'
			);
			foreach ($confs as $confname)
				Configuration::updateValue($confname, (int)Tools::getValue($confname));
			echo '<div class="conf confirm"> '.$this->l('Configuration updated').'</div>';
		}

		if (Tools::isSubmit('submitCheckupOrder'))
		{
			$this->context->cookie->checkup_order = (int)Tools::getValue('submitCheckupOrder');
			echo '<div class="conf confirm"> '.$this->l('Configuration updated').'</div>';
		}

		if (!isset($this->context->cookie->checkup_order))
			$this->context->cookie->checkup_order = 1;

		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$employee = Context::getContext()->employee;
		$prop30 = ((strtotime($employee->stats_date_to.' 23:59:59') - strtotime($employee->stats_date_from.' 00:00:00')) / 60 / 60 / 24) / 30;

		// Get languages
		$sql = 'SELECT l.*
				FROM '._DB_PREFIX_.'lang l'
				.Shop::addSqlAssociation('lang', 'l');
		$languages = $db->executeS($sql);

		$arrayColors = array(
			0 => '<img src="../modules/'.$this->name.'/red.png" alt="'.$this->l('Bad').'" />',
			1 => '<img src="../modules/'.$this->name.'/orange.png" alt="'.$this->l('Average').'" />',
			2 => '<img src="../modules/'.$this->name.'/green.png" alt="'.$this->l('Good').'" />'
		);
		$tokenProducts = Tools::getAdminToken('AdminProducts'.(int)(Tab::getIdFromClassName('AdminProducts')).(int)Context::getContext()->employee->id);
		$divisor = 4;
		$totals = array('products' => 0, 'active' => 0, 'images' => 0, 'sales' => 0, 'stock' => 0);
		foreach ($languages as $language)
		{
			$divisor++;
			$totals['description_'.$language['iso_code']] = 0;
		}

		$orderBy = 'p.id_product';
		if ($this->context->cookie->checkup_order == 2)
			$orderBy = 'pl.name';
		else if ($this->context->cookie->checkup_order == 3)
			$orderBy = 'nbSales DESC';

		// Get products stats
		$sql = 'SELECT p.id_product, product_shop.active, pl.name, (
					SELECT COUNT(*)
					FROM '._DB_PREFIX_.'image i
					'.Shop::addSqlAssociation('image', 'i').'
					WHERE i.id_product = p.id_product
				) as nbImages, (
					SELECT SUM(od.product_quantity)
					FROM '._DB_PREFIX_.'orders o
					LEFT JOIN '._DB_PREFIX_.'order_detail od ON o.id_order = od.id_order
					WHERE od.product_id = p.id_product
						AND o.invoice_date BETWEEN '.ModuleGraph::getDateBetween().'
						'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
				) as nbSales,
				IFNULL(stock.quantity, 0) as stock
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl
					ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('pl').')
				ORDER BY '.$orderBy;
		$result = $db->executeS($sql);

		if (!$result)
			return $this->l('No product was found.');

		$arrayConf = array(
			'DESCRIPTIONS' => array('name' => $this->l('Descriptions'), 'text' => $this->l('Chars (without HTML)')),
			'IMAGES' => array('name' => $this->l('Images'), 'text' => $this->l('Images')),
			'SALES' => array('name' => $this->l('Sales'), 'text' => $this->l('orders / month')),
			'STOCK' => array('name' => $this->l('Available quantity for sale'), 'text' => $this->l('Items'))
		);

		$this->html = '
		<style type="text/css">
			form.checkup input[type=text] {width:30px}
			form.checkup div {margin-top:20px}
			table.checkup th {text-align:center}
			table.checkup td {padding:5px 10px}
			table.checkup2 td {text-align:right}
		</style>
		<div class="blocStats">
		<form action="'.AdminController::$currentIndex.'&token='.Tools::safeOutput(Tools::getValue('token')).'&module='.$this->name.'" method="post" class="checkup">
			<table class="table checkup" border="0" cellspacing="0" cellspacing="0">
				<tr>
					<th></th>
					<th>'.$arrayColors[0].' '.$this->l('Not enough').'</th>
					<th>'.$arrayColors[2].' '.$this->l('Alright').'</th>
				</tr>';
			foreach ($arrayConf as $conf => $translations)
				$this->html .= '<tr>
					<th>'.$translations['name'].'</th>
					<td>'.$this->l('Less than').'
						 <input type="text" name="CHECKUP_'.$conf.'_LT"
						 	value="'.Tools::safeOutput(Tools::getValue('CHECKUP_'.$conf.'_LT', Configuration::get('CHECKUP_'.$conf.'_LT'))).'" /> '.$translations['text'].'
					</td>
					<td>'.$this->l('Greater than').'
						 <input type="text" name="CHECKUP_'.$conf.'_GT"
						 	value="'.Tools::safeOutput(Tools::getValue('CHECKUP_'.$conf.'_GT', Configuration::get('CHECKUP_'.$conf.'_GT'))).'" /> '.$translations['text'].'
					</td>
				</tr>';
			$this->html .= '</table>
			<div><input type="submit" name="submitCheckup" class="button" value="'.$this->l('   Save   ').'" /></div>
		</form>
		</div>
		<br />
		<div class="blocStats">
		<form action="'.AdminController::$currentIndex.'&token='.Tools::safeOutput(Tools::getValue('token')).'&module='.$this->name.'" method="post">
			'.$this->l('Order by').'
			<select name="submitCheckupOrder" onchange="this.form.submit();" style="width:100px">
				<option value="1">'.$this->l('ID').'</option>
				<option value="2" '.($this->context->cookie->checkup_order == 2 ? 'selected="selected"' : '').'>'.$this->l('Name').'</option>
				<option value="3" '.($this->context->cookie->checkup_order == 3 ? 'selected="selected"' : '').'>'.$this->l('Sales').'</option>
			</select>
		</form>
		<br />
		<table class="table checkup2" border="0" cellspacing="0" cellspacing="0">
			<tr>
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Item').'</th>
				<th>'.$this->l('Active').'</th>';
		foreach ($languages as $language)
			$this->html .= '<th>'.$this->l('Desc.').' ('.strtoupper($language['iso_code']).')</th>';
		$this->html .= '
				<th>'.$this->l('Images').'</th>
				<th>'.$this->l('Sales').'</th>
				<th>'.$this->l('Available quantity for sale').'</th>
				<th>'.$this->l('Global').'</th>
			</tr>';
		foreach ($result as $row)
		{
			$totals['products']++;
			$scores = array(
				'active' => ($row['active'] ? 2 : 0),
				'images' => ($row['nbImages'] < Configuration::get('CHECKUP_IMAGES_LT') ? 0 : ($row['nbImages'] > Configuration::get('CHECKUP_IMAGES_GT') ? 2 : 1)),
				'sales' => (($row['nbSales'] * $prop30 < Configuration::get('CHECKUP_SALES_LT')) ? 0 : (($row['nbSales'] * $prop30 > Configuration::get('CHECKUP_SALES_GT')) ? 2 : 1)),
				'stock' => (($row['stock'] < Configuration::get('CHECKUP_STOCK_LT')) ? 0 : (($row['stock'] > Configuration::get('CHECKUP_STOCK_GT')) ? 2 : 1)),
			);
			$totals['active'] += (int)$scores['active'];
			$totals['images'] += (int)$scores['images'];
			$totals['sales'] += (int)$scores['sales'];
			$totals['stock'] += (int)$scores['stock'];
			$descriptions = $db->executeS('
				SELECT l.iso_code, pl.description
				FROM '._DB_PREFIX_.'product_lang pl
				LEFT JOIN '._DB_PREFIX_.'lang l
					ON pl.id_lang = l.id_lang
				WHERE id_product = '.(int)$row['id_product'].Shop::addSqlRestrictionOnLang('pl'));
			foreach ($descriptions as $description)
			{
				$row['desclength_'.$description['iso_code']] = Tools::strlen(strip_tags($description['description']));
				$scores['description_'.$description['iso_code']] = ($row['desclength_'.$description['iso_code']] < Configuration::get('CHECKUP_DESCRIPTIONS_LT') ? 0 : ($row['desclength_'.$description['iso_code']] > Configuration::get('CHECKUP_DESCRIPTIONS_GT') ? 2 : 1));
				$totals['description_'.$description['iso_code']] += $scores['description_'.$description['iso_code']];
			}
			$scores['average'] = array_sum($scores) / $divisor;
			$scores['average'] = ($scores['average'] < 1 ? 0 : ($scores['average'] > 1.5 ? 2 : 1));

			$this->html .= '<tr>
				<td>'.$row['id_product'].'</td>
				<td style="text-align:left"><a href="index.php?tab=AdminProducts&updateproduct&id_product='.$row['id_product'].'&token='.$tokenProducts.'">'.Tools::substr($row['name'], 0, 42).'</a></td>
				<td>'.$arrayColors[$scores['active']].'</td>';
			foreach ($languages as $language)
				if (isset($row['desclength_'.$language['iso_code']]))
					$this->html .= '<td>'.(int)$row['desclength_'.$language['iso_code']].' '.$arrayColors[$scores['description_'.$language['iso_code']]].'</td>';
				else
					$this->html .= '<td>0 '.$arrayColors[0].'</td>';
			$this->html .= '
				<td>'.(int)$row['nbImages'].' '.$arrayColors[$scores['images']].'</td>
				<td>'.(int)$row['nbSales'].' '.$arrayColors[$scores['sales']].'</td>
				<td>'.(int)$row['stock'].' '.$arrayColors[$scores['stock']].'</td>
				<td>'.$arrayColors[$scores['average']].'</td>
			</tr>';
		}

		$totals['active'] = $totals['active'] / $totals['products'];
		$totals['active'] = ($totals['active'] < 1 ? 0 : ($totals['active'] > 1.5 ? 2 : 1));
		$totals['images'] = $totals['images'] / $totals['products'];
		$totals['images'] = ($totals['images'] < 1 ? 0 : ($totals['images'] > 1.5 ? 2 : 1));
		$totals['sales'] = $totals['sales'] / $totals['products'];
		$totals['sales'] = ($totals['sales'] < 1 ? 0 : ($totals['sales'] > 1.5 ? 2 : 1));
		$totals['stock'] = $totals['stock'] / $totals['products'];
		$totals['stock'] = ($totals['stock'] < 1 ? 0 : ($totals['stock'] > 1.5 ? 2 : 1));
		foreach ($languages as $language)
		{
			$totals['description_'.$language['iso_code']] = $totals['description_'.$language['iso_code']] / $totals['products'];
			$totals['description_'.$language['iso_code']] = ($totals['description_'.$language['iso_code']] < 1 ? 0 : ($totals['description_'.$language['iso_code']] > 1.5 ? 2 : 1));
		}
		$totals['average'] = array_sum($totals) / $divisor;
		$totals['average'] = ($totals['average'] < 1 ? 0 : ($totals['average'] > 1.5 ? 2 : 1));

		$this->html .= '
			<tr>
				<th colspan="2"></th>
				<th>'.$this->l('Active').'</th>';
		foreach ($languages as $language)
			$this->html .= '<th>'.$this->l('Desc.').' ('.strtoupper($language['iso_code']).')</th>';
		$this->html .= '
				<th>'.$this->l('Images').'</th>
				<th>'.$this->l('Sales').'</th>
				<th>'.$this->l('Available quantity for sale').'</th>
				<th>'.$this->l('Global').'</th>
			</tr>
			<tr>
				<td colspan="2"></td>
				<td>'.$arrayColors[$totals['active']].'</td>';
		foreach ($languages as $language)
			$this->html .= '<td>'.$arrayColors[$totals['description_'.$language['iso_code']]].'</td>';
		$this->html .= '
				<td>'.$arrayColors[$totals['images']].'</td>
				<td>'.$arrayColors[$totals['sales']].'</td>
				<td>'.$arrayColors[$totals['stock']].'</td>
				<td>'.$arrayColors[$totals['average']].'</td>
			</tr>
		</table>
		</div>
		<div class="clear">&nbsp;</div>';

		return $this->html;
	}
}