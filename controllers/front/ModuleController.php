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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * The module controller call a module action. Module action method need to have the name :
 * 		public function actionMyAction(FrontController $controller)
 *
 * @since 1.5.0
 */
class ModuleControllerCore extends FrontController
{
	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		// Check module existence
		$name = Tools::getValue('module');
		if (!Validate::isModuleName($name) || !file_exists(_PS_MODULE_DIR_.$name))
			Tools::redirect('index');

		$module = Module::getInstanceByName($name);
		if (!$module->active)
			Tools::redirect('index');

		// Trigger module action
		$action = Tools::getValue('action');
		$method = 'action'.$action;

		$this->context->smarty->assign('page_name', 'module-'.$name.'-'.$action);

		if ($action && method_exists($module, $method))
			$this->setTemplate($module->$method());
		else
			die('Module action not found');
	}
}
