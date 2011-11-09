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

class AdminStatsConfControllerCore extends AdminPreferencesControllerCore
{
	public function __construct()
	{
		$autoclean_period = array(
			array('value' => 'never', 'name' => $this->l('Never')),
			array('value' => 'week', 'name' => $this->l('Week')),
			array('value' => 'month', 'name' => $this->l('Month')),
			array('value' => 'year', 'name' => $this->l('Year')),
		);

		$list_engine_description_stats = array();
		$list_select_stats = array();
		foreach (ModuleGraphEngine::getGraphEngines() as $k => $engine)
		{
			$list_select_stats[] = array(
				'name' =>	$engine[0],
				'value' =>	$k,
			);
			$list_engine_description_stats[$k] = $engine[1];
		}

		$list_engine_description_grid = array();
		$list_select_grid = array();
		foreach (ModuleGridEngine::getGridEngines() as $k => $engine)
		{
			$list_select_grid[] = array(
				'name' =>	$engine[0],
				'value' =>	$k,
			);
			$list_engine_description_grid[$k] = $engine[1];
		}

		$this->options = array(
			'general' => array(
				'title' =>	$this->l('General'),
				'icon' =>	'tab-preferences',
				'fields' =>	array(
					'PS_STATS_RENDER' => array(
						'title' => $this->l('Graph engine'),
						'validation' => 'isGenericName',
						'cast' => 'strval',
						'type' => 'selectEngine',
						'list' => $list_select_stats,
						'identifier' => 'value',
						'js' => '$(\'#render_engine_description\').html(engineDescriptions[$(this).val()])'
					),
					'PS_STATS_GRID_RENDER' => array(
						'title' => $this->l('Grid engine'),
						'validation' => 'isGenericName',
						'cast' => 'strval',
						'type' => 'selectGrid',
						'list' => $list_select_grid,
						'identifier' => 'value',
						'js' => '$(\'#render_engine_description\').html(engineDescriptions[$(this).val()])'
					),
					'PS_STATS_OLD_CONNECT_AUTO_CLEAN' => array(
						'title' => $this->l('Auto-clean period'),
						'validation' => 'isGenericName',
						'type' => 'select',
						'list' => $autoclean_period,
						'identifier' => 'value'
					)
				),
				'submit' => array()
			)
 		);
 
 		$this->tpl_option_vars = array (
 			'list_engine_description_stats_js' => Tools::jsonEncode($list_engine_description_stats),
 			'list_engine_description_stats' => $list_engine_description_stats,
 			'list_engine_description_grid_js' => Tools::jsonEncode($list_engine_description_grid),
 			'list_engine_description_grid' => $list_engine_description_grid
 		);
 
		parent::__construct();
	}
}
