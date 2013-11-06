<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;	
	
class ThemeConfigurator extends Module
{
	public function __construct()
	{
		$this->name = 'themeconfigurator';
		$this->tab = 'front_office_features';
		$this->version = '0.1';
		parent::__construct();	
		$this->displayName = $this->l('Theme configurator');
		$this->description = $this->l('Configure elements of your theme');
	}
	
	
	public function getContent()
	{
		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('PS_QUICK_VIEW', (int)Tools::getValue('quick_view'));
			foreach($this->getConfigurableModules() as $module)
			{
				if (!isset($module['is_module']) || !$module['is_module'] || !Validate::isModuleName($module['name']))
					continue;

				$module_instance = Module::getInstanceByName($module['name']);
				if ($module_instance === false || !is_object($module_instance))
					continue;

				$is_installed = (int)Validate::isLoadedObject($module_instance);
				if ($is_installed)
				{
					if (($active = (int)Tools::getValue($module['name'])) == $module_instance->active)
						continue;

					if ($active)
						$module_instance->enable();
					else
						$module_instance->disable();
				}
				else
					if ((int)Tools::getValue($module['name']))
						$module_instance->install();
			}			
		}
		return $this->renderForm();
	}

	public function renderForm()
	{

		$inputs = array();
		foreach ($this->getConfigurableModules() as $module)
			$inputs[] = array(
				'type' => 'switch',
				'label' => $module['label'],
				'name' => $module['name'],
				'desc' => (isset($module['desc']) ? $module['desc'] : ''),
				'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
				);
		
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => $inputs,
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'btn btn-primary')
				),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
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
	
	protected function getConfigurableModules()
	{
		return array(
			array(
				'label' => $this->l('Display the reinsurance block'),
				'name' => 'blockreinsurance',
				'desc' => '<a href="#">'.$this->l('Configure the reinsurance block').'</a>',
				'value' => (int)(Validate::isLoadedObject($module = Module::getInstanceByName('blockreinsurance')) && $module->active),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Display the social following links'),
				'name' => 'blocksocial',
				'desc' => '<a href="#">'.$this->l('Configure the social following links').'</a>',
				'value' => (int)(Validate::isLoadedObject($module = Module::getInstanceByName('blocksocial')) && $module->active),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Display contact information'),
				'name' => 'blockcontactinfos',
				'desc' => '<a href="#">'.$this->l('Configure the contact information of your store').'</a>',
				'value' => (int)(Validate::isLoadedObject($module = Module::getInstanceByName('blockcontactinfos')) && $module->active),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Display social buttons on the products page'),
				'name' => 'addsharethis',
				'desc' => '<a href="#">'.$this->l('Configure').'</a>',
				'value' => (int)(Validate::isLoadedObject($module = Module::getInstanceByName('addsharethis')) && $module->active),
				'is_module' => true,
			),
			array(
				'label' => $this->l('Enable Quick view'),
				'name' => 'quick_view',
				'value' => (int)Tools::getValue('PS_QUICK_VIEW', Configuration::get('PS_QUICK_VIEW'))
			)
		);
	}

	public function getConfigFieldsValues()
	{		
		$values = array();		
		foreach ($this->getConfigurableModules() as $module)
			$values[$module['name']] = $module['value'];
		return $values;

	}		
}
