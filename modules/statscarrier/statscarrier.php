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

class StatsCarrier extends ModuleGraph
{
    private $_html = '';
    private $_query = '';
    private $_query2 = '';
    private $_option = '';

    function __construct()
    {
        $this->name = 'statscarrier';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		
		parent::__construct();
		
        $this->displayName = $this->l('Carrier distribution');
        $this->description = $this->l('Display the carriers distribution');
    }
	
	public function install()
	{
		return (parent::install() AND $this->registerHook('AdminStatsModules'));
	}
		
	public function hookAdminStatsModules($params)
	{
		global $cookie;
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(o.`id_order`) as total
		FROM `'._DB_PREFIX_.'orders` o
		WHERE o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
		'.((int)(Tools::getValue('id_order_state')) ? 'AND (SELECT oh.id_order_state FROM `'._DB_PREFIX_.'order_history` oh WHERE o.id_order = oh.id_order ORDER BY oh.date_add DESC, oh.id_order_history DESC LIMIT 1) = '.(int)(Tools::getValue('id_order_state')) : ''));
		$states = OrderState::getOrderStates((int)($cookie->id_lang));
		if (Tools::getValue('export'))
				$this->csvExport(array('type' => 'pie', 'option' => Tools::getValue('id_order_state')));
		$this->_html = '
		<fieldset class="width3"><legend><img src="../modules/'.$this->name.'/logo.gif" /> '.$this->displayName.'</legend>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="float: right;">
				<select name="id_order_state">
					<option value="0"'.((!Tools::getValue('id_order_state')) ? ' selected="selected"' : '').'>'.$this->l('All').'</option>';
		foreach ($states AS $state)
			$this->_html .= '<option value="'.$state['id_order_state'].'"'.(($state['id_order_state'] == Tools::getValue('id_order_state')) ? ' selected="selected"' : '').'>'.$state['name'].'</option>';
		$this->_html .= '</select>
				<input type="submit" name="submitState" value="'.$this->l('Filter').'" class="button" />
			</form>
			<p><img src="../img/admin/down.gif" />'.$this->l('This graph represents the carrier distribution for your orders. You can also limit it to orders in one state.').'</p>
			'.($result['total'] ? ModuleGraph::engine(array('type' => 'pie', 'option' => Tools::getValue('id_order_state'))).'<br /><br />	<a href="'.$_SERVER['REQUEST_URI'].'&export=1&exportType=language"><img src="../img/admin/asterisk.gif" />'.$this->l('CSV Export').'</a>' : $this->l('No valid orders for this period.')).'
		</fieldset>';
		return $this->_html;
	}
	
	public function setOption($option, $layers = 1)
	{
		$this->_option = (int)($option);
	}
	
	protected function getData($layers)
	{
		$stateQuery = '';
		if ((int)($this->_option))
			$stateQuery = 'AND (SELECT oh.id_order_state FROM `'._DB_PREFIX_.'order_history` oh WHERE o.id_order = oh.id_order ORDER BY oh.date_add DESC, oh.id_order_history DESC LIMIT 1) = '.(int)($this->_option);
		$this->_titles['main'] = $this->l('Percentage of orders by carrier');
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.name, COUNT(DISTINCT o.`id_order`) as total
		FROM `'._DB_PREFIX_.'carrier` c
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON o.id_carrier = c.id_carrier
		WHERE o.`date_add` BETWEEN '.ModuleGraph::getDateBetween().'
		'.$stateQuery.'
		GROUP BY c.`id_carrier`');
		foreach ($result as $row)
		{
		    $this->_values[] = $row['total'];
		    $this->_legend[] = $row['name'];
		}
	}
}


