<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminMarketingControllerCore extends AdminController
{

	public function __construct()
	{
		$this->bootstrap = true;
		parent::__construct();
	}

	public function initContent()
	{
		$this->display = 'view';
		return parent::initContent();
	}
	
	public function initToolbarTitle()
	{
		$this->toolbar_title = array_unique($this->breadcrumbs);
	}

	public function initPageHeaderToolbar()
	{
		parent::initPageHeaderToolbar();
		$this->page_header_toolbar_btn = array();
	}
	
	public function initToolbar()
	{
		return false;
	}
	
	public function renderView()
	{
		$this->tpl_view_vars = array(
			'modules_list' => $this->renderModulesList(),
		);
		return parent::renderView();
	}

	public function ajaxProcessGetModuleQuickView()
	{
		$modules = Module::getModulesOnDisk();

		foreach ($modules as $module)
			if ($module->name == Tools::getValue('module'))
				break;

		$this->context->smarty->assign(array(
			'displayName' => $module->displayName,
			'image' => $module->image,
			'nb_rates' => (int)$module->nb_rates[0],
			'avg_rate' => (int)$module->avg_rate[0],
			'badges' => $module->badges,
			'compatibility' => $module->compatibility,
			'description_full' => $module->description_full,
			'additional_description' => $module->additional_description,
			'url' => $module->url
		));
		$this->smartyOutputContent('controllers/modules/quickview.tpl');
		die(1);
	}
}
