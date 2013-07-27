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

class StatsPersonalInfos extends ModuleGraph
{
	private $html = '';
	private $_query = '';
	private $_option;

	public function __construct()
	{
		$this->name = 'statspersonalinfos';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Registered customer information');
		$this->description = $this->l('Display characteristics such as gender and age.');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		$this->html = '<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>';
		if (count(Customer::getCustomers()))
		{
			if (Tools::getValue('export'))
				if (Tools::getValue('exportType') == 'gender')
					$this->csvExport(array('type' => 'pie', 'option' => 'gender'));
				else if (Tools::getValue('exportType') == 'age')
					$this->csvExport(array('type' => 'pie', 'option' => 'age'));
				else if (Tools::getValue('exportType') == 'country')
					$this->csvExport(array('type' => 'pie', 'option' => 'country'));
				else if (Tools::getValue('exportType') == 'currency')
					$this->csvExport(array('type' => 'pie', 'option' => 'currency'));
				else if (Tools::getValue('exportType') == 'language')
					$this->csvExport(array('type' => 'pie', 'option' => 'language'));

			$this->html .= '
				<p><img src="../img/admin/down.gif" />'.$this->l('Gender distribution allows you to determine the percentage of men and women shoppers in your store. ').'</p>
				<div>'.$this->engine(array('type' => 'pie', 'option' => 'gender')).'</div><br />
				<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=gender"><span>'.$this->l('CSV Export').'</span></a></p>
				<br class="clear" /><br />
				<p><img src="../img/admin/down.gif" />'.$this->l('Age ranges allow you to better understand target demographics. ').'</p>
				<div>'.$this->engine(array('type' => 'pie', 'option' => 'age')).'</div><br />
				<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=age"><span>'.$this->l('CSV Export').'</span></a></p><br /><br />
				<p><img src="../img/admin/down.gif" />'.$this->l('Country distribution allows you to analyze which part of the world your customers are shopping from.').'</p>
				<div>'.$this->engine(array('type' => 'pie', 'option' => 'country')).'</div><br />
				<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=country"><span>'.$this->l('CSV Export').'</span></a></p><br /><br />
				<p><img src="../img/admin/down.gif" />'.$this->l('Currency range allows you to determine which currency your customers are using.').'</p>
				<div>'.$this->engine(array('type' => 'pie', 'option' => 'currency')).'</div><br />
				<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=currency"><span>'.$this->l('CSV Export').'</span></a></p><br /><br />
				<p><img src="../img/admin/down.gif" />'.$this->l('Language distribution allows you to analyze the browsing language used by your customers. ').'</p>
				<div>'.$this->engine(array('type' => 'pie', 'option' => 'language')).'</div><br />
				<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1&exportType=language"><span>'.$this->l('CSV Export').'</span></a></p>
			</center>';
		}
		else
			$this->html .= '<p>'.$this->l('No customers have registered yet.').'</p>';
		$this->html .= '
		</div><br />
		<div class="blocStats"><h2 class="icon-guide"><span></span>'.$this->l('Guide').'</h2>
			<h2>'.$this->l('Target your audience.').'</h2>
			<p>
				'.$this->l('In order for each message to have an impact, you need to know who it is being addressed to. ').'
				'.$this->l('Defining your target audience is essential when choosing the right tools to win them over.').'
				'.$this->l('It is best to limit action to a group -- or groups -- of clients.').'
				'.$this->l('Storing registered customer information allows you to accurately define customer profile so you can adapt your special deals and promotions.').'
			</p><br />
			<p>
				'.$this->l('Increase your sales by').'
				<ul>
					<li class="bullet">'.$this->l('launching targeted advertisement campaigns.').'</li>
					<li class="bullet">'.$this->l('Contact a group of clients by email or newsletter.').'</li>
				</ul>
			</p><br />
		</div>';
		return $this->html;
	}

	public function setOption($option, $layers = 1)
	{
		$this->_option = $option;
	}

	protected function getData($layers)
	{
		switch ($this->_option)
		{
			case 'gender':
				$this->_titles['main'] = $this->l('Gender distribution');
				$genders = array(
					0 => $this->l('Male'),
					1 => $this->l('Female'),
					2 => $this->l('Unknown'),
				);

				$sql = 'SELECT g.type, c.id_gender, COUNT(c.id_customer) AS total
						FROM '._DB_PREFIX_.'customer c
						LEFT JOIN '._DB_PREFIX_.'gender g ON c.id_gender = g.id_gender
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c').'
						GROUP BY c.id_gender';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

				$gendersResults = array();
				foreach ($result as $row)
				{
					$type = (is_null($row['type'])) ? 2 : $row['type'];
					if (!isset($gendersResults[$type]))
						$gendersResults[$type] = 0;
					$gendersResults[$type] += $row['total'];
				}

				foreach ($gendersResults as $type => $total)
				{
					$this->_values[] = $total;
					$this->_legend[] = $genders[$type];
				}
			break;

			case 'age':
				$this->_titles['main'] = $this->l('Age Range');

				// 0 - 18 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 18
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('0-18');
				}

				// 18 - 24 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 18
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 25
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('18-24');
				}

				// 25 - 34 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 25
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 35
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
 				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('25-34');
				}

				// 35 - 49 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 35
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 50
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('35-49');
				}

				// 50 - 59 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 50
							AND (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) < 60
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('50-59');
				}

				// More than 60 years
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE (YEAR(CURDATE()) - YEAR(`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(`birthday`, 5)) >= 60
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
							AND `birthday` IS NOT NULL';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('60+');
				}

				// Total unknown
				$sql = 'SELECT COUNT(`id_customer`) as total
						FROM `'._DB_PREFIX_.'customer`
						WHERE `birthday` IS NULL
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
				if (isset($result['total']) && $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('Unknown');
				}
			break;

			case 'country':
				$this->_titles['main'] = $this->l('Country distribution');
				$sql = 'SELECT cl.`name`, COUNT(c.`id_country`) AS total
						FROM `'._DB_PREFIX_.'address` a
						LEFT JOIN `'._DB_PREFIX_.'customer` cu ON cu.id_customer = a.id_customer
						LEFT JOIN `'._DB_PREFIX_.'country` c ON a.`id_country` = c.`id_country`
						LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
						WHERE a.id_customer != 0
							'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'cu').'
						GROUP BY c.`id_country`';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $row['name'];
				}
			break;

			case 'currency':
				$this->_titles['main'] = $this->l('Currency distribution');
				$sql = 'SELECT c.`name`, COUNT(c.`id_currency`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'currency` c ON o.`id_currency` = c.`id_currency`
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
						GROUP BY c.`id_currency`';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $row['name'];
				}
			break;

			case 'language':
				$this->_titles['main'] = $this->l('Language distribution');
				$sql = 'SELECT c.`name`, COUNT(c.`id_lang`) AS total
						FROM `'._DB_PREFIX_.'orders` o
						LEFT JOIN `'._DB_PREFIX_.'lang` c ON o.`id_lang` = c.`id_lang`
						WHERE 1
							'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
						GROUP BY c.`id_lang`';
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $row['name'];
				}
			break;
		}
	}
}


