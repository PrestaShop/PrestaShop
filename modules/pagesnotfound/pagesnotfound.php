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

class Pagesnotfound extends Module
{
    private $_html = '';

    function __construct()
    {
        $this->name = 'pagesnotfound';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';

        parent::__construct();
		
        $this->displayName = $this->l('Pages not found');
        $this->description = $this->l('Display the pages requested by your visitors but not found.');
    }

	function install()
	{
		if (!parent::install() OR !$this->registerHook('top') OR !$this->registerHook('AdminStatsModules'))
			return false;
		return Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'pagenotfound` (
		  id_pagenotfound INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
		  request_uri VARCHAR(256) NOT NULL,
		  http_referer VARCHAR(256) NOT NULL,
		  date_add DATETIME NOT NULL,
		  PRIMARY KEY(id_pagenotfound),
		  INDEX (`date_add`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
	}
	
    function uninstall()
    {
        return (parent::uninstall() AND Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'pagenotfound`'));
    }
	
	private function getPages()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT http_referer, request_uri, COUNT(*) as nb
		FROM `'._DB_PREFIX_.'pagenotfound` p
		WHERE p.date_add BETWEEN '.ModuleGraph::getDateBetween().'
		GROUP BY http_referer, request_uri');

		$pages = array();
		foreach ($result as $row)
		{
			$row['http_referer'] = parse_url($row['http_referer'], PHP_URL_HOST).parse_url($row['http_referer'], PHP_URL_PATH);
			if (!isset($row['http_referer']) OR empty($row['http_referer']))
				$row['http_referer'] = '--';
			if (!isset($pages[$row['request_uri']]))
				$pages[$row['request_uri']] = array('nb' => 0);
			$pages[$row['request_uri']][$row['http_referer']] = $row['nb'];
			$pages[$row['request_uri']]['nb'] += $row['nb'];
		}
		uasort($pages, 'pnfSort');
		return $pages;
	}
	
    function hookAdminStatsModules()
    {
		if (Tools::isSubmit('submitTruncatePNF'))
		{
			Db::getInstance()->Execute('TRUNCATE `'._DB_PREFIX_.'pagenotfound`');
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" /> '.$this->l('Pages not found has been emptied.').'</div>';
		}
	
        $this->_html .= '<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>';
		if (!file_exists(dirname(__FILE__).'/../../.htaccess'))
			$this->_html .= '<div class="warning warn">'.$this->l('You <b>must</b> use a .htaccess file to redirect 404 errors to the page "404.php"').'</div>';
		
		$pages = $this->getPages();
		if (sizeof($pages))
		{
			$this->_html .= '
			<table class="table" cellpadding="0" cellspacing="0">
				<tr>
					<th width="200">'.$this->l('Page').'</th>
					<th width="500">'.$this->l('Referrer').'</th>
					<th>'.$this->l('Counter').'</th>
				</tr>';
			foreach ($pages as $ru => $hrs)
				foreach ($hrs as $hr => $counter)
					if ($hr != 'nb')
						$this->_html .= '
						<tr>
							<td><a href="'.$ru.'-admin404">'.wordwrap($ru, 30, '<br />', true).'</a></td>
							<td><a href="'.Tools::getProtocol().$hr.'">'.wordwrap($hr, 40, '<br />', true).'</a></td>
							<td align="right">'.$counter.'</td>
						</tr>';
			$this->_html .= '
			</table>';
		}
		else
			$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" /> '.$this->l('No pages registered').'</div>';
			
		$this->_html .= '</fieldset>';
		if (sizeof($pages))
			$this->_html .= '<div class="clear">&nbsp;</div>
			<fieldset class="width3"><legend><img src="../img/admin/delete.gif" /> '.$this->l('Empty database').'</legend>
				<form action="'.Tools::htmlEntitiesUtf8($_SERVER['REQUEST_URI']).'" method="post"><input type="submit" class="button" name="submitTruncatePNF" value="'.$this->l('Empty ALL pages not found').'"></form>
			</fieldset>';
		$this->_html .= '<div class="clear">&nbsp;</div>
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
			<h2>'.$this->l('404 errors').'</h2>
			<p>'.$this->l('A 404 error is an HTTP error code which means that the file requested by the user cannot be found. In your case it means that one of your visitors entered a wrong URL in the address bar or that you or another website has a dead link. When it is available, the referrer is shown so you can find the page which contains the dead link. If not, it means generally that it is a direct access, so someone may have bookmarked a link which doesn\'t exist anymore.').'</p>
			<h3>'.$this->l('How to catch these errors?').'</h3>
			<p>'.$this->l('If your webhost supports the <i>.htaccess</i> file, you can create it in the root directory of PrestaShop and insert the following line inside:').' <i>ErrorDocument 404 '.__PS_BASE_URI__.'404.php</i>. '.$this->l('A user requesting a page which doesn\'t exist will be redirected to the page.').' <i>'.__PS_BASE_URI__.'404.php</i>. '.$this->l('This module logs the accesses to this page: the page requested, the referrer and the number of times that it occurred.').'</p><br />
		</fieldset>';

        return $this->_html;
    }
	
	function hookTop($params)
	{
		if (strstr($_SERVER['REQUEST_URI'], '404.php') AND isset($_SERVER['REDIRECT_URL']))
			$_SERVER['REQUEST_URI'] = $_SERVER['REDIRECT_URL'];
		if (!Validate::isUrl($request_uri = $_SERVER['REQUEST_URI']) OR strstr($_SERVER['REQUEST_URI'], '-admin404'))
			return;
		if (strstr($_SERVER['PHP_SELF'], '404.php') AND !strstr($_SERVER['REQUEST_URI'], '404.php'))
		{
			$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			if (empty($http_referer) OR Validate::isAbsoluteUrl($http_referer))
				Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'pagenotfound` (`request_uri`,`http_referer`,`date_add`) VALUES (\''.pSQL($request_uri).'\',\''.pSQL($http_referer).'\',NOW())');
		}
	}
}

function pnfSort($a, $b) {
    if ($a['nb'] == $b['nb'])
        return 0;
    return ($a['nb'] > $b['nb']) ? -1 : 1;
}


