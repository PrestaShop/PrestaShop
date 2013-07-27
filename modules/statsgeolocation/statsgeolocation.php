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

class StatsGeoLocation extends Module
{
	private $_map_path = 'img/map.png';
	private $_cross_path = 'img/cross.png';

	function __construct()
	{
		$this->name = 'statsgeolocation';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = $this->l('Geolocation');
		$this->description = $this->l('Display your customers\' origin');
	}

	function install()
	{
		$countries = array('AT' => array('x' => 294, 'y' => 68),
					'AU' => array('x' => 534, 'y' => 228),
					'BE' => array('x' => 276, 'y' => 62),
					'BO' => array('x' => 135, 'y' => 210),
					'CA' => array('x' => 84, 'y' => 45),
					'CH' => array('x' => 281, 'y' => 69),
					'CI' => array('x' => 253, 'y' => 156),
					'CN' => array('x' => 470, 'y' => 99),
					'CZ' => array('x' => 293, 'y' => 63),
					'DE' => array('x' => 285, 'y' => 61),
					'DK' => array('x' => 284, 'y' => 51),
					'ES' => array('x' => 260, 'y' => 85),
					'FI' => array('x' => 310, 'y' => 35),
					'FR' => array('x' => 271, 'y' => 69),
					'GB' => array('x' => 265, 'y' => 55),
					'GR' => array('x' => 308, 'y' => 87),
					'HK' => array('x' => 491, 'y' => 123),
					'IE' => array('x' => 253, 'y' => 58),
					'IL' => array('x' => 334, 'y' => 106),
					'IT' => array('x' => 292, 'y' => 80),
					'JP' => array('x' => 531, 'y' => 92),
					'KR' => array('x' => 509, 'y' => 93),
					'LU' => array('x' => 277, 'y' => 63),
					'NG' => array('x' => 282, 'y' => 153),
					'NL' => array('x' => 278, 'y' => 58),
					'NO' => array('x' => 283, 'y' => 41),
					'NZ' => array('x' => 590, 'y' => 264),
					'PL' => array('x' => 300, 'y' => 59),
					'PT' => array('x' => 251, 'y' => 86),
					'TG' => array('x' => 267, 'y' => 154),
					'SE' => array('x' => 294, 'y' => 40),
					'SG' => array('x' => 475, 'y' => 169),
					'US' => array('x' => 71, 'y' => 87),
					'ZA' => array('x' => 311, 'y' => 239));

		if ( !parent::install() OR !$this->registerHook('AdminStatsModules'))
			return false;
		if (!Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'location_coords` (
			`id_location_coords` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
			`x` int(4) NOT NULL,
			`y` int(4) NOT NULL,
			`id_country` INTEGER UNSIGNED NOT NULL,
			PRIMARY KEY(`id_location_coords`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;

		$flag = 0;
		$query = 'INSERT INTO `'._DB_PREFIX_.'location_coords` (`x`, `y`, `id_country`) VALUES ';
		$result = Db::getInstance()->executeS('SELECT `id_country`, `iso_code` FROM `'._DB_PREFIX_.'country`;');
		foreach ($result as $index => $row)
		{
			if (isset($countries[$row['iso_code']]))
			{
				if ($flag)
					$query .= ', ';
				$query .= '(\''.$countries[$row['iso_code']]['x'].'\', \''.$countries[$row['iso_code']]['y'].'\', \''.$row['id_country'].'\')';
				$flag = 1;
			}
		}
		return Db::getInstance()->execute($query.';');
	}
	
	function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return (Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'location_coords`'));
	}

	function hookAdminStatsModules()
	{
		$this->_html = '
		<fieldset class="width3"><legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>
			<center>
				<p><img src="../img/admin/down.gif" />'.$this->l('This module shows the distribution of the countries of your customers by displaying different sized points on the worldmap below. See the fame of your website all around the world and which continent you have yet to conquer.').'</p>
			</center>
			<p class="space">
				<img src="'.$this->_path.'drawer.php" alt="" title="" />
			</p>
			
		</fieldset><br />
		<fieldset class="width3"><legend><img src="../img/admin/comment.gif" /> '.$this->l('Guide').'</legend>
		<h2>'.$this->l('Open to the world').'</h2>
			<p>
				<ul>
					<li class="bullet">'.$this->l('Add new languages to your shop if you see that a sufficient part of your customers come from a foreign country.').'</li>
				<li class="bullet">'.$this->l('Enlarge your shipping area to meet the potential demand.').'</li>
				</ul>
			</p>
		</fieldset>';
		return $this->_html;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$map_size;
		$cross_size;
		$id_lang = (int)$this->context->language->id;
		$wait = $this->l('Please wait...');

		if ((file_exists('../modules/'.$this->name.'/'.$this->_map_path) == FALSE) || 
			(file_exists('../modules/'.$this->name.'/'.$this->_cross_path) == FALSE) ||
			(!($map_size = getimagesize('../modules/'.$this->name.'/'.$this->_map_path))) ||
			(!($cross_size = getimagesize('../modules/'.$this->name.'/'.$this->_cross_path))))
			return ("Error: cannot load images");

		$output = '<script type="text/javascript" src="'.$this->_path.'statsgeolocation.js"></script>
				<script type="text/javascript">$(document).ready(_firstOfAll);</script>

				<form><fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Update coordinates').'</legend>
				<div style="font-weight:bold;" id="divtitle">'.$this->l('Click on a country\'s name and define its position on the map').'</div><br />
				<div id="reference" onclick="clickOnImage(event)" style="background-image:url(\''.$this->_path.$this->_map_path.'\');width:'.$map_size[0].'px; height:'.$map_size[1].'px; z-index:5;">
				  <div id="marker" style= "display:none;background-image:url(\''.$this->_path.$this->_cross_path.'\');width:0px; height:0px; z-index:20; position:relative;"></div>
				</div>
				<div id="belowmap">'.$wait.'</div>
				<noscript>You should enable javascript to configure this module</noscript> 

				<input type="hidden" id="opt" value="1" />
				<input type="hidden" id="id_lang" value="'.$id_lang.'" />
				<input type="hidden" id="lang_info" value="'.$this->l('Click on the map to set the position of:').'" />
				<input type="hidden" id="lang_error" value="'.$this->l('Error: click on the map or the cancel button').'" />
				<input type="hidden" id="lang_cancel" value="'.$this->l('Cancel').'" />
				<input type="hidden" id="lang_validate" value="'.$this->l('Validate').'" />
				<input type="hidden" id="marker_size" value="'.$cross_size[0].'" />
				<input type="hidden" id="form_x" value="0" />
				<input type="hidden" id="form_y" value="0" />
				<input type="hidden" id="wait" value="'.$wait.'" />
				<input type="hidden" id="country_selected" value="0" />
				</fieldset></form>';
		return $output;
	}
}

