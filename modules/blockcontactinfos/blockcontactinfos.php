<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;
	
class Blockcontactinfos extends Module
{
	public function __construct()
	{
		$this->name = 'blockcontactinfos';
		$this->author = 'PrestaShop';
		if (version_compare(_PS_VERSION_, '1.4.0.0') >= 0)
			$this->tab = 'front_office_features';
		else
			$this->tab = 'Blocks';
		$this->version = '1.0';

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Contact information block');
		$this->description = $this->l('This module will allow you to display your e-store\'s contact information in a customizable block.');
	}
	
	public function install()
	{
		return (parent::install() 
				&& Configuration::updateValue('BLOCKCONTACTINFOS_COMPANY', Configuration::get('PS_SHOP_NAME'))
				&& Configuration::updateValue('BLOCKCONTACTINFOS_ADDRESS', '') && Configuration::updateValue('BLOCKCONTACTINFOS_PHONE', '')
				&& Configuration::updateValue('BLOCKCONTACTINFOS_EMAIL', Configuration::get('PS_SHOP_EMAIL'))
				&& $this->registerHook('header') && $this->registerHook('footer'));
	}
	
	public function uninstall()
	{
		//Delete configuration			
		return (Configuration::deleteByName('BLOCKCONTACTINFOS_COMPANY')
				&& Configuration::deleteByName('BLOCKCONTACTINFOS_ADDRESS') && Configuration::deleteByName('BLOCKCONTACTINFOS_PHONE')
				&& Configuration::deleteByName('BLOCKCONTACTINFOS_EMAIL') && parent::uninstall());
	}
	
	public function getContent()
	{
		$html = '';
		// If we try to update the settings
		if (Tools::isSubmit('submitModule'))
		{	
			Configuration::updateValue('BLOCKCONTACTINFOS_COMPANY', Tools::getValue('blockcontactinfos_company'));
			Configuration::updateValue('BLOCKCONTACTINFOS_ADDRESS', Tools::getValue('blockcontactinfos_address'));
			Configuration::updateValue('BLOCKCONTACTINFOS_PHONE', Tools::getValue('blockcontactinfos_phone'));
			Configuration::updateValue('BLOCKCONTACTINFOS_EMAIL', Tools::getValue('blockcontactinfos_email'));
			$this->_clearCache('blockcontactinfos.tpl');
			$html .= $this->displayConfirmation($this->l('Configuration updated'));
		}

		$html .= $this->renderForm();
		
		return $html;
	}
	
	public function hookHeader()
	{
		$this->context->controller->addCSS(($this->_path).'blockcontactinfos.css', 'all');
	}
	
	public function hookFooter($params)
	{	
		if (!$this->isCached('blockcontactinfos.tpl', $this->getCacheId()))
			$this->smarty->assign(array(
				'blockcontactinfos_company' => Configuration::get('BLOCKCONTACTINFOS_COMPANY'),
				'blockcontactinfos_address' => Configuration::get('BLOCKCONTACTINFOS_ADDRESS'),
				'blockcontactinfos_phone' => Configuration::get('BLOCKCONTACTINFOS_PHONE'),
				'blockcontactinfos_email' => Configuration::get('BLOCKCONTACTINFOS_EMAIL')
			));
		return $this->display(__FILE__, 'blockcontactinfos.tpl', $this->getCacheId());
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Company name'),
						'name' => 'blockcontactinfos_company',
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Address'),
						'name' => 'blockcontactinfos_address',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Phone number'),
						'name' => 'blockcontactinfos_phone',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Email'),
						'name' => 'blockcontactinfos_email',
					),
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'blockcontactinfos_company' => Tools::getValue('blockcontactinfos_company', Configuration::get('BLOCKCONTACTINFOS_COMPANY')),
			'blockcontactinfos_address' => Tools::getValue('blockcontactinfos_address', Configuration::get('BLOCKCONTACTINFOS_ADDRESS')),
			'blockcontactinfos_phone' => Tools::getValue('blockcontactinfos_phone', Configuration::get('BLOCKCONTACTINFOS_PHONE')),
			'blockcontactinfos_email' => Tools::getValue('blockcontactinfos_email', Configuration::get('BLOCKCONTACTINFOS_EMAIL')),
		);
	}
}
