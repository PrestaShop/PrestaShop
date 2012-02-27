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

class StatsEquipment extends ModuleGraph
{
	private $html = '';
	private $_query = '';
	private $_query2 = '';

	public function __construct()
	{
		$this->name = 'statsequipment';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Software');
		$this->description = $this->l('Display the software used by your visitors.');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	/**
	 * @return array Get list of browser "plugins" (javascript, media player, etc.)
	 */
	private function getEquipment()
	{
		$sql = 'SELECT DISTINCT g.*
				FROM `'._DB_PREFIX_.'connections` c 
				LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.`id_guest` = c.`id_guest`
				WHERE c.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
					'.Shop::addSqlRestriction(false, 'c');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql);

		$calcArray = array(
			'jsOK' => 0,
			'jsKO' => 0,
			'javaOK' => 0,
			'javaKO' => 0,
			'wmpOK' => 0,
			'wmpKO' => 0,
			'qtOK' => 0,
			'qtKO' => 0,
			'realOK' => 0,
			'realKO' => 0,
			'flashOK' => 0,
			'flashKO' => 0,
			'directorOK' => 0,
			'directorKO' => 0
		);
		while ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->nextRow($result))
		{
			if (!$row['javascript'])
			{
				++$calcArray['jsKO'];
				continue;
			}
			++$calcArray['jsOK'];
			($row['windows_media']) ? ++$calcArray['wmpOK'] : ++$calcArray['wmpKO'];
			($row['real_player']) ? ++$calcArray['realOK'] : ++$calcArray['realKO'];
			($row['adobe_flash']) ? ++$calcArray['flashOK'] : ++$calcArray['flashKO'];
			($row['adobe_director']) ? ++$calcArray['directorOK'] : ++$calcArray['directorKO'];
			($row['sun_java']) ? ++$calcArray['javaOK'] : ++$calcArray['javaKO'];
			($row['apple_quicktime']) ? ++$calcArray['qtOK'] : ++$calcArray['qtKO'];
		}

		if (!$calcArray['jsOK'])
			return false;

		$equip = array(
			'Windows Media Player' => $calcArray['wmpOK'] / ($calcArray['wmpOK'] + $calcArray['wmpKO']),
			'Real Player' => $calcArray['realOK'] / ($calcArray['realOK'] + $calcArray['realKO']),
			'Apple Quicktime' => $calcArray['qtOK'] / ($calcArray['qtOK'] + $calcArray['qtKO']),
			'Sun Java' => $calcArray['javaOK'] / ($calcArray['javaOK'] + $calcArray['javaKO']),
			'Adobe Flash' => $calcArray['flashOK'] / ($calcArray['flashOK'] + $calcArray['flashKO']),
			'Adobe Shockwave' => $calcArray['directorOK'] / ($calcArray['directorOK'] + $calcArray['directorKO'])
		);
		arsort($equip);
		return $equip;
	}

	public function hookAdminStatsModules($params)
	{
		if (Tools::getValue('export'))
			if (Tools::getValue('exportType') == 'browser')
				$this->csvExport(array('type' => 'pie', 'option' => 'wb'));
			else if (Tools::getValue('exportType') == 'os')
				$this->csvExport(array('type' => 'pie', 'option' => 'os'));

		$equipment = $this->getEquipment();
		$this->html = '
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
			<p><img src="../img/admin/down.gif" />'.$this->l('Determine the percentage of web browsers used by your customers.').'</p>
			'.$this->engine(array('type' => 'pie', 'option' => 'wb')).'<br /><br />
			<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=browser"><span>'.$this->l('CSV Export').'</span></a></p>
			<p><img src="../img/admin/down.gif" />'.$this->l('Determine the percentage of operating systems used by your customers.').'</p>
			'.$this->engine(array('type' => 'pie', 'option' => 'os')).'
			<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=os"><span>'.$this->l('CSV Export').'</span></a></p>';

			if ($equipment)
			{
				$this->html .= '<table class="table space" border="0" cellspacing="0" cellpadding="0">
				<tr><th style="width: 200px">'.$this->l('Plug-ins').'</th><th></th></tr>';
				foreach ($equipment as $name => $value)	
					$this->html .= '<tr><td>'.$name.'</td><td>'.number_format(100 * $value, 2).'%</td></tr>';
				$this->html .= '</table>';
			}
			$this->html .= '
		</div>
		<br />
		<div class="blocStats"><h2 class="icon-guide"><span></span>'.$this->l('Guide').'</h2>
		<h2>'.$this->l('Ensure that your website is accessible to all.').'</h2>
			<p>
				'.$this->l('When managing Websites, it is important to keep track of software used by visitors in order to be sure that the site displays the same way for everyone. 
					PrestaShop was built in order to be compatible with most recent Web browsers and computer operating systems (OS). 
					However, because you may end up adding advanced features to your Website or even modify the core PrestaShop code, these additions may not be accessible by everyone. 
					That is why it is a good idea to keep tabs on the percentage of users for each type of software before adding or changing something that only a limited number of users will be able to access.').'
			</p><br />
			
		</div>';
		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		switch ($option)
		{
			case 'wb':
				$this->_titles['main'] = $this->l('Web browser use');
				$this->_query = 'SELECT wb.`name`, COUNT(g.`id_web_browser`) AS total
						FROM `'._DB_PREFIX_.'web_browser` wb
						LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.`id_web_browser` = wb.`id_web_browser`
						LEFT JOIN `'._DB_PREFIX_.'connections` c ON g.`id_guest` = c.`id_guest`
						WHERE 1
							'.Shop::addSqlRestriction(false, 'c').'
							AND c.`date_add` BETWEEN ';
				$this->_query2 = ' GROUP BY g.`id_web_browser`';
			break;

			case 'os':
				$this->_titles['main'] = $this->l('Operating systems use');
				$this->_query = 'SELECT os.`name`, COUNT(g.`id_operating_system`) AS total
						FROM `'._DB_PREFIX_.'operating_system` os
						LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.`id_operating_system` = os.`id_operating_system`
						LEFT JOIN `'._DB_PREFIX_.'connections` c ON g.`id_guest` = c.`id_guest`
						WHERE 1
							'.Shop::addSqlRestriction(false, 'c').'
							AND c.`date_add` BETWEEN ';
				$this->_query2 = ' GROUP BY g.`id_operating_system`';
			 break;
		}
	}

	protected function getData($layers)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query.$this->getDate().$this->_query2);
		$this->_values = array();
		$i = 0;
		foreach ($result as $row)
		{
			$this->_values[$i] = $row['total'];
			$this->_legend[$i++] = $row['name'];
		}
	}
}


