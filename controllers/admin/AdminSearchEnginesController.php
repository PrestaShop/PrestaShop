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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSearchEnginesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'search_engine';
	 	$this->className = 'SearchEngine';
	 	$this->edit = true;
		$this->delete = true;
	 	$this->lang = false;
		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldsDisplay = array(
			'id_search_engine' => array('title' => $this->l('ID'), 'width' => 25),
			'server' => array('title' => $this->l('Server'), 'width' => 200),
			'getvar' => array('title' => $this->l('GET variable'), 'width' => 40)
		);

		$this->template = 'adminSearchEngines.tpl';

		parent::__construct();
	}

	public function postProcess()
	{
		parent::postProcess();
	}

	public function displayForm($is_main_tab = true)
	{
		parent::displayForm($is_main_tab);

		if (!($obj = $this->loadObject(true)))
			return;

		$this->context->smarty->assign('tab_form', array(
			'current' => self::$currentIndex,
			'table' => $this->table,
			'token' => $this->token,
			'id' => $obj->id,
			'server' => $this->getFieldValue($obj, 'server'),
			'getvar' => $this->getFieldValue($obj, 'getvar')
		));
	}

	public function initContent()
	{
		if ($this->display != 'edit')
			$this->display = 'list';

		parent::initContent();
	}

}


