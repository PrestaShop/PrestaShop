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
		$this->addJS(array(
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/d3.js',
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/js/vendor/nv.d3.js'
		));
		$this->addCSS(array(
			__PS_BASE_URI__.$admin_webpath.'/themes/'.$this->bo_theme.'/css/nv.d3.css'
		));
	}
	
	public function renderView()
	{
		$this->tpl_view_vars = array(
			'hookDashboardZoneOne' => Hook::exec('dashboardZoneOne'),
			'hookDashboardZoneTwo' => Hook::exec('dashboardZoneTwo'),
		);
		return parent::renderView();
	}
}