<?php

class AdminNotFoundControllerCore extends AdminController
{
	public function checkAccess()
	{
		return true;
	}

	public function viewAccess()
	{
		return true;
	}

	public function initContent()
	{
		$this->errors[] = Tools::displayError('Controller not found');
		$tpl_vars['controller'] = Tools::getvalue('controller');

		$this->context->smarty->assign($tpl_vars);
		parent::initContent();
	}
}
