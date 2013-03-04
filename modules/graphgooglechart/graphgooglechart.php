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

class GraphGoogleChart extends ModuleGraphEngine
{
	private $_width;
	private $_height;
	private $_values;
	private $_legend;
	private $_titles;
	
    function __construct($type = null)
    {
		if ($type != null)
		{
			parent::__construct($type);
		}
		else
		{
			$this->name = 'graphgooglechart';
			$this->tab = 'administration';
			$this->version = 1.0;
			$this->author = 'PrestaShop';
			$this->need_instance = 0;

			Module::__construct();
				
			$this->displayName = $this->l('Google Chart');
			$this->description = $this->l('The Google Chart API lets you dynamically generate charts.');
		}
    }

	function install()
	{
		return (parent::install() AND $this->registerHook('GraphEngine'));
	}
    
	public static function hookGraphEngine($params, $drawer)
	{
		return '<img src="'.$drawer.'&width='.$params['width'].'&height='.$params['height'].'" />';
	}
	
	
	public function createValues($values)
	{		
		$this->_values = $values;
	}
	
	public function setSize($width, $height)
	{
		$this->_width = $width;
		$this->_height = $height;
	}
	
	public function setLegend($legend)
	{
		$this->_legend = $legend;
	}

	public function setTitles($titles)
	{
		$this->_titles = $titles;
	}
	
	private function getChbh($sizeof_values)
	{
		$chbh = 12;

		if ($sizeof_values < 25)
			$chbh += 4;
		if ($sizeof_values < 20)
			$chbh += 4;
		if ($sizeof_values < 15)
			$chbh += 8;
		if ($sizeof_values < 10)
			$chbh += 14;
		return ($chbh);
	}

	private function drawColumn($max_y)
	{
		if (!isset($this->_values[0]) || !is_array($this->_values[0]))
			$sizeof_values = sizeof($this->_values);
		else
			$sizeof_values = sizeof($this->_values[0]);
		$url = 'bvs&chxt=x,y&chxr=1,0,'.$max_y.'&chbh='.$this->getChbh($sizeof_values).'&chg=0,12.5&chxl=0:|';
		for ($i = 0; $i < $sizeof_values; $i++)
			if (!isset($this->_values[0]) || !is_array($this->_values[0]))
			{
				if (isset($this->_values[$i]))
					$this->_values[$i] = ($this->_values[$i] * 100) / $max_y;
				else
					$this->_values[$i] = 0;
			}
			else
				foreach ($this->_values as $k => $value)
				{
					if (!isset($this->_values[$k][$i]))
						$this->_values[$k][$i] = 0;
					$this->_values[$k][$i] = ($this->_values[$k][$i] * 100) / $max_y;
				}
		return ($url);
	}
	
	private function drawLine($max_y)
	{
		return ('lc'./*&chxt=x,y*/'&chbh='.$this->getChbh(sizeof($this->_values)).'&chg=0,12.5&chxl=0:|');
	}

	private function drawPie()
	{
		return ('p3&chl=');
	}

	public function draw()
	{
		$url = 'http://chart.apis.google.com/chart?cht=';
		$legend = '';
		$values = '';
		$scale = '';

		switch ($this->_type)
		{
			case 'pie':
				$url .= $this->drawPie();
				break;
			case 'line':
				$url .= $this->drawLine($this->getYMax($this->_values));
			case 'column':
			default:
				$url .= $this->drawColumn($this->getYMax($this->_values));
				break;
		}

		foreach ($this->_legend as $label)
			$legend .= $label.'|';
		$url .= htmlentities(urlencode(html_entity_decode(rtrim($legend, '|'))));

		if (!isset($this->_values[0]) || !is_array($this->_values[0]))
		{
			foreach ($this->_values as $label)
				$values .= ($label ? $label : '0').',';
			$url .= '&chd=t:'.urlencode(rtrim($values, ','));
		}
		else
		{
			$i = 0;
			$url .= '&chd=t:';
			foreach ($this->_values as $val)
			{
				$values = '';
				if ($i++ > 0)
					$url .= '|';
				foreach ($val as $label)
					$values .= ($label ? $label : '0').',';
				$url .= urlencode(rtrim($values, ','));
			}
		}
		
		$url .= '&chs='.(int)($this->_width).'x'.(int)($this->_height);
		if (!isset($this->_values[0]) || !is_array($this->_values[0]))
			$url .= (isset($this->_titles['main'])) ? '&chtt='.urlencode($this->_titles['main']) : '';
		else
		{
			$url .= $this->getStringColor(sizeof($this->_values));
			$i = 0;
			foreach ($this->_titles['main'] as $val)
			{
				if ($i == 0 && !empty($this->_titles['main']))
					$url .= '&chtt='.urlencode($this->_titles['main'][$i]);
				else if ($i == 1)
					$url .= '&chdl=';
				else if ($i > 1)
					$url .= '|';
				if ($i != 0)
					$url .= urlencode($this->_titles['main'][$i]);
				$i++;
			}
		}
		header("Content-type: image/png");
		readfile($url);
	}
	
	private function getYMax($values)
	{
		$max = 0;
		if (!isset($this->_values[0]) || !is_array($this->_values[0]))
		{
			foreach ($values as $k => $val)
				if ($val > $max)
					$max = $val;
		}
		else
		{
			foreach ($values as $value)
				foreach ($value as $val)
					if ($val > $max)
						$max = $val;
		}
		return ($max < 4) ? 4 : (round($max, 0));
	}
	
	private function getStringColor($nb_colors)
	{
		$tabColors = array('ffb649', '427fc3', 'ff0000', '55a339', 'ff2398');
		$color = '';
		if ($nb_colors > 1)
		{
			$color = '&chco=';
			for ($i = 0; $i < $nb_colors; $i++)
			{
				if ($i > 0)
					$color .= ',';
				$color .= $tabColors[$i % 5];
			}
		}
		return $color;
	}
}


