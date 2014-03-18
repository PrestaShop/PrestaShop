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

class AdminPatternsControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->show_toolbar = false;
		$this->context = Context::getContext();
		
		parent::__construct();
	}
	
	public function viewAccess()
	{
		return true;
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Title'),
				'icon' => 'icon-envelope-alt'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('input type text'),
					'name' => 'type_text',
					'required' => false,
					'hint' => $this->l('hint input text')
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);

		return parent::renderForm();
	}
	
	public function renderList()
	{
		$this->fields_list = array(
			'id' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
			);
		
		return parent::renderList();
	}
	
	public function renderOptions()
	{
		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('General'),
				'icon' =>	'icon-cogs',
				'fields' =>	array(),
				'submit' => array('title' => $this->l('Save'))
			)
		);
		return parent::renderOptions();
	}

	public function initContent()
	{
		$this->display = 'view';
		$this->page_header_toolbar_title = $this->toolbar_title = $this->l('Kevin');
		
		parent::initContent();

		$this->content .= $this->renderForm();
		$this->content .= $this->renderList();
		$this->content .= $this->renderOptions();
		
		$this->context->smarty->assign(array('content' => $this->content));
	}
}
