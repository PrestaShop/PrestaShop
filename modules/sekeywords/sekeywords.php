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

class SEKeywords extends ModuleGraph
{
    private $_html = '';
	private $_query = '';
	private $_query2 = '';

    function __construct()
    {
        $this->name = 'sekeywords';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		
		$this->_query = '
		SELECT sek.`keyword`, COUNT(TRIM(sek.`keyword`)) as occurences
		FROM `'._DB_PREFIX_.'sekeyword` sek
		WHERE '.(Configuration::get('SEK_FILTER_KW') == '' ? '1' : 'sek.`keyword` REGEXP \''.pSQL(Configuration::get('SEK_FILTER_KW')).'\'').'
		AND sek.`date_add` BETWEEN ';
		$this->_query2 = '
		GROUP BY TRIM(sek.`keyword`)
		HAVING occurences > '.(int)Configuration::get('SEK_MIN_OCCURENCES').'
		ORDER BY occurences DESC';

        parent::__construct();
		
        $this->displayName = $this->l('Search engine keywords');
        $this->description = $this->l('Display which keywords have led visitors to your website.');
    }

	function install()
	{
		if (!parent::install() OR !$this->registerHook('top') OR !$this->registerHook('AdminStatsModules'))
			return false;
		Configuration::updateValue('SEK_MIN_OCCURENCES', 1);
		Configuration::updateValue('SEK_FILTER_KW', '');
		return Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'sekeyword` (
			id_sekeyword INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			keyword VARCHAR(256) NOT NULL,
			date_add DATETIME NOT NULL,
			PRIMARY KEY(id_sekeyword)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8');
	}
	
    function uninstall()
    {
        if (!parent::uninstall())
			return false;
		return (Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'sekeyword`'));
    }
	
	function hookTop($params)
	{
		if (!isset($_SERVER['HTTP_REFERER']) OR strstr($_SERVER['HTTP_REFERER'], Tools::getHttpHost(false, false)))
			return;
		if (!Validate::isAbsoluteUrl($_SERVER['HTTP_REFERER']))
			return;
		if ($keywords = $this->getKeywords($_SERVER['HTTP_REFERER']))
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'sekeyword` (`keyword`,`date_add`)
			VALUES (\''.pSQL(Tools::strtolower(trim($keywords))).'\',\''.pSQL(date('Y-m-d H:i:s')).'\')');
	}
	
	function hookAdminStatsModules()
	{
		if (Tools::isSubmit('submitSEK'))
		{
			Configuration::updateValue('SEK_FILTER_KW', trim(Tools::getValue('SEK_FILTER_KW')));
			Configuration::updateValue('SEK_MIN_OCCURENCES', (int)Tools::getValue('SEK_MIN_OCCURENCES'));
			Tools::redirectAdmin('index.php?tab=AdminStats&token='.Tools::getValue('token').'&module='.$this->name);
		}
		if (Tools::getValue('export'))
				$this->csvExport(array('type' => 'pie'));
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query.ModuleGraph::getDateBetween().$this->_query2);
		$total = count($result);
		$this->_html = '<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
		'.$total.' '.($total == 1 ? $this->l('keyword matches your query.') : $this->l('keywords match your query.')).'<div class="clear">&nbsp;</div>';
		if ($result AND count($result))
		{
			$table = '
			<div style="overflow-y: scroll; height: 600px;">
			<table class="table" border="0" cellspacing="0" cellspacing="0">
			<thead>
				<tr><th style="width:400px;">'.$this->l('Keywords').'</th>
				<th style="width:50px; text-align: right">'.$this->l('Occurrences').'</th></tr>
			</thead><tbody>';
			foreach ($result as $index => $row)
			{
				$keyword =& $row['keyword'];
				$occurences =& $row['occurences'];
				$table .= '<tr><td>'.$keyword.'</td><td style="text-align: right">'.$occurences.'</td></tr>';
			}
			$table .= '</tbody></table></div>';
			$this->_html .= '<center>'.ModuleGraph::engine(array('type' => 'pie')).'</center>
			<br class="clear" />
			<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=language"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br class="clear" />
			<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
				'.$this->l('Filter by keyword').' <input type="text" name="SEK_FILTER_KW" value="'.Tools::htmlentitiesUTF8(Configuration::get('SEK_FILTER_KW')).'" />
				'.$this->l('and min occurrences').' <input type="text" name="SEK_MIN_OCCURENCES" value="'.(int)(Configuration::get('SEK_MIN_OCCURENCES')).'" />
				<input type="submit" class="button" name="submitSEK" value="'.$this->l('   Apply   ').'" />
			</form>
			<br class="clear" />'.$table;
		}
		else
			$this->_html .= '<p><strong>'.$this->l('No keywords').'</strong></p>';

		$this->_html .= '</fieldset><br class="clear" />
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
			<h2>'.$this->l('Identify external search engines\' keywords').'</h2>
			<p>'.$this->l('One of the most common ways of finding a website through a search engine. Identifying the most popular keywords entered by your new visitors allows you to see which products you should put in front if you want to attract more visitors and potential customers.').'</p><br />
			<h3>'.$this->l('How does it work?').'</h2>
			<p>'.$this->l('When a visitor comes to your website, the server notes their previous location. This module parses the URL and finds the keywords in it. Currently, it manages the following search engines:').'<b> Google, AOL, Yandex, Ask, NHL, Yahoo, Baidu, Lycos, Exalead, Live, Voila</b> '.$this->l('and').' <b>Altavista</b>. '.$this->l('Soon it will be possible to dynamically add new search engines and contribute to this module.').'</p><br />
		</fieldset>';
		return $this->_html;
	}
	
	function getKeywords($url)
	{
		if (!Validate::isAbsoluteUrl($url))
			return false;
		$parsedUrl = parse_url($url);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT `server`, `getvar` FROM `'._DB_PREFIX_.'search_engine`');
		foreach ($result as $index => $row)
		{
			$host =& $row['server'];
			$varname =& $row['getvar'];
			if (strstr($parsedUrl['host'], $host))
			{
				$kArray = array();
				if (!isset($parsedUrl['query']))
					return false;
				preg_match('/[^a-z]'.$varname.'=.+\&'.'/U', $parsedUrl['query'], $kArray);
				if (!isset($kArray[0]) OR empty($kArray[0]))
					preg_match('/[^a-z]'.$varname.'=.+$'.'/', $parsedUrl['query'], $kArray);
				if (!isset($kArray[0]) OR empty($kArray[0]))
					return false;
				$kString = urldecode(str_replace('+', ' ', ltrim(substr(rtrim($kArray[0], '&'), strlen($varname) + 1), '=')));
				return $kString;
			}
		}
	}
	
	protected function getData($layers)
	{
		$this->_titles['main'] = $this->l('10 first keywords');
		$totalResult = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query.$this->getDate().$this->_query2);
		$total = 0;
		$total2 = 0;
		foreach ($totalResult as $totalRow)
			$total += $totalRow['occurences'];
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($this->_query.$this->getDate().$this->_query2.' LIMIT 9');
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


