<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminDashboardControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'view';
		parent::__construct();
	}

	public function setMedia()
	{
		$admin_webpath = str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_);
		$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
		parent::setMedia();
		$this->addJqueryUI('ui.datepicker');
		$this->addJS(array(
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/d3.js',
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/nv.d3.js',
			_PS_JS_DIR_.'/admin-dashboard.js',
		));
		$this->addCSS(array(
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/css/nv.d3.css',
		));
	}
	
	public function renderView()
	{
		$translations = array(
			'Calendar' => $this->l('Calendar', 'AdminStatsTab'),
			'Day' => $this->l('Day', 'AdminStatsTab'),
			'Month' => $this->l('Month', 'AdminStatsTab'),
			'Year' => $this->l('Year', 'AdminStatsTab'),
			'From' => $this->l('From:', 'AdminStatsTab'),
			'To' => $this->l('To:', 'AdminStatsTab'),
			'Save' => $this->l('Save', 'AdminStatsTab')
		);
		
		$this->tpl_view_vars = array(
			'hookDashboardZoneOne' => Hook::exec('dashboardZoneOne'),
			'hookDashboardZoneTwo' => Hook::exec('dashboardZoneTwo'),
			'translations' => $translations,
			'action' => '#',
			'dashboard_use_push' => Configuration::get('PS_DASHBOARD_USE_PUSH'),
			'datepickerFrom' => Tools::getValue('datepickerFrom', $this->context->employee->stats_date_from),
			'datepickerTo' => Tools::getValue('datepickerTo', $this->context->employee->stats_date_to)
		);
		return parent::renderView();
	}

	public function ajaxProcessRefreshDashboard()
	{
		$id_module = null;
		if ($module = Tools::getValue('module'))
		{
			$module_obj = Module::getInstanceByName($module);
			if (Validate::isLoadedObject($module_obj))
				$id_module = $module_obj->id;
		}
		
		$params = array(
			'date_from' => $this->context->employee->stats_date_from,
			'date_to' => $this->context->employee->stats_date_to,
			'dashboard_use_push' => (int)Tools::getValue('dashboard_use_push')
		);
		
		die(Tools::jsonEncode(Hook::exec('dashboardData', $params, $id_module, true, true, (int)Tools::getValue('dashboard_use_push'))));
	}
	
	public function ajaxProcessGetBlogRss()
	{
		$return = array('has_errors' => false, 'rss' => array());
		if (!$this->isFresh('/config/xml/blog-'.$this->context->language->iso_code.'.xml', 604800))
		{
			if (!$this->refresh('/config/xml/blog-'.$this->context->language->iso_code.'.xml', 'https://api.prestashop.com/rss/blog/blog-'.$this->context->language->iso_code.'.xml'))
				$return['has_errors'] = true;		
		}
		
		if (!$return['has_errors'])
		{
			$rss = simpleXML_load_file(_PS_ROOT_DIR_.'/config/xml/blog-'.$this->context->language->iso_code.'.xml');
			$articles_limit = 3;
			foreach ($rss->channel->item as $item)
			{
				if ($articles_limit > 0)
					$return['rss'][] = array('title' => (string)$item->title, 'short_desc' => (string)$item->description);
				else
					break;
				$articles_limit --;
			}
		}
				
		die(Tools::jsonEncode($return));
	}
}