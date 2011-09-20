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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(dirname(__FILE__).'/AdminStatsTab.php');
include_once(_PS_ADMIN_DIR_.'/tabs/AdminPreferences.php');

class AdminStatsConf extends AdminPreferences
{
	public function __construct()
	{
		parent::__construct();
		
		$autocleanPeriod = array(
			array('value' => 'never', 'name' => $this->l('Never')),
			array('value' => 'week', 'name' => $this->l('Week')),
			array('value' => 'month', 'name' => $this->l('Month')),
			array('value' => 'year', 'name' => $this->l('Year')),
		);

		$this->optionsList = array(
			'general' => array(
				'title' =>	$this->l('General'),
				'icon' =>	'tab-preferences',
				'fields' =>	array(
					'PS_STATS_RENDER' => array('title' => $this->l('Graph engine'), 'validation' => 'isGenericName', 'cast' => 'strval', 'type' => 'selectEngine'),
					'PS_STATS_GRID_RENDER' => array('title' => $this->l('Grid engine'), 'validation' => 'isGenericName', 'cast' => 'strval', 'type' => 'selectGrid'),
					'PS_STATS_OLD_CONNECT_AUTO_CLEAN' => array('title' => $this->l('Auto-clean period'), 'validation' => 'isGenericName', 'type' => 'select', 'list' => $autocleanPeriod, 'identifier' => 'value'),
				),
			),
 		);
	}

	public function displayOptionTypeSelectEngine($key, $field, $value)
	{
		$listEngineDescription = array();
		$listEngine = array();
		foreach (ModuleGraphEngine::getGraphEngines() as $k => $engine)
		{
			$listEngine[] = array(
				'name' =>	$engine[0],
				'value' =>	$k,
			);
			$listEngineDescription[$k] = $engine[1];
		}
		
		$field['list'] = $listEngine;
		$field['identifier'] = 'value';
		$field['js'] = '$(\'#render_engine_description\').html(engineDescriptions[$(this).val()])';

		echo '<script type="text/javascript">var engineDescriptions = '.Tools::jsonEncode($listEngineDescription).';</script>';
		$this->displayOptionTypeSelect($key, $field, $value);
		echo '<div id="render_engine_description">'.$listEngineDescription[$value].'</div>';
	}
	
	public function displayOptionTypeSelectGrid($key, $field, $value)
	{
		$listEngineDescription = array();
		$listEngine = array();
		foreach (ModuleGridEngine::getGridEngines() as $k => $engine)
		{
			$listEngine[] = array(
				'name' =>	$engine[0],
				'value' =>	$k,
			);
			$listEngineDescription[$k] = $engine[1];
		}
		
		$field['list'] = $listEngine;
		$field['identifier'] = 'value';
		$field['js'] = '$(\'#render_grid_description\').html(gridDescriptions[$(this).val()])';

		echo '<script type="text/javascript">var gridDescriptions = '.Tools::jsonEncode($listEngineDescription).';</script>';
		$this->displayOptionTypeSelect($key, $field, $value);
		echo '<div id="render_grid_description">'.$listEngineDescription[$value].'</div>';
	}
}
