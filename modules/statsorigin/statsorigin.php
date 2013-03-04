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

class StatsOrigin extends ModuleGraph
{
	private $_html;

	public function __construct()
	{
		$this->name = 'statsorigin';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Visitors origin');
		$this->description = $this->l('Display the websites your visitors come from.');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	private function getOrigins($dateBetween)
	{
		$directLink = $this->l('Direct link');
		$sql = 'SELECT http_referer
				FROM '._DB_PREFIX_.'connections
				WHERE 1
					'.Shop::addSqlRestriction().'
					AND date_add BETWEEN '.$dateBetween;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);
		$websites = array($directLink => 0);
		while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($result))
		{
			if (!isset($row['http_referer']) || empty($row['http_referer']))
				++$websites[$directLink];
			else
			{
				$website = preg_replace('/^www./', '', parse_url($row['http_referer'], PHP_URL_HOST));
				if (!isset($websites[$website]))
					$websites[$website] = 1;
				else
					++$websites[$website];
			}
		}
		arsort($websites);
		return $websites;
	}

	public function hookAdminStatsModules()
	{
		$websites = $this->getOrigins(ModuleGraph::getDateBetween());
		if (Tools::getValue('export'))
			if (Tools::getValue('exportType') == 'top')
				$this->csvExport(array('type' => 'pie'));
		$this->_html = '<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->l('Origin').'</h2>';
		if (count($websites))
		{
			$this->_html .= '
			<p><img src="../img/admin/down.gif" />'.$this->l('Here is the percentage of the 10 most popular referrer websites by which visitors went through to get to your shop.').'</p>
			<div>'.$this->engine(array('type' => 'pie')).'</div><br />
			<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=top"><span>'.$this->l('CSV Export').'</span></a></p><br />
			
			<table class="table " border="0" cellspacing="0" cellspacing="0">
				<tr>
					<th style="width:400px;">'.$this->l('Origin').'</th>
					<th style="width:50px; text-align: right">'.$this->l('Total').'</th>
				</tr>';
			foreach ($websites as $website => $total)
				$this->_html .= '<tr>
					<td>'.(!strstr($website, ' ') ? '<a href="'.Tools::getProtocol().$website.'">' : '').$website.(!strstr($website, ' ') ? '</a>' : '').'</td><td style="text-align: right">'.$total.'</td>
				</tr>';
			$this->_html .= '</table></div>';
		}
		else
			$this->_html .= '<p><strong>'.$this->l('Direct links only').'</strong></p>';
		$this->_html .= '</div><br />
		<div class="blocStats"><h2 class="icon-guide"><span></span>'.$this->l('Guide').'</h2>
		<h2>'.$this->l('What is a referrer website?').'</h2>
			<p>
				'.$this->l('When visiting a webpage, the referrer is the URL of the previous webpage from which a link was followed.').'<br />
				'.$this->l('A referrer enables you to know which keywords are entered by visitors in search engines when getting to your shop and allows you to optimize web promotion.').'<br /><br />
				'.$this->l('A referrer can be:').'
				<ul>
					<li class="bullet">'.$this->l('Someone who put a link on their website for your shop').'</li>
					<li class="bullet">'.$this->l('A partner with whom you made a link exchange in order to bring in sales or attract new customers').'</li>
				</ul>
			</p>
		</div>';
		return $this->_html;
	}

	protected function getData($layers)
	{
		$this->_titles['main'] = $this->l('First 10 websites');
		$websites = $this->getOrigins($this->getDate());
		$total = 0;
		$total2 = 0;
		$i = 0;
		foreach ($websites as $website => $totalRow)
		{
			if (!$totalRow)
				continue;
			$total += $totalRow;
			if ($i++ < 9)
			{
				$this->_legend[] = $website;
				$this->_values[] = $totalRow;
				$total2 += $totalRow;
			}
		}
		if ($total != $total2)
		{
			$this->_legend[] = $this->l('Others');
			$this->_values[] = $total - $total2;
		}
	}
}


