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

class StatsPersonalInfos extends ModuleGraph
{
    private $_html = '';
    private $_query = '';
	private $_option;

    function __construct()
    {
        $this->name = 'statspersonalinfos';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		
		parent::__construct();
		
        $this->displayName = $this->l('Registered Customer Info');
        $this->description = $this->l('Display characteristics such as gender and age.');
	}
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}
		
	public function hookAdminStatsModules($params)
	{
		$this->_html = '<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>';
		if (sizeof(Customer::getCustomers()))
		{
			if (Tools::getValue('export'))
				if (Tools::getValue('exportType') =='gender')
					$this->csvExport(array('type' => 'pie', 'option' => 'gender'));
				elseif (Tools::getValue('exportType') =='age')
					$this->csvExport(array('type' => 'pie', 'option' => 'age'));
				elseif (Tools::getValue('exportType') =='country')
					$this->csvExport(array('type' => 'pie', 'option' => 'country'));
				elseif (Tools::getValue('exportType') =='currency')
					$this->csvExport(array('type' => 'pie', 'option' => 'currency'));
				elseif (Tools::getValue('exportType') =='language')
					$this->csvExport(array('type' => 'pie', 'option' => 'language'));
			
			$this->_html .= '
			
				<center><p><img src="../img/admin/down.gif" />'.$this->l('Gender distribution allows you to determine the percentage of men and women among your customers.').'</p>
				'.ModuleGraph::engine(array('type' => 'pie', 'option' => 'gender')).'<br /></center>
				<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=gender"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p>
				<br class="clear" /><br />
				<center><p><img src="../img/admin/down.gif" />'.$this->l('Age ranges allows you to determine in which age range your customers are.').'</p>
				'.ModuleGraph::engine(array('type' => 'pie', 'option' => 'age')).'<br /></center>
				<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=age"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br /><br />
				<center><p><img src="../img/admin/down.gif" />'.$this->l('Country distribution allows you to determine in which part of the world your customers are shopping from.').'</p>
				'.ModuleGraph::engine(array('type' => 'pie', 'option' => 'country')).'<br /></center>
				<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=country"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br /><br />
				<center><p><img src="../img/admin/down.gif" />'.$this->l('Currency ranges allows you to determine which currencies your customers are using.').'</p>
				'.ModuleGraph::engine(array('type' => 'pie', 'option' => 'currency')).'<br /></center>
				<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=currency"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p><br /><br />
				<center><p><img src="../img/admin/down.gif" />'.$this->l('Language distribution allows you to determine the general languages your customers are using on your shop.').'</p>
				'.ModuleGraph::engine(array('type' => 'pie', 'option' => 'language')).'<br /></center>
				<p><a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=language"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a></p>
			</center>';
		}
		else
			$this->_html .= '<p>'.$this->l('No customers registered yet.').'</p>';
		$this->_html .= '
		</fieldset><br />
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
			<h2>'.$this->l('Target your audience').'</h2>
			<p>
				'.$this->l('In order for each message to have an impact, you need to know to whom it should be addressed.').'
				'.$this->l('Addressing the right audience is essential for choosing the right tools to win them over.').'
				'.$this->l('It is best to limit action to a group or groups of clients.').'
				'.$this->l('Registered customer information allows you to accurately define the typical customer profile so that you can adapt your specials to various criteria.').'
			</p><br />
			<p>
				'.$this->l('You should use this information to increase your sales by').'
				<ul>
					<li class="bullet">'.$this->l('launching ad campaigns addressed to specific customers who might be interested in a particular offer at specific dates and times.').'</li>
					<li class="bullet">'.$this->l('Contacting a group of clients by e-mail / newsletter.').'</li>
				</ul>
			</p><br />
		</fieldset>';
		return $this->_html;
	}

	public function setOption($option, $layers = 1)
	{
		$this->_option = $option;
	}
	
	protected function getData($layers)
	{
		global $cookie;
		
		switch ($this->_option)
		{
			case 'gender':
				$this->_titles['main'] = $this->l('Gender distribution');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.`id_gender`, COUNT(c.`id_customer`) AS total
				FROM `'._DB_PREFIX_.'customer` c
				GROUP BY c.`id_gender`');
				$gender = array(1 => $this->l('Male'), 2 => $this->l('Female'), 9 => $this->l('Unknown'), 0 => $this->l('Unknown'));
				foreach ($result as $row)
				{
					$this->_values[] = $row['total'];
					$this->_legend[] = $gender[$row['id_gender']];
				}
				break;
			case 'age':
				$this->_titles['main'] = $this->l('Age ranges');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) < 18 
				AND c.`birthday` IS NOT NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('0-18 years old');
				}
				
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) >= 18
				AND (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) < 25
				AND c.`birthday` IS NOT NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('18-24 years old');
				}

 				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) >= 25
				AND (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) < 35
				AND c.`birthday` IS NOT NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('25-34 years old');
				}
				
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) >= 35
				AND (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) < 50
				AND c.`birthday` IS NOT NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('35-49 years old');
				}
				
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) >= 50
				AND (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) < 60
				AND c.`birthday` IS NOT NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('50-59 years old');
				}
				
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE (YEAR(CURDATE()) - YEAR(c.`birthday`)) - (RIGHT(CURDATE(), 5) < RIGHT(c.`birthday`, 5)) >= 60
				AND c.`birthday` IS NOT NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('60 years old and more');
				}
				
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT COUNT(c.`id_customer`) as total
				FROM `'._DB_PREFIX_.'customer` c
				WHERE c.`birthday` IS NULL');
				if (isset($result['total']) AND $result['total'])
				{
					$this->_values[] = $result['total'];
					$this->_legend[] = $this->l('Unknown');
				}
				break;
			case 'country':
				$this->_titles['main'] = $this->l('Country distribution');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT cl.`name`, COUNT(c.`id_country`) AS total
				FROM `'._DB_PREFIX_.'address` a
				LEFT JOIN `'._DB_PREFIX_.'country` c ON a.`id_country` = c.`id_country`
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)($cookie->id_lang).')
				WHERE a.id_customer != 0
				GROUP BY c.`id_country`');
				foreach ($result as $row)
				{
				    $this->_values[] = $row['total'];
				    $this->_legend[] = $row['name'];
				}
				break;
			case 'currency':
				$this->_titles['main'] = $this->l('Currency distribution');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.`name`, COUNT(c.`id_currency`) AS total
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'currency` c ON o.`id_currency` = c.`id_currency`
				GROUP BY c.`id_currency`');
				foreach ($result as $row)
				{
				    $this->_values[] = $row['total'];
				    $this->_legend[] = $row['name'];
				}
				break;
			case 'language':
				$this->_titles['main'] = $this->l('Language distribution');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.`name`, COUNT(c.`id_lang`) AS total
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'lang` c ON o.`id_lang` = c.`id_lang`
				GROUP BY c.`id_lang`');
				foreach ($result as $row)
				{
				    $this->_values[] = $row['total'];
				    $this->_legend[] = $row['name'];
				}
				break;
		}
	}
}


