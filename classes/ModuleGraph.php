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

abstract class ModuleGraphCore extends Module
{
	protected $_employee;
	
	/** @var integer array graph data */
	protected	$_values = array();
	
	/** @var string array graph legends (X axis) */
	protected	$_legend = array();
	
	/**@var string graph titles */
	protected	$_titles = array('main' => NULL, 'x' => NULL, 'y' => NULL);
		
	/** @var ModuleGraphEngine graph engine */
	protected $_render;
	
	abstract protected function getData($layers);
	
	public function setEmployee($id_employee)
	{
		$this->_employee = new Employee((int)($id_employee));
	}
	public function setLang($id_lang)
	{
		$this->_id_lang = $id_lang;
	}
	
	protected function setDateGraph($layers, $legend = false)
	{
		// Get dates in a manageable format
		$fromArray = getdate(strtotime($this->_employee->stats_date_from));
		$toArray = getdate(strtotime($this->_employee->stats_date_to));
		
		// If the granularity is inferior to 1 day
		if ($this->_employee->stats_date_from == $this->_employee->stats_date_to)
		{
			if ($legend)
				for ($i = 0; $i < 24; $i++)
				{
					if ($layers == 1)
						$this->_values[$i] = 0;
					else
						for ($j = 0; $j < $layers; $j++)
							$this->_values[$j][$i] = 0;
					$this->_legend[$i] = ($i % 2) ? '' : sprintf('%02dh', $i);
				}
			if (is_callable(array($this, 'setDayValues')))
				$this->setDayValues($layers);
		}
		// If the granularity is inferior to 1 month TODO : change to manage 28 to 31 days
		elseif (strtotime($this->_employee->stats_date_to) - strtotime($this->_employee->stats_date_from) <= 2678400)
		{
			if ($legend)
			{
				$days = array();
				if ($fromArray['mon'] == $toArray['mon'])
					for ($i = $fromArray['mday']; $i <= $toArray['mday']; ++$i)
						$days[] = $i;
				else
				{
					$imax = date('t', mktime(0, 0, 0, $fromArray['mon'], 1, $fromArray['year']));
					for ($i = $fromArray['mday']; $i <= $imax; ++$i)
						$days[] = $i;
					for ($i = 1; $i <= $toArray['mday']; ++$i)
						$days[] = $i;
				}
				foreach ($days as $i)
				{
					if ($layers == 1)
						$this->_values[$i] = 0;
					else
						for ($j = 0; $j < $layers; $j++)
							$this->_values[$j][$i] = 0;
					$this->_legend[$i] = ($i % 2) ? '' : sprintf('%02d', $i);
				}
			}
			if (is_callable(array($this, 'setMonthValues')))
				$this->setMonthValues($layers);
		}
		// If the granularity is superior to 1 month
		else
		{
			if ($legend)
			{
				$months = array();
				if ($fromArray['year'] == $toArray['year'])
					for ($i = $fromArray['mon']; $i <= $toArray['mon']; ++$i)
						$months[] = $i;
				else
				{
					for ($i = $fromArray['mon']; $i <= 12; ++$i)
						$months[] = $i;
					for ($i = 1; $i <= $toArray['mon']; ++$i)
						$months[] = $i;
				}
				foreach ($months as $i)
				{
					if ($layers == 1)
						$this->_values[$i] = 0;
					else
						for ($j = 0; $j < $layers; $j++)
							$this->_values[$j][$i] = 0;
					$this->_legend[$i] = sprintf('%02d', $i);
				}
			}
			if (is_callable(array($this, 'setYearValues')))
				$this->setYearValues($layers);
		}
	}
	
	protected function csvExport($datas)
	{
		global $cookie;
		$this->setEmployee(intval($cookie->id_employee));
		$this->setLang(intval($cookie->id_lang));

		$layers = isset($datas['layers']) ?  $datas['layers'] : 1;
		if (isset($datas['option']))
			$this->setOption($datas['option'], $layers);
		$this->getData($layers);
		
		if (is_array($this->_titles['main']))
			for ($i = 1; $i <= sizeof($this->_titles['main']); $i++)
				$this->_csv .= ';'.$this->_titles['main'][$i];
		else
			$this->_csv .= ';'.$this->_titles['main'];
		$this->_csv .= "\n";
		if (sizeof($this->_legend))
		{
			if ($datas['type'] == 'pie')
				foreach ($this->_legend AS $key => $legend)
					for ($i = 0; $i < (is_array($this->_titles['main']) ? sizeof($this->_values) : 1); ++$i)
						$total += (is_array($this->_values[$i])  ? $this->_values[$i][$key] : $this->_values[$key]);
			foreach ($this->_legend AS $key => $legend)
			{
				$this->_csv .= $legend.';';		
				for ($i = 0; $i < (is_array($this->_titles['main']) ? sizeof($this->_values) : 1); ++$i)
					$this->_csv .= (is_array($this->_values[$i])  ? $this->_values[$i][$key] : $this->_values[$key]) / (($datas['type'] == 'pie') ? $total : 1).';';
				$this->_csv .= "\n";
			}
		}
		$this->_displayCsv();
	}
	
	protected function _displayCsv()
	{
		ob_end_clean();
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$this->displayName.' - '.time().'.csv"');
		echo $this->_csv;
		exit;
	}
	
	public function create($render, $type, $width, $height, $layers)
	{
		if (!Tools::file_exists_cache($file = dirname(__FILE__).'/../modules/'.$render.'/'.$render.'.php'))
			die(Tools::displayError());
		require_once($file);
		$this->_render = new $render($type);
		
		$this->getData($layers);
		$this->_render->createValues($this->_values);
		$this->_render->setSize($width, $height);
		$this->_render->setLegend($this->_legend);
		$this->_render->setTitles($this->_titles);
	}
	
	public function draw()
	{
		$this->_render->draw();
	}
		
	public static function engine($params)
	{		
		if (!($render = Configuration::get('PS_STATS_RENDER')))
			return Tools::displayError('No graph engine selected');
		if (!file_exists(dirname(__FILE__).'/../modules/'.$render.'/'.$render.'.php'))
			return Tools::displayError('Graph engine selected is unavailable.');
			
		global $cookie;
		$id_employee = (int)($cookie->id_employee);
		$id_lang = (int)($cookie->id_lang);

		if (!isset($params['layers']))
			$params['layers'] = 1;
		if (!isset($params['type']))
			$params['type'] = 'column';
		if (!isset($params['width']))
			$params['width'] = 550;
		if (!isset($params['height']))
			$params['height'] = 270;
		
		global $cookie;
		$id_employee = (int)($cookie->id_employee);
		$drawer = 'drawer.php?render='.$render.'&module='.Tools::getValue('module').'&type='.$params['type'].'&layers='.$params['layers'].'&id_employee='.$id_employee.'&id_lang='.$id_lang;
		if (isset($params['option']))
			$drawer .= '&option='.$params['option'];
			
		require_once(dirname(__FILE__).'/../modules/'.$render.'/'.$render.'.php');
		return call_user_func(array($render, 'hookGraphEngine'), $params, $drawer);
	}
	
	protected static function getEmployee($employee = null)
	{
		if (!$employee)
		{
			global $cookie;
			$employee = new Employee((int)($cookie->id_employee));
		}
		
		if (empty($employee->stats_date_from) OR empty($employee->stats_date_to) OR $employee->stats_date_from == '0000-00-00' OR $employee->stats_date_to == '0000-00-00')
		{
			if (empty($employee->stats_date_from) OR $employee->stats_date_from == '0000-00-00')
				$employee->stats_date_from = date('Y').'-01-01';
			if (empty($employee->stats_date_to)  OR $employee->stats_date_to == '0000-00-00')
				$employee->stats_date_to = date('Y').'-12-31';
			$employee->update();
		}
		return $employee;
	}
	
	public function getDate()
	{
		return self::getDateBetween($this->_employee);
	}
	
	public static function getDateBetween($employee = null)
	{
		$employee = self::getEmployee($employee);
		return ' \''.$employee->stats_date_from.' 00:00:00\' AND \''.$employee->stats_date_to.' 23:59:59\' ';
	}
	
	public function getLang()
	{
		return $this->_id_lang;
	}
}


