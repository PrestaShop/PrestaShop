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

class StatsOrigin extends ModuleGraph
{
	private $_html;
	
    function __construct()
    {
        $this->name = 'statsorigin';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		
        parent::__construct();
		
        $this->displayName = $this->l('Visitors origin');
        $this->description = $this->l('Display the websites your visitors come from.');
    }

	function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}

	private function getOrigins($dateBetween)
	{
		$directLink = $this->l('Direct link');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.http_referer
		FROM '._DB_PREFIX_.'connections c
		WHERE c.date_add BETWEEN '.$dateBetween, false);
		$websites = array($directLink => 0);
		while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($result))
		{
			if (!isset($row['http_referer']) OR empty($row['http_referer']))
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

	function hookAdminStatsModules()
	{
		$websites = $this->getOrigins(ModuleGraph::getDateBetween());
		if (Tools::getValue('export'))
			if (Tools::getValue('exportType') == 'top')
				$this->csvExport(array('type' => 'pie'));
		$this->_html = '<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->l('Origin').'</legend>';
		if (sizeof($websites))
		{
			$this->_html .= '
			<center><p><img src="../img/admin/down.gif" />'. $this->l('Here is the percentage of the 10 most popular referrer websites by which visitors went through to get to your shop.').'</p>
			'.ModuleGraph::engine(array('type' => 'pie')).'</center>
			<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=top"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br /><br />
			<div style="overflow-y: scroll; height: 600px;">
			<center>
			<table class="table " border="0" cellspacing="0" cellspacing="0">
				<tr>
					<th style="width:400px;">'.$this->l('Origin').'</th>
					<th style="width:50px; text-align: right">'.$this->l('Total').'</th>
				</tr>';
			foreach ($websites as $website => $total)
				$this->_html .= '<tr><td>'.(!strstr($website, ' ') ? '<a href="'.Tools::getProtocol().$website.'">' : '').$website.(!strstr($website, ' ') ? '</a>' : '').'</td><td style="text-align: right">'.$total.'</td></tr>';
			$this->_html .= '</table></center></div>';
		}
		else
			$this->_html .= '<p><strong>'.$this->l('Direct links only').'</strong></p>';
		$this->_html .= '</fieldset><br />
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
		<h2>'.$this->l('What is a referrer website?').'</h2>
			<p>
				'.$this->l('When visiting a webpage, the referrer is the URL of the previous webpage from which a link was followed.').'<br />
				'.$this->l('A referrer enables you to know which keywords are entered by visitors in search engines when getting to your shop and allows you to optimize web promotion.').'<br /><br />
				'. $this->l('A referrer can be:').'
				<ul>
					<li class="bullet">'. $this->l('Someone who put a link on their website for your shop').'</li>
					<li class="bullet">'. $this->l('A partner with whom you made a link exchange in order to bring in sales or attract new customers').'</li>
				</ul>
			</p>
		</fieldset>';
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


