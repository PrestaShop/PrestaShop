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

class StatsSearch extends ModuleGraph
{
	private $_html = '';
	private $_query = '';
	private $_query2 = '';

	public function __construct()
	{
		$this->name = 'statssearch';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->_query = 'SELECT `keywords`, COUNT(TRIM(`keywords`)) as occurences, MAX(results) as total
				FROM `'._DB_PREFIX_.'statssearch`
				WHERE 1
					'.Shop::addSqlRestriction().'
					AND `date_add` BETWEEN ';

		$this->_query2 = 'GROUP BY `keywords`
				HAVING occurences > 1
				ORDER BY occurences DESC';

		$this->displayName = $this->l('Shop search');
		$this->description = $this->l('Display which keywords have been searched by your store\'s visitors.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('search') || !$this->registerHook('AdminStatsModules'))
			return false;
		return Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'statssearch` (
			id_statssearch INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			id_shop INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
		  	id_shop_group INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			keywords VARCHAR(255) NOT NULL,
			results INT(6) NOT NULL DEFAULT 0,
			date_add DATETIME NOT NULL,
			PRIMARY KEY(id_statssearch)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return (Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'statssearch`'));
	}

	/**
	 * Insert keywords in statssearch table when a search is launched on FO
	 */
	public function hookSearch($params)
	{
		$sql = 'INSERT INTO `'._DB_PREFIX_.'statssearch` (`id_shop`, `id_shop_group`, `keywords`, `results`, `date_add`)
				VALUES ('.(int)$this->context->shop->id.', '.(int)$this->context->shop->id_shop_group.', \''.pSQL($params['expr']).'\', '.(int)$params['total'].', NOW())';
		Db::getInstance()->execute($sql);
	}

	public function hookAdminStatsModules()
	{
		if (Tools::getValue('export'))
			$this->csvExport(array('type' => 'pie'));

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.ModuleGraph::getDateBetween().$this->_query2);
		$this->_html = '
		<div class="panel-heading">
			'.$this->displayName.'
		</div>';
		$table = '
		<table class="table">
			<thead>
				<tr>
					<th><span class="title_box active">'.$this->l('Keywords').'</span></th>
					<th><span class="title_box active">'.$this->l('Occurrences').'</span></th>
					<th><span class="title_box active">'.$this->l('Results').'</span></th>
				</tr>
			</thead>
			<tbody>';

		foreach ($result as $row)
		{
			if (Tools::strlen($row['keywords']) >= Configuration::get('PS_SEARCH_MINWORDLEN'))
				$table .= '<tr>
					<td>'.$row['keywords'].'</td>
					<td>'.$row['occurences'].'</td>
					<td>'.$row['total'].'</td>
				</tr>';
		}
		$table .= '
			</tbody>
		</table>';

		if (count($result))
			$this->_html .= '<div>'.$this->engine(array('type' => 'pie')).'</div>
							<a class="btn btn-default" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1">
								<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'
							</a>'.$table;
		else
			$this->_html .= '<p>'.$this->l('No keywords searched more than once have been found.').'</p>';
		return $this->_html;
	}

	protected function getData($layers)
	{
		$this->_titles['main'] = $this->l('Top 10 keywords');
		$totalResult = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate().$this->_query2);
		$total = 0;
		$total2 = 0;
		foreach ($totalResult as $totalRow)
			$total += $totalRow['occurences'];
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate().$this->_query2.' LIMIT 9');
		foreach ($result as $row)
		{
			if (!$row['occurences'])
				continue;
			$this->_legend[] = $row['keywords'];
			$this->_values[] = $row['occurences'];
			$total2 += $row['occurences'];
		}
		if ($total > $total2)
		{
			$this->_legend[] = $this->l('Others');
			$this->_values[] = $total - $total2;
		}
	}
}