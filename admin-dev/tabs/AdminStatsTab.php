<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/tabs/AdminPreferences.php');

abstract class AdminStatsTab extends AdminPreferences
{
	public function __construct()
	{
 		$this->_fieldsSettings = array(
			'PS_STATS_RENDER' => array('title' => $this->l('Graph engine'), 'validation' => 'isGenericName'),
			'PS_STATS_GRID_RENDER' => array('title' => $this->l('Grid engine'), 'validation' => 'isGenericName')
		);
		parent::__construct();
	}
	
	public function postProcess()
	{
		global $cookie, $currentIndex;
		
		if (Tools::isSubmit('submitDatePicker'))
		{
			if (!Validate::isDate($from = Tools::getValue('datepickerFrom')) OR !Validate::isDate($to = Tools::getValue('datepickerTo')))
				$this->_errors[] = Tools::displayError('Date specified is invalid');
		}
		if (Tools::isSubmit('submitDateDay'))
		{
			$from = date('Y-m-d');
			$to = date('Y-m-d');
		}
		if (Tools::isSubmit('submitDateDayPrev'))
		{
			$yesterday = time() - 60*60*24;
			$from = date('Y-m-d', $yesterday);
			$to = date('Y-m-d', $yesterday);
		}
		if (Tools::isSubmit('submitDateMonth'))
		{
			$from = date('Y-m-01');
			$to = date('Y-m-t');
		}
		if (Tools::isSubmit('submitDateMonthPrev'))
		{
			$m = (date('m') == 1 ? 12 : date('m') - 1);
			$y = ($m == 12 ? date('Y') - 1 : date('Y'));
			$from = $y.'-'.$m.'-01';
			$to = $y.'-'.$m.date('-t', mktime(12, 0, 0, $m, 15, $y));
		}
		if (Tools::isSubmit('submitDateYear'))
		{
			$from = date('Y-01-01');
			$to = date('Y-12-31');
		}
		if (Tools::isSubmit('submitDateYearPrev'))
		{
			$from = (date('Y') - 1).date('-01-01');
			$to = (date('Y') - 1).date('-12-31');
		}
		if (isset($from) AND isset($to) AND !sizeof($this->_errors))
		{
			$employee = new Employee($cookie->id_employee);
			$employee->stats_date_from = $from;
			$employee->stats_date_to = $to;
			$employee->update();
			Tools::redirectAdmin($_SERVER['REQUEST_URI']);
		}
		if (Tools::getValue('submitSettings'))
		{
		 	if ($this->tabAccess['edit'] === '1')
			{
				$currentIndex .= '&module='.Tools::getValue('module');
				$this->_postConfig($this->_fieldsSettings);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		if (sizeof($this->_errors))
			AdminTab::displayErrors();
	}
	
	protected function displayEngines()
	{
		global $currentIndex, $cookie;
		
		$graphEngine = Configuration::get('PS_STATS_RENDER');
		$gridEngine = Configuration::get('PS_STATS_GRID_RENDER');
		$arrayGraphEngines = ModuleGraphEngine::getGraphEngines();
		$arrayGridEngines = ModuleGridEngine::getGridEngines();
		
		echo '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset style="width: 200px;"><legend><img src="../img/admin/tab-preferences.gif" />'.$this->l('Settings', 'AdminStatsTab').'</legend>';
		echo '<p><strong>'.$this->l('Graph engine', 'AdminStatsTab').' </strong><br />';
		if (sizeof($arrayGraphEngines))
		{
			echo '<select name="PS_STATS_RENDER">';
			foreach ($arrayGraphEngines as $k => $value)
				echo '<option value="'.$k.'"'.($k == $graphEngine ? ' selected="selected"' : '').'>'.$value[0].'</option>';
			echo '</select><p>';
		}
		else
			echo $this->l('No graph engine module installed', 'AdminStatsTab');
		echo '<p><strong>'.$this->l('Grid engine', 'AdminStatsTab').' </strong><br />';
		if (sizeof($arrayGridEngines))
		{
			echo '<select name="PS_STATS_GRID_RENDER">';
			foreach ($arrayGridEngines as $k => $value)
				echo '<option value="'.$k.'"'.($k == $gridEngine ? ' selected="selected"' : '').'>'.$value[0].'</option>';
			echo '</select></p>';
		}
		else
			echo $this->l('No grid engine module installed', 'AdminStatsTab');
		echo '<p><input type="submit" value="'.$this->l('   Save   ', 'AdminStatsTab').'" name="submitSettings" class="button" /></p>
			</fieldset>
		</form><div class="clear space">&nbsp;</div>';
	}
	
	protected function getDate()
	{
		global $cookie;
		$year = isset($cookie->stats_year) ? $cookie->stats_year : date('Y');
		$month = isset($cookie->stats_month) ? sprintf('%02d', $cookie->stats_month) : '%';
		$day = isset($cookie->stats_day) ? sprintf('%02d', $cookie->stats_day) : '%';
		return $year.'-'.$month.'-'.$day;
	}
	
	public function displayCalendar()
	{
		echo '<div id="calendar">
		'.self::displayCalendarStatic(array(
			'Calendar' => $this->l('Calendar', 'AdminStatsTab'), 'Day' => $this->l('Day', 'AdminStatsTab'), 
			'Month' => $this->l('Month', 'AdminStatsTab'), 'Year' => $this->l('Year', 'AdminStatsTab'),
			'From' => $this->l('From:', 'AdminStatsTab'), 'To' => $this->l('To:', 'AdminStatsTab'), 'Save' => $this->l('Save', 'AdminStatsTab')
		)).'
		<div class="clear space">&nbsp;</div></div>';
	}
	
	public static function displayCalendarStatic($translations)
	{
		global $cookie;
		$employee = new Employee($cookie->id_employee);

		includeDatepicker(array('datepickerFrom', 'datepickerTo'));
		return '
		<fieldset style="width: 200px; font-size:13px;"><legend><img src="../img/admin/date.png" /> '.$translations['Calendar'].'</legend>
			<div>
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<input type="submit" name="submitDateDay" class="button" value="'.$translations['Day'].'">
					<input type="submit" name="submitDateMonth" class="button" value="'.$translations['Month'].'">
					<input type="submit" name="submitDateYear" class="button" value="'.$translations['Year'].'"><br />
					<input type="submit" name="submitDateDayPrev" class="button" value="'.$translations['Day'].'-1" style="margin-top:2px">
					<input type="submit" name="submitDateMonthPrev" class="button" value="'.$translations['Month'].'-1" style="margin-top:2px">
					<input type="submit" name="submitDateYearPrev" class="button" value="'.$translations['Year'].'-1" style="margin-top:2px">
					<p>'.(isset($translations['From']) ? $translations['From'] : 'From:').' <input type="text" name="datepickerFrom" id="datepickerFrom" value="'.Tools::getValue('datepickerFrom', $employee->stats_date_from).'"></p>
					<p>'.(isset($translations['To']) ? $translations['To'] : 'To:').' <input type="text" name="datepickerTo" id="datepickerTo" value="'.Tools::getValue('datepickerTo', $employee->stats_date_to).'"></p>
					<input type="submit" name="submitDatePicker" class="button" value="'.(isset($translations['Save']) ? $translations['Save'] : '   Save   ').'" />
				</form>
			</div>
		</fieldset>';
	}
	
	public function displaySearch()
	{
		return;
		echo '
		<fieldset style="margin-top:20px; width: 200px;"><legend><img src="../img/admin/binoculars.png" /> '.$this->l('Search', 'AdminStatsTab').'</legend>
			<input type="text" /> <input type="button" class="button" value="'.$this->l('Go', 'AdminStatsTab').'" />
		</fieldset>';
	}
	
	private function getModules()
	{
		return Db::getInstance()->ExecuteS('
		SELECT h.`name` AS hook, m.`name`
		FROM `'._DB_PREFIX_.'module` m
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` LIKE \'AdminStatsModules\'
		AND m.`active` = 1
		ORDER BY hm.`position`');
	}
	
	public function displayMenu()
	{
		global $currentIndex, $cookie;
		$modules = $this->getModules();

		echo '<fieldset style="width: 200px"><legend><img src="../img/admin/navigation.png" /> '.$this->l('Navigation', 'AdminStatsTab').'</legend>';
		if (sizeof($modules))
		{
			foreach ($modules AS $module)
				if ($moduleInstance = Module::getInstanceByName($module['name']))
					echo '<h4><img src="../modules/'.$module['name'].'/logo.gif" /><a href="'.$currentIndex.'&token='.Tools::getValue('token').'&module='.$module['name'].'">'.$moduleInstance->displayName.'</a></h4>';
		}
		else
			echo $this->l('No module installed', 'AdminStatsTab');
		echo '</fieldset><div class="clear space">&nbsp;</div>';
	}
	
	public function display()
	{
		echo '<div style="float:left">';
		$this->displayCalendar();
		$this->displayEngines();
		$this->displayMenu();
		$this->displaySearch();
		echo '</div>
		<div style="float:left; margin-left:20px;">';
		
		if (!($moduleName = Tools::getValue('module')) AND $moduleInstance = Module::getInstanceByName('statsforecast') AND $moduleInstance->active)
			$moduleName = 'statsforecast';
		if ($moduleName)
		{
			// Needed for the graphics display when this is the default module
			$_GET['module'] = $moduleName;
			if (!isset($moduleInstance))
				$moduleInstance = Module::getInstanceByName($moduleName);
			if ($moduleInstance AND $moduleInstance->active)
				echo Module::hookExec('AdminStatsModules', NULL, $moduleInstance->id);
			else
				echo $this->l('Module not found', 'AdminStatsTab');
		}
		else
			echo '<h3 class="space">'.$this->l('Please select a module in the left column.').'</h3>';
		echo '</div><div class="clear"></div>';
	}
}


