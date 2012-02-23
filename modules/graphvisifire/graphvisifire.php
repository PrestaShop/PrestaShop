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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class GraphVisifire extends ModuleGraphEngine
{
	private	$_xml;
	private	$_values = NULL;
	private	$_legend = NULL;
	private	$_titles = NULL;
	
    function __construct($type = null)
    {
		if ($type != null)
		{
			$this->_xml = '<vc:Chart xmlns:vc="clr-namespace:Visifire.Charts;assembly=Visifire.Charts" BorderThickness="0" AnimationEnabled="True" AnimationType="Type5"';
			if ($type == 'pie' || $type == 'line')
				$this->_xml .= ' Theme="Theme1" View3D="True"';
			else
				$this->_xml .= ' Theme="Theme2" ColorSet="Visifire2" UniqueColors="True"';
			$this->_xml .= '>';
			parent::__construct($type);
		}
		else
		{
	        $this->name = 'graphvisifire';
	        $this->tab = 'administration';
	        $this->version = 1.0;
			$this->author = 'PrestaShop';
			$this->need_instance = 0;

	        Module::__construct();
			
	        $this->displayName = $this->l('Visifire');
	        $this->description = $this->l('Visifire is a set of open source data visualization components - powered by Microsoft Silverlight 2 beta 2.');
		}
    }

	function install()
	{
		return (parent::install() AND $this->registerHook('GraphEngine'));
	}
    
	public static function hookGraphEngine($params, $drawer)
	{
		static $divid = 1;
		return '<script type="text/javascript" src="../modules/graphvisifire/visifire/Visifire.js"></script>
		<div id="VisifireChart'.$divid.'">
			<script language="javascript" type="text/javascript">
				var vChart = new Visifire("../modules/graphvisifire/visifire/Visifire.xap", '.$params['width'].', '.$params['height'].');
				vChart.setLogLevel(0);
				vChart.setDataUri(\''.$drawer.'\');
				vChart.render("VisifireChart'.$divid++.'");
			</script>
		</div>';
	}
			
	public function createValues($values)
	{
		$this->_values = array();
		if (!is_array($values[array_rand($values)]))
			foreach ($values as $value)
				$this->_values[] = $value;
		else
		{
			foreach ($values as $i => $layerValue)
			{
				$this->_values[$i] = array();
				foreach ($layerValue as $value)
					$this->_values[$i][] = $value;
			}
		}
	}
	
	public function setSize($width, $height)
	{
		// Unavailable
	}

	public function setLegend($legend)
	{
		$this->_legend = array();
		if (!is_array($legend[array_rand($legend)]))
			foreach ($legend as $label)
				$this->_legend[] = $label;
		else
		{
			foreach ($legend as $i => $layerlabel)
			{
				$this->_legend[$i] = array();
				foreach ($layerlabel as $label)
					$this->_legend[$i][] = $label;
			}
		}
	}

	public function setTitles($titles)
	{
		$this->_titles = $titles;
		if (isset($titles['main']) && !is_array($titles['main']))
			$this->_xml .= '<vc:Title Text="'.$titles['main'].'"/>';
		if (is_array($titles['main']) && isset($titles['main'][0]))
			$this->_xml .= '<vc:Title Text="'.$titles['main'][0].'"/>';
		if (isset($titles['x']))
			$this->_xml .= '<vc:AxisX Title="'.$titles['x'].'" />';
		if (isset($titles['y']))
			$this->_xml .= '<vc:AxisY Title="'.$titles['y'].'" />';
	}

	public function draw()
	{
		header('content-type: text/xml'); 
		if ($this->_values != NULL && $this->_legend != NULL)
		{
			if (!isset($this->_values[0]) || !is_array($this->_values[0]))
				$size = sizeof($this->_values);
			else
				$size = sizeof($this->_values[0]);
			if ($size == sizeof($this->_legend))
			{
				if (!is_array($this->_values[array_rand($this->_values)]))
				{
					$this->_xml .= '<vc:DataSeries RenderAs="'.$this->_type.'">';
					for ($i = 0; $i < $size; $i++)
					{
						$this->_xml .= '<vc:DataPoint ';
						$this->_xml .= 'AxisLabel=" '.str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('&', '&amp;', str_replace('&quot', "'", $this->_legend[$i])))).'" ';
						$this->_xml .= 'YValue="'.$this->_values[$i].'"';
						if ($this->_type == 'pie')
							$this->_xml .= ' ExplodeOffset="0.2"';
						$this->_xml .= '/>';
					}
					$this->_xml .= '</vc:DataSeries>';
				}
				else
				{
					foreach ($this->_values as $layer => $values)
					{
						$this->_xml .= '<vc:DataSeries Name="'.(isset($this->_titles['main'][$layer+1]) ? $this->_titles['main'][$layer+1] : '').'" RenderAs="'.$this->_type.'">';
						foreach ($values as $i => $value)
						{
							$this->_xml .= '<vc:DataPoint ';
							if ($layer == 0)
								$this->_xml .= 'AxisLabel=" '.str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('&', '&amp;', str_replace('&quot', "'", $this->_legend[$i])))).'" ';
							$this->_xml .= 'YValue="'.$value.'"';
							if ($this->_type == 'pie')
								$this->_xml .= ' ExplodeOffset="0.2"';
							$this->_xml .= '/>';
						}
						$this->_xml .= '</vc:DataSeries>';
					}
				}
			}
		}
		$this->_xml .= '</vc:Chart>';
		echo $this->_xml;
		exit(1);
	}
}


