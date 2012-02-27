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

class StatsRegistrations extends ModuleGraph
{
	private $_html = '';
	private $_query = '';

	function __construct()
	{
		$this->name = 'statsregistrations';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
			
		parent::__construct();
		
		$this->displayName = $this->l('Customer accounts');
		$this->description = $this->l('Display the progress of customer registration.');
	}

	/**
	 * Called during module installation
	 */
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}

	/**
	 * @return int Get total of registration in date range
	 */
	public function getTotalRegistrations()
	{
		$sql = 'SELECT COUNT(`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer`
				WHERE `date_add` BETWEEN '.ModuleGraph::getDateBetween().'
				'.Shop::addSqlRestriction(Shop::SHARE_ORDER);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return isset($result['total']) ? $result['total'] : 0;
	}

	/**
	 * @return int Get total of blocked visitors during registration process
	 */
	public function getBlockedVisitors()
	{
		$sql = 'SELECT COUNT(DISTINCT c.`id_guest`) as blocked
				FROM `'._DB_PREFIX_.'page_type` pt
				LEFT JOIN `'._DB_PREFIX_.'page` p ON p.id_page_type = pt.id_page_type
				LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON p.id_page = cp.id_page
				LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_connections = cp.id_connections
				LEFT JOIN `'._DB_PREFIX_.'guest` g ON c.id_guest = g.id_guest
				WHERE pt.name = "authentication"
					'.Shop::addSqlRestriction(false, 'c').'
					AND (g.id_customer IS NULL OR g.id_customer = 0)
					AND c.`date_add` BETWEEN '.ModuleGraph::getDateBetween();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return $result['blocked'];
	}

	public function getFirstBuyers()
	{
		$sql = 'SELECT COUNT(DISTINCT o.`id_customer`) as buyers
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'guest` g ON o.id_customer = g.id_customer
				LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
				WHERE o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND ABS(TIMEDIFF(o.date_add, c.date_add)+0) < 120000';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return $result['buyers'];
	}
		
	public function hookAdminStatsModules($params)
	{
		$totalRegistrations = $this->getTotalRegistrations();
		$totalBlocked = $this->getBlockedVisitors();
		$totalBuyers = $this->getFirstBuyers();
		if (Tools::getValue('export'))
			$this->csvExport(array('layers' => 0, 'type' => 'line'));
		$this->_html = '
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
		<ul>
			<li>
				'.$this->l('Visitors who have stopped at the registering step:').' <span class="totalStats">'.(int)($totalBlocked).($totalRegistrations ? ' ('.number_format(100*$totalBlocked/($totalRegistrations+$totalBlocked), 2).'%)' : '').'</span><li/>
				'.$this->l('Visitors who have placed an order directly after registration:').' <span class="totalStats">'.(int)($totalBuyers).($totalRegistrations ? ' ('.number_format(100*$totalBuyers/($totalRegistrations), 2).'%)' : '').'</span>
			<li>'.$this->l('Total customer accounts:').' <span class="totalStats">'.$totalRegistrations.'</span></li>
			</ul>
			<div>'.$this->engine(array('type' => 'line')).'</div>
			<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a></p>
		</div><br />
		<div class="blocStats"><h2 class="icon-guide"><span></span>'.$this->l('Guide').'</legend>
			<h2>'.$this->l('Number of customer accounts created').'</h2>
			<p>'.$this->l('The total number of accounts created is not in itself important information. However, it is beneficial to analyze the number created over time. This will indicate whether or not things are on the right track.').'</p>
			<br /><h3>'.$this->l('How to act on the registrations\' evolution?').'</h3>
			<p>
				'.$this->l('If you let your shop run without changing anything, the number of customer registrations should stay stable or may slightly decline.').'
				'.$this->l('A significant increase or decrease shows that there has probably been a change to your shop.Therefore, you have to identify it in order to backtrack if this change makes the number of registrations decrease, or continue with it if it increases registration.').'<br />
				'.$this->l('Here is summary of what may affect the creation of customer accounts:').'
				<ul>
					<li>'.$this->l('An advertising campaign can attract a greater number of visitors. An increase in customer accounts will ensue, which will depend on their "quality". Well-targeted advertising can be more effective than large-scale advertising.').'</li>
					<li>'.$this->l('Specials, sales, or contests draw greater attention and curiosity, not only keeping your shop lively but also increasing its traffic. This way, you can push impulsive buyers to take the plunge.').'</li>
					<li>'.$this->l('Design and user-friendliness are more important than ever: an ill-chosen or hard-to-follow graphical theme can turn off visitors. You should strike the right balance between an innovative design and letting visitors move around easily. Proper spelling and clarity also inspire more customer confidence in your shop.').'</li>
				</ul>
			</p><br />
		</div>';
		return $this->_html;
	}
	
	protected function getData($layers)
	{
		$this->_query = '
			SELECT `date_add`
			FROM `'._DB_PREFIX_.'customer`
			WHERE 1
				'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
				AND `date_add` BETWEEN';
		$this->_titles['main'] = $this->l('Number of customer accounts created');
		$this->setDateGraph($layers, true);
	}
	
	protected function setAllTimeValues($layers)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		foreach ($result AS $row)
			$this->_values[(int)(substr($row['date_add'], 0, 4))]++;
	}
	
	protected function setYearValues($layers)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		foreach ($result AS $row)
		{
			$mounth = (int)substr($row['date_add'], 5, 2);
			if (!isset($this->_values[$mounth]))
				$this->_values[$mounth] = 0;
			$this->_values[$mounth]++;
		}
	}
	
	protected function setMonthValues($layers)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		foreach ($result AS $row)
			$this->_values[(int)(substr($row['date_add'], 8, 2))]++;
	}

	protected function setDayValues($layers)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate());
		foreach ($result AS $row)
			$this->_values[(int)(substr($row['date_add'], 11, 2))]++;
	}
}


