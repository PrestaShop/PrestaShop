<?php

class AdminNotFoundControllerCore extends AdminController
{
	public function initContent()
	{
		$this->_errors[] = Tools::displayError('Controller not found');
		$tpl_vars['controller'] = Tools::getvalue('controller');

		$this->context->smarty->assign($tpl_vars);
		parent::initContent();
	}
}
