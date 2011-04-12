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

class StatsVisits extends ModuleGraph
{
    private $_html = '';
    private $_query = '';
    private $_query2 = '';
    private $_option;

    function __construct()
    {
        $this->name = 'statsvisits';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
			
		parent::__construct();
		
        $this->displayName = $this->l('Visits and Visitors');
        $this->description = $this->l('Display statistics about your visits and visitors.');
    }
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}
	
	public function getTotalVisits()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(c.`id_connections`)
		FROM `'._DB_PREFIX_.'connections` c
		WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween());
	}
	
	public function getTotalGuests()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT COUNT(DISTINCT c.`id_guest`)
		FROM `'._DB_PREFIX_.'connections` c
		WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween());
	}
	
	public function hookAdminStatsModules($params)
	{
		$totalVisits = $this->getTotalVisits();
		$totalGuests = $this->getTotalGuests();
		if (Tools::getValue('export'))
			$this->csvExport(array('layers' =>2, 'type' => 'line', 'option' => 3));
		$this->_html = '
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			<p><center>
				<img src="../img/admin/down.gif" />'.$this->l('A visit corresponds to an internet user coming to your shop. Until the end of their session, only one visit is counted.').'
				'.$this->l('A visitor is an unknown person, who has not registered or logged on, surfing on your shop. A visitor can come and visit your shop many times.').'
			</center></p>
			<div style="margin-top:20px"></div>
			<p>'.$this->l('Total visits:').' '.$totalVisits.'</p>
			<p>'.$this->l('Total visitors:').' '.$totalGuests.'</p>
			'.($totalVisits ? ModuleGraph::engine(array('layers' => 2, 'type' => 'line', 'option' => 3)).'<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p>' : '').'
			
		</fieldset>
		<br class="clear" />
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
				<h2>'.$this->l('Determine the interest of a visit').'</h2>
				'.$this->l('The visitors\' evolution graph strongly resembles the visits\' graph, but provides additional information:').'<br />
				<ul>
					<li>'.$this->l('If this is the case, congratulations, your website is well planned and pleasing.').'</li>
					<li>'.$this->l('Otherwise, the conclusion is not so simple. The problem can be aesthetic or ergonomic, or else the offer is not sufficient. It is also possible that these visitors mistakenly came here without particular interest for your shop; this phenomenon often happens with the search engines.').'</li>
				</ul>
				'.$this->l('This information is mostly qualitative: you have to determine the interest of a disjointed visit.').'<br />
		</fieldset>';
		
		return $this->_html;
	}
	
	public function setOption($option, $layers = 1)
	{
		switch ($option)
		{
			case 3:
				$this->_titles['main'][0] = $this->l('Number of visits and unique visitors');
				$this->_titles['main'][1] = $this->l('Visits');
				$this->_titles['main'][2] = $this->l('Visitors');
				$this->_query[0] = '
					SELECT date_add, COUNT(`date_add`) as total
					FROM `'._DB_PREFIX_.'connections`
					WHERE `date_add` BETWEEN ';
				$this->_query[1] = '
					SELECT date_add, COUNT(DISTINCT `id_guest`) as total
					FROM `'._DB_PREFIX_.'connections`
					WHERE `date_add` BETWEEN ';
				break;
		}
	}
	
	protected function getData($layers)
	{
		$this->setDateGraph($layers, true);
	}
	
	protected function setYearValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 7)');
			foreach ($result AS $row)
				$this->_values[$i][(int)(substr($row['date_add'], 5, 2))] = (int)($row['total']);
		}
	}
	
	protected function setMonthValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 10)');
			foreach ($result AS $row)
				$this->_values[$i][(int)(substr($row['date_add'], 8, 2))] = (int)($row['total']);
		}
	}

	protected function setDayValues($layers)
	{
		for ($i = 0; $i < $layers; $i++)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query[$i].$this->getDate().' GROUP BY LEFT(date_add, 13)');
			foreach ($result AS $row)
				$this->_values[$i][(int)(substr($row['date_add'], 11, 2))] = (int)($row['total']);
		}
	}
}


