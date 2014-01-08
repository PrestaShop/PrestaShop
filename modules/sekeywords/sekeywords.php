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

class SEKeywords extends ModuleGraph
{
	private $html = '';
	private $_query = '';
	private $_query2 = '';

	public function __construct()
	{
		$this->name = 'sekeywords';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->_query = 'SELECT `keyword`, COUNT(TRIM(`keyword`)) as occurences
				FROM `'._DB_PREFIX_.'sekeyword`
				WHERE '.(Configuration::get('SEK_FILTER_KW') == '' ? '1' : '`keyword` REGEXP \''.pSQL(Configuration::get('SEK_FILTER_KW')).'\'')
					.Shop::addSqlRestriction().
					' AND `date_add` BETWEEN ';

		$this->_query2 = 'GROUP BY TRIM(`keyword`)
				HAVING occurences > '.(int)Configuration::get('SEK_MIN_OCCURENCES').'
				ORDER BY occurences DESC';

		$this->displayName = $this->l('Search engine keywords');
		$this->description = $this->l('Displays which keywords have led visitors to your website.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('AdminStatsModules'))
			return false;
		Configuration::updateValue('SEK_MIN_OCCURENCES', 1);
		Configuration::updateValue('SEK_FILTER_KW', '');
		return Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'sekeyword` (
			id_sekeyword INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			id_shop INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			id_shop_group INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			keyword VARCHAR(256) NOT NULL,
			date_add DATETIME NOT NULL,
			PRIMARY KEY(id_sekeyword)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
	}

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return (Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'sekeyword`'));
	}

	public function hookTop($params)
	{
		if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], Tools::getHttpHost(false, false) == 0))
			return;

		if ($keywords = $this->getKeywords($_SERVER['HTTP_REFERER']))
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'sekeyword` (`keyword`, `date_add`, `id_shop`, `id_shop_group`)
										VALUES (\''.pSQL(Tools::strtolower(trim($keywords))).'\', NOW(), '.(int)$this->context->shop->id.', '.(int)$this->context->shop->id_shop_group.')');
	}

	public function hookAdminStatsModules()
	{
		if (Tools::isSubmit('submitSEK'))
		{
			Configuration::updateValue('SEK_FILTER_KW', trim(Tools::getValue('SEK_FILTER_KW')));
			Configuration::updateValue('SEK_MIN_OCCURENCES', (int)Tools::getValue('SEK_MIN_OCCURENCES'));
			Tools::redirectAdmin('index.php?tab=AdminStats&token='.Tools::getValue('token').'&module='.$this->name);
		}

		if (Tools::getValue('export'))
			$this->csvExport(array('type' => 'pie'));
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.ModuleGraph::getDateBetween().$this->_query2);
		$total = count($result);
		$this->html = '
		<div class="panel-heading">'
			.$this->displayName.'
		</div>
		<h4>'.$this->l('Guide').'</h4>
		<div class="alert alert-warning">
			<h4>'.$this->l('Identify external search engine keywords').'</h4>
			<p>'
				.$this->l('This is one of the most common ways of finding a website through a search engine.').
				$this->l('Identifying the most popular keywords entered by your new visitors allows you to see the products you should put in front if you want to achieve better visibility in search engines.').'
			</p>
			<p>&nbsp;</p>
			<h4>'.$this->l('How does it work?').'</h4>
			<p>'
				.$this->l('When a visitor comes to your website, the web server notes the URL of the site he/she comes from. This module then parses the URL, and if it finds a reference to a known search engine, it finds the keywords in it.').'<br>'.
				$this->l('This module can recognize all the search engines listed in PrestaShop\'s Stats/Search Engine page -- and you can add more!').'<br>'.
				$this->l('IMPORTANT NOTE: in September 2013, Google chose to encrypt its searches queries using SSL. This means all the referer-based tools in the World (including this one) cannot identify Google keywords anymore.').'
			</p>
		</div>
		<p>'.($total == 1 ? sprintf($this->l('%d keyword matches your query.'), $total) : sprintf($this->l('%d keywords match your query.'), $total)).'</p>';
		
		$form = '
		<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post" class="form-horizontal">
			<div class="row row-margin-bottom">
				<label class="control-label col-lg-3">'.$this->l('Filter by keyword').'</label>
				<div class="col-lg-9">
					<input type="text" name="SEK_FILTER_KW" value="'.Tools::htmlentitiesUTF8(Configuration::get('SEK_FILTER_KW')).'" />
				</div>
			</div>
			<div class="row row-margin-bottom">
				<label class="control-label col-lg-3">'.$this->l('And min occurrences').'</label>
				<div class="col-lg-9">
					<input type="text" name="SEK_MIN_OCCURENCES" value="'.(int)Configuration::get('SEK_MIN_OCCURENCES').'" />
				</div>
			</div>
			<div class="row row-margin-bottom">
				<div class="col-lg-9 col-lg-offset-3">
					<button type="submit" class="btn btn-default" name="submitSEK">
						<i class="icon-ok"></i> '.$this->l('Apply').'
					</button>
				</div>
			</div>
		</form>';
		
		if ($result && $total)
		{
			$table = '
			<table class="table">
				<thead>
					<tr>
						<th><span class="title_box active">'.$this->l('Keywords').'</span></th>
						<th><span class="title_box active">'.$this->l('Occurrences').'</span></th>
					</tr>
				</thead>
				<tbody>';
			foreach ($result as $index => $row)
			{
				$keyword =& $row['keyword'];
				$occurences =& $row['occurences'];
				$table .= '<tr><td>'.$keyword.'</td><td>'.$occurences.'</td></tr>';
			}
			$table .= '</tbody></table>';
			$this->html .= '<div>'.$this->engine(array('type' => 'pie')).'</div>
			<a class="btn btn-default" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=language"><<i class="icon-cloud-upload"></i> '.$this->l('CSV Export').'</a>
			'.$form.'<br/>'.$table;
		}
		else
			$this->html .= '<p>'.$form.'<strong>'.$this->l('No keywords').'</strong></p>';

		return $this->html;
	}

	public function getKeywords($url)
	{
		if (!Validate::isAbsoluteUrl($url))
			return false;

		$parsedUrl = parse_url($url);
		if (!isset($parsedUrl['query']) && isset($parsedUrl['fragment']))
			$parsedUrl['query'] = $parsedUrl['fragment'];

		if (!isset($parsedUrl['query']))
			return false;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `server`, `getvar` FROM `'._DB_PREFIX_.'search_engine`');
		foreach ($result as $index => $row)
		{
			$host =& $row['server'];
			$varname =& $row['getvar'];
			if (strstr($parsedUrl['host'], $host))
			{
				$kArray = array();
				preg_match('/[^a-zA-Z&]?'.$varname.'=.*\&'.'/U', $parsedUrl['query'], $kArray);

				if (!isset($kArray[0]) || empty($kArray[0]))
					preg_match('/[^a-zA-Z&]?'.$varname.'=.*$'.'/', $parsedUrl['query'], $kArray);

				if (!isset($kArray[0]) || empty($kArray[0]))
					return false;

				if ($kArray[0][0] == '&' && Tools::strlen($kArray[0]) == 1)
					return false;

				return urldecode(str_replace('+', ' ', ltrim(substr(rtrim($kArray[0], '&'), strlen($varname) + 1), '=')));
			}
		}
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
			$this->_legend[] = $row['keyword'];
			$this->_values[] = $row['occurences'];
			$total2 += $row['occurences'];
		}
		if ($total >= $total2)
		{
			$this->_legend[] = $this->l('Others');
			$this->_values[] = $total - $total2;
		}
	}
}
