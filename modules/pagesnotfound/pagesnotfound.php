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

class Pagesnotfound extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'pagesnotfound';
		$this->tab = 'analytics_stats';
		$this->version = 1.1;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Pages not found');
		$this->description = $this->l('Display the pages requested by your visitors that have not been found.');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('AdminStatsModules'))
			return false;
		return Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'pagenotfound` (
			id_pagenotfound INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			id_shop INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			id_shop_group INTEGER UNSIGNED NOT NULL DEFAULT \'1\',
			request_uri VARCHAR(256) NOT NULL,
			http_referer VARCHAR(256) NOT NULL,
			date_add DATETIME NOT NULL,
			PRIMARY KEY(id_pagenotfound),
			INDEX (`date_add`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
	}

	public function uninstall()
	{
		return (parent::uninstall() && Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'pagenotfound`'));
	}

	private function getPages()
	{
		$sql = 'SELECT http_referer, request_uri, COUNT(*) as nb
				FROM `'._DB_PREFIX_.'pagenotfound`
				WHERE date_add BETWEEN '.ModuleGraph::getDateBetween()
					.Shop::addSqlRestriction().
				'GROUP BY http_referer, request_uri';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		$pages = array();
		foreach ($result as $row)
		{
			$row['http_referer'] = parse_url($row['http_referer'], PHP_URL_HOST).parse_url($row['http_referer'], PHP_URL_PATH);
			if (!isset($row['http_referer']) || empty($row['http_referer']))
				$row['http_referer'] = '--';
			if (!isset($pages[$row['request_uri']]))
				$pages[$row['request_uri']] = array('nb' => 0);
			$pages[$row['request_uri']][$row['http_referer']] = $row['nb'];
			$pages[$row['request_uri']]['nb'] += $row['nb'];
		}
		uasort($pages, 'pnfSort');
		return $pages;
	}

	public function hookAdminStatsModules()
	{
		if (Tools::isSubmit('submitTruncatePNF'))
		{
			Db::getInstance()->execute('TRUNCATE `'._DB_PREFIX_.'pagenotfound`');
			$this->_html .= '<div class="alert alert-warning"> '.$this->l('The pages not found cache has been emptied.').'</div>';
		}
		else if (Tools::isSubmit('submitDeletePNF'))
		{
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'pagenotfound`
				WHERE date_add BETWEEN '.ModuleGraph::getDateBetween());
			$this->_html .= '<div class="alert alert-warning"> '.$this->l('Pages not found have been deleted.').'</div>';
		}

		$this->_html .= '
			<div class="panel-heading">
				'.$this->displayName.'
			</div>
			<h4>'.$this->l('Guide').'</h4>
			<div class="alert alert-warning">
				<h4>'.$this->l('404 errors').'</h4>
				<p>'
					.$this->l('A 404 error is an HTTP error code which means that the file requested by the user cannot be found. 
					In your case it means that one of your visitors entered a wrong URL in the address bar or that you or another website has a dead link. 
					When it is available, the referrer is shown so you can find the page which contains the dead link. 
					If not, it means generally that it is a direct access, so someone may have bookmarked a link which doesn\'t exist anymore.').'
				</p>
				<p>&nbsp;</p>
				<h4>'.$this->l('How to catch these errors?').'</h4>
				<p>'
					.$this->l('If your webhost supports .htaccess files, you can create one in the root directory of PrestaShop and insert the following line inside:').' 
					<i>ErrorDocument 404 '.__PS_BASE_URI__.'404.php</i>. '.
					$this->l('A user requesting a page which doesn\'t exist will be redirected to the following page.').' <i>'.__PS_BASE_URI__.'404.php</i>. '.
					$this->l('This module logs access to this page.').'
				</p>
			</div>';
		if (!file_exists(dirname(__FILE__).'/../../.htaccess'))
			$this->_html .= '<div class="alert alert-warning">'.$this->l('You must use a .htaccess file to redirect 404 errors to the page "404.php"').'</div>';

		$pages = $this->getPages();
		if (count($pages))
		{
			$this->_html .= '
			<table class="table">
				<thead>
					<tr>
						<th><span class="title_box active">'.$this->l('Page').'</span></th>
						<th><span class="title_box active">'.$this->l('Referrer').'</span></th>
						<th><span class="title_box active">'.$this->l('Counter').'</span></th>
					</tr>
				</thead>
				<tbody>';
			foreach ($pages as $ru => $hrs)
				foreach ($hrs as $hr => $counter)
					if ($hr != 'nb')
						$this->_html .= '
						<tr>
							<td><a href="'.$ru.'-admin404">'.wordwrap($ru, 30, '<br />', true).'</a></td>
							<td><a href="'.Tools::getProtocol().$hr.'">'.wordwrap($hr, 40, '<br />', true).'</a></td>
							<td>'.$counter.'</td>
						</tr>';
			$this->_html .= '
				</tbody>
			</table>';
		}
		else
			$this->_html .= '<div class="alert alert-warning"> '.$this->l('No pages registered').'</div>';

		if (count($pages))
			$this->_html .= '
				<h4>'.$this->l('Empty database').'</h4>
				<form action="'.Tools::htmlEntitiesUtf8($_SERVER['REQUEST_URI']).'" method="post">
					<button type="submit" class="btn btn-default" name="submitDeletePNF">
						<i class="icon-remove"></i> '.$this->l('Empty ALL pages not found for this period').'
					</button>
					<button type="submit" class="btn btn-default" name="submitTruncatePNF">
						<i class="icon-remove"></i> '.$this->l('Empty ALL pages not found').'
					</button>
				</form>';

		return $this->_html;
	}

	public function hookTop($params)
	{
		if (strstr($_SERVER['REQUEST_URI'], '404.php') && isset($_SERVER['REDIRECT_URL']))
			$_SERVER['REQUEST_URI'] = $_SERVER['REDIRECT_URL'];
		if (!Validate::isUrl($request_uri = $_SERVER['REQUEST_URI']) || strstr($_SERVER['REQUEST_URI'], '-admin404'))
			return;

		if (get_class(Context::getContext()->controller) == 'PageNotFoundController')
		{
			$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			if (empty($http_referer) || Validate::isAbsoluteUrl($http_referer))
			{
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'pagenotfound` (`request_uri`, `http_referer`, `date_add`, `id_shop`, `id_shop_group`)
					VALUES (\''.pSQL($request_uri).'\', \''.pSQL($http_referer).'\', NOW(), '.(int)$this->context->shop->id.', '.(int)$this->context->shop->id_shop_group.')
				');
			}
		}
	}
}

function pnfSort($a, $b)
{
	if ($a['nb'] == $b['nb'])
		return 0;
	return ($a['nb'] > $b['nb']) ? -1 : 1;
}