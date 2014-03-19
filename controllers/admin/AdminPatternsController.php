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

		$this->fields_value = array(
			'type_text' => 'with value',
			'type_text_readonly' => 'with value that you can\'t edit'
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('patterns of helper form.tpl'),
				'icon' => 'icon-envelope-alt'
			),
			'description' => 'You can use image instead of icon for the title.',
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('simple input text'),
					'name' => 'type_text'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input text with desc'),
					'name' => 'type_text_desc',
					'desc' => $this->l('desc input text')
				),
				array(
					'type' => 'text',
					'label' => $this->l('required input text'),
					'name' => 'type_text_required',
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('input text with hint'),
					'name' => 'type_text_hint',
					'hint' => $this->l('hint input text')
				),
				array(
					'type' => 'text',
					'label' => $this->l('input text with prefix'),
					'name' => 'type_text_prefix',
					'prefix' => 'prefix'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input text with suffix'),
					'name' => 'type_text_suffix',
					'suffix' => 'suffix'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input text with placeholder'),
					'name' => 'type_text_placeholder',
					'placeholder' => 'placeholder'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input text with character counter'),
					'name' => 'type_text_maxchar',
					'maxchar' => 30
				),
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('input text multilang'),
					'name' => 'type_text_multilang'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input readonly'),
					'readonly' => true,
					'name' => 'type_text_readonly'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input fixed-width-xs'),
					'name' => 'type_text_xs',
					'class' => 'input fixed-width-xs'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input fixed-width-sm'),
					'name' => 'type_text_sm',
					'class' => 'input fixed-width-sm'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input fixed-width-md'),
					'name' => 'type_text_md',
					'class' => 'input fixed-width-md'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input fixed-width-lg'),
					'name' => 'type_text_lg',
					'class' => 'input fixed-width-lg'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input fixed-width-xl'),
					'name' => 'type_text_xl',
					'class' => 'input fixed-width-xl'
				),
				array(
					'type' => 'text',
					'label' => $this->l('input fixed-width-xxl'),
					'name' => 'type_text_xxl',
					'class' => 'fixed-width-xxl'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('input tags'),
					'name' => 'type_text_tags'
				),
				array(
					'type' => 'textbutton',
					'label' => $this->l('input with button'),
					'name' => 'type_textbutton',
					'button' => array(
						'label' => $this->l('do something'),
						'attributes' => array(
							'onclick' => 'alert(\'something done\');'
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('select'),
					'name' => 'type_select',
					'options' => array(
						'query' => Zone::getZones(),
						'id' => 'id_zone',
						'name' => 'name'
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('select with chosen'),
					'name' => 'type_select_chosen',
					'class' => 'chosen',
					'options' => array(
						'query' => Country::getCountries((int)Context::getContext()->cookie->id_lang),
						'id' => 'id_zone',
						'name' => 'name'
					),
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);

		return parent::renderForm();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addjQueryPlugin('tagify', null, false);
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
				'fields' =>	array(

					),
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
