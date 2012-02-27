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

class StatsVisits extends ModuleGraph
{
	private $html = '';
	private $_query = '';
	private $_query2 = '';
	private $_option;

	public function __construct()
	{
		$this->name = 'statsvisits';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Visits and Visitors');
		$this->description = $this->l('Display statistics about your visits and visitors.');
	}

	public function install()
	{
		return parent::install() && $this->registerHook('AdminStatsModules');
	}

	public function getTotalVisits()
	{
		$sql = 'SELECT COUNT(c.`id_connections`)
				FROM `'._DB_PREFIX_.'connections` c
				WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(false, 'c');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getTotalGuests()
	{
		$sql = 'SELECT COUNT(DISTINCT c.`id_guest`)
				FROM `'._DB_PREFIX_.'connections` c
				WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(false, 'c');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function hookAdminStatsModules($params)
	{
		$graphParams = array(
			'layers' => 	2,
			'type' => 		'line',
			'option' => 	3,
		);

		$totalVisits = $this->getTotalVisits();
		$totalGuests = $this->getTotalGuests();
		if (Tools::getValue('export'))
			$this->csvExport(array('layers' => 2, 'type' => 'line', 'option' => 3));
		$this->html = '
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
			<p>
				<img src="../img/admin/down.gif" />'.$this->l('A visit corresponds to an internet user coming to your shop. Until the end of their session, only one visit is counted.').'
				'.$this->l('A visitor is an unknown person, who has not registered or logged on, surfing on your shop. A visitor can come and visit your shop many times.').'
			</p>
			<div style="margin-top:20px"></div>
			<p>'.$this->l('Total visits:').' <span class="totalStats">'.$totalVisits.'</span></p>
			<p>'.$this->l('Total visitors:').' <span class="totalStats">'.$totalGuests.'</span></p>
			'.($totalVisits ? $this->engine($graphParams).'<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a></p>' : '').'
		</div>
		<br />
		<div class="blocStats"><h2 class="icon-guide"><span></span>'.$this->l('Guide').'</h2>
				<h2>'.$this->l('Determine the interest of a visit').'</h2>
				'.$this->l('The visitors\' evolution graph strongly resembles the visits\' graph, but provides additional information:').'<br />
				<ul>
					<li>'.$this->l('If this is the case, congratulations, your website is well planned and pleasing.').'</li>
					<li>'.$this->l('Otherwise, the conclusion is not so simple. The problem can be aesthetic or ergonomic, or else the offer is not sufficient. It is also possible that these visitors mistakenly came here without particular interest for your shop; this phenomenon often happens with the search engines.').'</li>
				</ul>
				'.$this->l('This information is mostly qualitative: you have to determine the interest of a disjointed visit.').'<br />
		</div>';

		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		switch ($option)
		{
			case 3:
				$this->_titles['main'][0] = $this->l('Number of visits and unique visitors');
				$this->_titles['main'][1] = $this->l('Visits');
				$this->_titles['main'][2] = $this->l('Visitors');
				$this->_query[0] = 'SELECT date_add, COUNT(`date_add`) as total
					FROM `'._DB_PREFIX_.'connections`
					WHERE 1
						'.Shop::addSqlRestriction().'
						AND `date_add` BETWEEN ';
				$this->_query[1] = 'SELECT date_add, COUNT(DISTINCT `id_guest`) as total
					FROM `'._DB_PREFIX_.'connections`
					WHERE 1
						'.Shop::addSqlRestriction().'
						AND `date_add` BETWEEN ';
				break;
		}
	}

	protected function getData($layers)
	{
		$this->setDateGraph($layers, true);
	}

	protected function setAllTimeValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 4)');
			foreach ($result as $row)
				$this->_values[$i][(int)substr($row['date_add'], 0, 4)] = (int)$row['total'];
		}
	}

	protected function setYearValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 7)');
			foreach ($result as $row)
				$this->_values[$i][(int)substr($row['date_add'], 5, 2)] = (int)$row['total'];
		}
	}

	protected function setMonthValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 10)');
			foreach ($result as $row)
				$this->_values[$i][(int)substr($row['date_add'], 8, 2)] = (int)$row['total'];
		}
	}

	protected function setDayValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 13)');
			foreach ($result as $row)
				$this->_values[$i][(int)substr($row['date_add'], 11, 2)] = (int)$row['total'];
		}
	}
}


