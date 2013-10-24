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

class BlockNewProducts extends Module
{
	public function __construct()
	{
		$this->name = 'blocknewproducts';
		$this->tab = 'front_office_features';
		$this->version = '1.5';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('New products block');
		$this->description = $this->l('Displays a block featuring your store\'s newest products.');
	}

	public function install()
	{
		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !Configuration::updateValue('NEW_PRODUCTS_NBR', 5)
			|| !$this->registerHook('displayHomeTab')
			|| !$this->registerHook('displayHomeTabContent')
		)
			return false;
		$this->_clearCache('blocknewproducts.tpl');
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('blocknewproducts.tpl');
		$this->_clearCache('tab.tpl');
		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockNewProducts'))
		{
			if (!($productNbr = Tools::getValue('NEW_PRODUCTS_NBR')) || empty($productNbr))
				$output .= $this->displayError($this->l('Please complete the "products to display" field.'));
			elseif ((int)($productNbr) == 0)
				$output .= $this->displayError($this->l('Invalid number.'));
			else
			{
				Configuration::updateValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', (int)(Tools::getValue('PS_BLOCK_NEWPRODUCTS_DISPLAY')));
				Configuration::updateValue('NEW_PRODUCTS_NBR', (int)($productNbr));
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
	}
	
	public function hookRightColumn($params)
	{
		if (!$this->isCached('blocknewproducts.tpl', $this->getCacheId()))
		{
			if (!Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))
				return;
			$newProducts = Product::getNewProducts((int) $params['cookie']->id_lang, 0, (int) Configuration::get('NEW_PRODUCTS_NBR'));
			if (!$newProducts)
				return;

			$this->smarty->assign(array(
				'new_products' => $newProducts,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
			));
		}
		return $this->display(__FILE__, 'blocknewproducts.tpl', $this->getCacheId());
	}

	protected function getCacheId($name = null)
	{
		if ($name === null)
			$name = 'blocknewproducts';
		return parent::getCacheId($name.'|'.date('Ymd'));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookdisplayHomeTab($params)
	{
		return $this->display(__FILE__, 'tab.tpl', $this->getCacheId('blocknewproducts-tab'));
	}
	
	public function hookdisplayHomeTabContent($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'blocknewproducts.css', 'all');
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('blocknewproducts.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('blocknewproducts.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('blocknewproducts.tpl');
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
						'label' => $this->l('Products to display'),
						'name' => 'NEW_PRODUCTS_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products to be displayed in this block.')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Always display this block'),
						'name' => 'PS_BLOCK_NEWPRODUCTS_DISPLAY',
						'desc' => $this->l('Show the block even if no products are available.'),
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
				'class' => 'btn btn-default')
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBlockNewProducts';
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
			'PS_BLOCK_NEWPRODUCTS_DISPLAY' => Tools::getValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')),
			'NEW_PRODUCTS_NBR' => Tools::getValue('NEW_PRODUCTS_NBR', Configuration::get('NEW_PRODUCTS_NBR')),
		);
	}
}
