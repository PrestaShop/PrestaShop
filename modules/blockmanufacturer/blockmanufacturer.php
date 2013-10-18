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

class BlockManufacturer extends Module
{
    public function __construct()
    {
        $this->name = 'blockmanufacturer';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

        parent::__construct();

		$this->displayName = $this->l('Manufacturers block');
        $this->description = $this->l('Displays a block listing product manufacturers and/or brands.');
    }

	public function install()
	{
		Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT', true);
		Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT_NB', 5);
		Configuration::updateValue('MANUFACTURER_DISPLAY_FORM', true);
		return parent::install() &&
			$this->registerHook('leftColumn') && 
			$this->registerHook('header') &&
			$this->registerHook('actionObjectManufacturerDeleteAfter') &&
			$this->registerHook('actionObjectManufacturerAddAfter') &&
			$this->registerHook('actionObjectManufacturerUpdateAfter');
    }

	public function hookLeftColumn($params)
	{
		if (!$this->isCached('blockmanufacturer.tpl', $this->getCacheId()))
			$this->smarty->assign(array(
				'manufacturers' => Manufacturer::getManufacturers(),
				'text_list' => Configuration::get('MANUFACTURER_DISPLAY_TEXT'),
				'text_list_nb' => Configuration::get('MANUFACTURER_DISPLAY_TEXT_NB'),
				'form_list' => Configuration::get('MANUFACTURER_DISPLAY_FORM'),
				'display_link_manufacturer' => Configuration::get('PS_DISPLAY_SUPPLIERS'),
			));
		return $this->display(__FILE__, 'blockmanufacturer.tpl', $this->getCacheId());
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockManufacturers'))
		{
			$text_list = (int)(Tools::getValue('MANUFACTURER_DISPLAY_TEXT'));
			$text_nb = (int)(Tools::getValue('MANUFACTURER_DISPLAY_TEXT_NB'));
			$form_list = (int)(Tools::getValue('MANUFACTURER_DISPLAY_FORM'));
			if ($text_list && !Validate::isUnsignedInt($text_nb))
				$errors[] = $this->l('There is an invalid number of elements');
			elseif (!$text_list && !$form_list)
				$errors[] = $this->l('Please activate at least one system list.');
			else
			{
				Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT', $text_list);
				Configuration::updateValue('MANUFACTURER_DISPLAY_TEXT_NB', $text_nb);
				Configuration::updateValue('MANUFACTURER_DISPLAY_FORM', $form_list);
				$this->_clearCache('blockmanufacturer.tpl');
			}
			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->renderForm();
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockmanufacturer.css', 'all');
	}
	
	public function hookActionObjectManufacturerUpdateAfter($params)
	{
		$this->_clearCache('blockmanufacturer.tpl');
	}

	public function hookActionObjectManufacturerAddAfter($params)
	{
		$this->_clearCache('blockmanufacturer.tpl');
	}

	public function hookActionObjectManufacturerDeleteAfter($params)
	{
		$this->_clearCache('blockmanufacturer.tpl');
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
						'type' => 'switch',
						'label' => $this->l('Use a plain-text list'),
						'name' => 'MANUFACTURER_DISPLAY_TEXT',
						'desc' => $this->l('Display manufacturers in a plain-text list'),
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
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of elements to display'),
						'name' => 'MANUFACTURER_DISPLAY_TEXT_NB',
						'class' => 'fixed-width-xs'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Use a drop-down list'),
						'name' => 'MANUFACTURER_DISPLAY_FORM',
						'desc' => $this->l('Display manufacturers in a drop-down list.'),
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
					)
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-primary')
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockManufacturers';
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
			'MANUFACTURER_DISPLAY_TEXT' => Tools::getValue('MANUFACTURER_DISPLAY_TEXT', Configuration::get('MANUFACTURER_DISPLAY_TEXT')),
			'MANUFACTURER_DISPLAY_TEXT_NB' => Tools::getValue('MANUFACTURER_DISPLAY_TEXT_NB', Configuration::get('MANUFACTURER_DISPLAY_TEXT_NB')),
			'MANUFACTURER_DISPLAY_FORM' => Tools::getValue('MANUFACTURER_DISPLAY_FORM', Configuration::get('MANUFACTURER_DISPLAY_FORM')),
		);
	}
}
