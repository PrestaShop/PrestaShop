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

class StatsNewsletter extends ModuleGraph
{
	private $_html = '';
	private $_query = '';
	private $_query2 = '';
	private $_option = '';

	public function __construct()
	{
		$this->name = 'statsnewsletter';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Newsletter');
		$this->description = $this->l('Display the newsletter registrations');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		if (Module::isInstalled('blocknewsletter'))
		{
			$totals = $this->getTotals();
			if (Tools::getValue('export'))
				$this->csvExport(array('type' => 'line', 'layers' => 3));
			$this->_html = '
			<div class="blocStats">
				<h2><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</h2>
				<p>'.$this->l('Registrations from customers:').' '.(int)$totals['customers'].'</p>
				<p>'.$this->l('Registrations from visitors:').' '.(int)$totals['visitors'].'</p>
				<p>'.$this->l('Both:').' '.(int)$totals['both'].'</p>
				<div>'.$this->engine(array('type' => 'line', 'layers' => 3)).'</div>
				<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a></p>
			</div>';
		}
		else
			$this->_html = '<p>'.$this->l('Module Newsletter Block must be installed').'</p>';

		return $this->_html;
	}

	private function getTotals()
	{
		$sql = 'SELECT COUNT(*) as customers
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
					AND `newsletter_date_add` BETWEEN '.ModuleGraph::getDateBetween();
		$result1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);

		$sql = 'SELECT COUNT(*) as visitors
				FROM '._DB_PREFIX_.'newsletter
				WHERE 1
				   '.Shop::addSqlRestriction().'
					AND `newsletter_date_add` BETWEEN '.ModuleGraph::getDateBetween();
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return array('customers' => $result1['customers'], 'visitors' => $result2['visitors'], 'both' => $result1['customers'] + $result2['visitors']);
	}

	protected function getData($layers)
	{
		$this->_titles['main'][0] = $this->l('Newsletter statistics');
		$this->_titles['main'][1] = $this->l('Customers');
		$this->_titles['main'][2] = $this->l('Visitors');
		$this->_titles['main'][3] = $this->l('Both');

		$this->_query = 'SELECT newsletter_date_add
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
					AND `newsletter_date_add` BETWEEN ';

		$this->_query2 = 'SELECT newsletter_date_add
				FROM '._DB_PREFIX_.'newsletter
				WHERE 1
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
					AND `newsletter_date_add` BETWEEN ';
		$this->setDateGraph($layers, true);
	}

	protected function setAllTimeValues($layers)
	{
		$result1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query2.$this->getDate());
		foreach ($result1 as $row)
			$this->_values[0][(int)substr($row['newsletter_date_add'], 0, 4)] += 1;
		if ($result2)
			foreach ($result2 as $row)
				$this->_values[1][(int)substr($row['newsletter_date_add'], 0, 4)] += 1;
		foreach ($this->_values[2] as $key => $zerofill)
			$this->_values[2][$key] = $this->_values[0][$key] + $this->_values[1][$key];
	}

	protected function setYearValues($layers)
	{
		$result1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query2.$this->getDate());
		foreach ($result1 as $row)
			$this->_values[0][(int)substr($row['newsletter_date_add'], 5, 2)] += 1;
		if ($result2)
			foreach ($result2 as $row)
				$this->_values[1][(int)substr($row['newsletter_date_add'], 5, 2)] += 1;
		foreach ($this->_values[2] as $key => $zerofill)
			$this->_values[2][$key] = $this->_values[0][$key] + $this->_values[1][$key];
	}

	protected function setMonthValues($layers)
	{
		$result1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query2.$this->getDate());
		foreach ($result1 as $row)
			$this->_values[0][(int)substr($row['newsletter_date_add'], 8, 2)] += 1;
		if ($result2)
			foreach ($result2 as $row)
				$this->_values[1][(int)substr($row['newsletter_date_add'], 8, 2)] += 1;
		foreach ($this->_values[2] as $key => $zerofill)
			$this->_values[2][$key] = $this->_values[0][$key] + $this->_values[1][$key];
	}

	protected function setDayValues($layers)
	{
		$result1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query2.$this->getDate());
		foreach ($result1 as $row)
			$this->_values[0][(int)substr($row['newsletter_date_add'], 11, 2)] += 1;
		if ($result2)
			foreach ($result2 as $row)
				$this->_values[1][(int)substr($row['newsletter_date_add'], 11, 2)] += 1;
		foreach ($this->_values[2] as $key => $zerofill)
			$this->_values[2][$key] = $this->_values[0][$key] + $this->_values[1][$key];
	}
}


