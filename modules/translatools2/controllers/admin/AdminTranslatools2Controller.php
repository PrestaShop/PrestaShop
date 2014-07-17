<?php
/**
* 2007-2014 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2014 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

class AdminTranslatools2Controller extends ModuleAdminController
{
	public function init()
	{
		$this->bootstrap = true;

		$this->action = Tools::getValue('action');
		if (!is_string($this->action) || !preg_match('/^\w+$/', $this->action))
			$this->action = 'default';

		$this->template = "{$this->action}.tpl";
		parent::init();
	}

	public function postProcess()
	{
		$methods = array(
			'beforeAll',
			Tools::strtolower($_SERVER['REQUEST_METHOD']).Tools::ucfirst($this->action).'Action'
		);

		$default_parameters = $this->getDefaultViewParameters();

		foreach ($methods as $method)
		{
			if (is_callable(array($this, $method)))
			{
				$view_parameters = $default_parameters;
				$template_params = $this->$method();

				if (is_array($template_params))
					$view_parameters = array_merge($view_parameters, $template_params);
				
				$this->context->smarty->assign($view_parameters);
			}
		}

		$this->errors = array_merge($this->errors, $this->module->errors);

		return parent::postProcess();
	}

	public function setMedia()
	{
		$this->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/translatools2.css');

		return parent::setMedia();
	}

	public function getDefaultViewParameters()
	{
		return array(
			'module_active' => $this->module->isActive(),
			'ctrl_url' => $this->context->link->getAdminLink('AdminTranslatools2')
		);
	}

	public function beforeAll()
	{
		
	}

	/**
	* Real Work Starts Here
	*/

	public function postActivateAction()
	{
		if ($this->module->activate())
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminTranslatools2'));
		else
			$this->template = 'default.tpl';
	}

	public function postDeactivateAction()
	{
		if ($this->module->deactivate())
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminTranslatools2'));
		else
			$this->template = 'default.tpl';
	}

	public function getDefaultAction()
	{
	}
}
