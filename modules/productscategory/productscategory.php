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

class productsCategory extends Module
{
 	private $_html;

	public function __construct()
 	{
 	 	$this->name = 'productscategory';
 	 	$this->version = '1.4';
		$this->author = 'PrestaShop';
 	 	$this->tab = 'front_office_features';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = $this->l('Products Category');
		$this->description = $this->l('Display products of the same category on the product page.');
		
		if (!$this->isRegisteredInHook('header'))
			$this->registerHook('header');
 	}

	public function install()
	{
		Configuration::updateValue('PRODUCTSCATEGORY_DISPLAY_PRICE', 0);
		$this->_clearCache('productscategory.tpl');
	 	return (parent::install()
			&& $this->registerHook('productfooter')
			&& $this->registerHook('header')
			&& $this->registerHook('addproduct')
			&& $this->registerHook('updateproduct')
			&& $this->registerHook('deleteproduct')
		);
	}
	
	public function uninstall()
	{
		Configuration::deleteByName('PRODUCTSCATEGORY_DISPLAY_PRICE');
		$this->_clearCache('productscategory.tpl');
	 	return parent::uninstall();
	}
	
	public function getContent()
	{
		$this->_html = '';
		if (Tools::isSubmit('submitCross') AND Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE') != 0 AND Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE') != 1)
			$this->_html .= $this->displayError('Invalid displayPrice');
		elseif (Tools::isSubmit('submitCross'))
		{
			Configuration::updateValue('PRODUCTSCATEGORY_DISPLAY_PRICE', Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE'));
			$this->_clearCache('productscategory.tpl');
			$this->_html .= $this->displayConfirmation($this->l('Settings updated successfully'));
		}
		$this->_html .= $this->renderForm();
		
		return $this->_html;
	}
	
	private function getCurrentProduct($products, $id_current)
	{
		if ($products)
			foreach ($products AS $key => $product)
				if ($product['id_product'] == $id_current)
					return $key;
		return false;
	}
	
	public function hookProductFooter($params)
	{
		$id_product = (int)$params['product']->id;
		$product = $params['product'];
		
		$cache_id = 'productscategory|'.$id_product.'|'.(isset($params['category']->id_category) ? (int)$params['category']->id_category : $product->id_category_default);

		if (!$this->isCached('productscategory.tpl', $this->getCacheId($cache_id)))
		{
			/* If the visitor has came to this product by a category, use this one */
			if (isset($params['category']->id_category))
				$category = $params['category'];
			/* Else, use the default product category */
			else
			{
				if (isset($product->id_category_default) AND $product->id_category_default > 1)
					$category = new Category((int)$product->id_category_default);
			}
			
			if (!Validate::isLoadedObject($category) OR !$category->active) 
				return;

			// Get infos
			$categoryProducts = $category->getProducts($this->context->language->id, 1, 100); /* 100 products max. */
			$sizeOfCategoryProducts = (int)sizeof($categoryProducts);
			$middlePosition = 0;
			
			// Remove current product from the list
			if (is_array($categoryProducts) AND sizeof($categoryProducts))
			{
				foreach ($categoryProducts AS $key => $categoryProduct)
					if ($categoryProduct['id_product'] == $id_product)
					{
						unset($categoryProducts[$key]);
						break;
					}

				$taxes = Product::getTaxCalculationMethod();
				if (Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE'))
					foreach ($categoryProducts AS $key => $categoryProduct)
						if ($categoryProduct['id_product'] != $id_product)
						{
							if ($taxes == 0 OR $taxes == 2)
								$categoryProducts[$key]['displayed_price'] = Product::getPriceStatic((int)$categoryProduct['id_product'], true, NULL, 2);
							elseif ($taxes == 1)
								$categoryProducts[$key]['displayed_price'] = Product::getPriceStatic((int)$categoryProduct['id_product'], false, NULL, 2);
						}
			
				// Get positions
				$middlePosition = round($sizeOfCategoryProducts / 2, 0);
				$productPosition = $this->getCurrentProduct($categoryProducts, (int)$id_product);
			
				// Flip middle product with current product
				if ($productPosition)
				{
					$tmp = $categoryProducts[$middlePosition-1];
					$categoryProducts[$middlePosition-1] = $categoryProducts[$productPosition];
					$categoryProducts[$productPosition] = $tmp;
				}
			
				// If products tab higher than 30, slice it
				if ($sizeOfCategoryProducts > 30)
				{
					$categoryProducts = array_slice($categoryProducts, $middlePosition - 15, 30, true);
					$middlePosition = 15;
				}
			}
			
			// Display tpl
			$this->smarty->assign(array(
				'categoryProducts' => $categoryProducts,
				'middlePosition' => (int)$middlePosition,
				'ProdDisplayPrice' => Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE')));
		}
		return $this->display(__FILE__, 'productscategory.tpl', $this->getCacheId($cache_id));
	}
	
	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'productscategory.css', 'all');
		$this->context->controller->addJS($this->_path.'productscategory.js');
		$this->context->controller->addJqueryPlugin('serialScroll');
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('productscategory.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('productscategory.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('productscategory.tpl');
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
						'label' => $this->l('Display price on products'),
						'desc' => $this->l('Show the price on the products in the block.'),
						'name' => 'PRODUCTSCATEGORY_DISPLAY_PRICE',
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
		$helper->submit_action = 'submitCross';
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
			'PRODUCTSCATEGORY_DISPLAY_PRICE' => Tools::getValue('PRODUCTSCATEGORY_DISPLAY_PRICE', Configuration::get('PRODUCTSCATEGORY_DISPLAY_PRICE')),
		);
	}

}
