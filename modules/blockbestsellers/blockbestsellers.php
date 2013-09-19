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
	
class BlockBestSellers extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'blockbestsellers';
		$this->tab = 'front_office_features';
		$this->version = '1.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Top-seller block');
		$this->description = $this->l('Add a block displaying your store\'s top-selling products.');
	}

	/**
	 * @see ModuleCore::install()
	 */
	public function install()
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
		
		if (!parent::install()
			|| !$this->registerHook('rightColumn')
			|| !$this->registerHook('header')
			|| !$this->registerHook('actionOrderStatusPostUpdate')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !ProductSale::fillProductSales()
		)
			return false;
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
		
		return parent::uninstall();
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	public function hookActionOrderStatusPostUpdate($params)
	{
		$this->_clearCache('blockbestsellers.tpl');
		$this->_clearCache('blockbestsellers-home.tpl');
	}

	/**
	 * Called in administration -> module -> configure
	 */
	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBestSellers'))
		{
			Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)Tools::getValue('PS_BLOCK_BESTSELLERS_DISPLAY'));
			$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
		return $output.$this->renderForm();
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-star-empty'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Always display this block'),
						'name' => 'PS_BLOCK_BESTSELLERS_DISPLAY',
						'is_bool' => true,
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
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBestSellers';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS($this->_path.'blockbestsellers.css', 'all');
	}

	public function hookHome($params)
	{
		if (!$this->isCached('blockbestsellers-home.tpl', $this->getCacheId('blockbestsellers-home')))
		{
			$best_sellers = $this->getBestSellers($params);
			if ($best_sellers === false)
				return;

			$this->smarty->assign(array(
				'best_sellers' => $best_sellers,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))));
		}
		return $this->display(__FILE__, 'blockbestsellers-home.tpl', $this->getCacheId('blockbestsellers-home'));
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('blockbestsellers.tpl', $this->getCacheId('blockbestsellers')))
		{
			$best_sellers = $this->getBestSellers($params);
			if ($best_sellers === false)
				return;

			$this->smarty->assign(array(
				'best_sellers' => $best_sellers,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'smallSize' => Image::getSize(ImageType::getFormatedName('small'))
			));
		}
		return $this->display(__FILE__, 'blockbestsellers.tpl', $this->getCacheId('blockbestsellers'));
	}
		
	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}
	
	protected function getBestSellers($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!($result = ProductSale::getBestSalesLight((int)$params['cookie']->id_lang, 0, 5)))
			return (Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY') ? array() : false);

		$bestsellers = array();
		$currency = new Currency($params['cookie']->id_currency);
		$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
		foreach ($result as &$row)
			$row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);
		return $result;
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'PS_BLOCK_BESTSELLERS_DISPLAY' => Tools::getValue('PS_BLOCK_BESTSELLERS_DISPLAY', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')),
		);
	}
}
