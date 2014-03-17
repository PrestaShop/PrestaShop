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

if (!defined('_PS_VERSION_'))
	exit;

class BlockCart extends Module
{
	public function __construct()
	{
		$this->name = 'blockcart';
		$this->tab = 'front_office_features';
		$this->version = '1.3';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Cart block');
		$this->description = $this->l('Adds a block containing the customer\'s shopping cart.');
	}

	public function assignContentVars($params)
	{
		global $errors;

		// Set currency
		if ((int)$params['cart']->id_currency && (int)$params['cart']->id_currency != $this->context->currency->id)
			$currency = new Currency((int)$params['cart']->id_currency);
		else
			$currency = $this->context->currency;

		$taxCalculationMethod = Group::getPriceDisplayMethod((int)Group::getCurrent()->id);

		$useTax = !($taxCalculationMethod == PS_TAX_EXC);

		$products = $params['cart']->getProducts(true);
		$nbTotalProducts = 0;
		foreach ($products as $product)
			$nbTotalProducts += (int)$product['cart_quantity'];
		$cart_rules = $params['cart']->getCartRules();

		$base_shipping = $params['cart']->getOrderTotal($useTax, Cart::ONLY_SHIPPING);
		$shipping_cost = Tools::displayPrice($base_shipping, $currency);
		$shipping_cost_float = Tools::convertPrice($base_shipping, $currency);
		$wrappingCost = (float)($params['cart']->getOrderTotal($useTax, Cart::ONLY_WRAPPING));
		$totalToPay = $params['cart']->getOrderTotal($useTax);

		if ($useTax && Configuration::get('PS_TAX_DISPLAY') == 1)
		{
			$totalToPayWithoutTaxes = $params['cart']->getOrderTotal(false);
			$this->smarty->assign('tax_cost', Tools::displayPrice($totalToPay - $totalToPayWithoutTaxes, $currency));
		}
		
		// The cart content is altered for display
		foreach ($cart_rules as &$cart_rule)
		{
			if ($cart_rule['free_shipping'])
			{
				$shipping_cost = Tools::displayPrice(0, $currency);
				$shipping_cost_float = 0;
				$cart_rule['value_real'] -= Tools::convertPrice($params['cart']->getOrderTotal(true, Cart::ONLY_SHIPPING), $currency);
				$cart_rule['value_tax_exc'] = Tools::convertPrice($params['cart']->getOrderTotal(false, Cart::ONLY_SHIPPING), $currency);
			}
			if ($cart_rule['gift_product'])
			{
				foreach ($products as &$product)
					if ($product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute'])
					{
						$product['is_gift'] = 1;
						$product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$product['total'] = Tools::ps_round($product['total'] - $product['price'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
					}
			}
		}

		$total_free_shipping = 0;
		if ($free_shipping = Tools::convertPrice(floatval(Configuration::get('PS_SHIPPING_FREE_PRICE')), $currency))
		{
			$total_free_shipping =  floatval($free_shipping - ($params['cart']->getOrderTotal(true, Cart::ONLY_PRODUCTS) + $params['cart']->getOrderTotal(true, Cart::ONLY_DISCOUNTS)));
			$discounts = $params['cart']->getCartRules(CartRule::FILTER_ACTION_SHIPPING);
			if ($total_free_shipping < 0)
				$total_free_shipping = 0;
			if (is_array($discounts) && count($discounts))
				$total_free_shipping = 0;
		}

		$this->smarty->assign(array(
			'products' => $products,
			'customizedDatas' => Product::getAllCustomizedDatas((int)($params['cart']->id)),
			'CUSTOMIZE_FILE' => _CUSTOMIZE_FILE_,
			'CUSTOMIZE_TEXTFIELD' => _CUSTOMIZE_TEXTFIELD_,
			'discounts' => $cart_rules,
			'nb_total_products' => (int)($nbTotalProducts),
			'shipping_cost' => $shipping_cost,
			'shipping_cost_float' => $shipping_cost_float,
			'show_wrapping' => $wrappingCost > 0 ? true : false,
			'show_tax' => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
			'wrapping_cost' => Tools::displayPrice($wrappingCost, $currency),
			'product_total' => Tools::displayPrice($params['cart']->getOrderTotal($useTax, Cart::BOTH_WITHOUT_SHIPPING), $currency),
			'total' => Tools::displayPrice($totalToPay, $currency),
			'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
			'ajax_allowed' => (int)(Configuration::get('PS_BLOCK_CART_AJAX')) == 1 ? true : false,
			'static_token' => Tools::getToken(false),
			'free_shipping' => $total_free_shipping
		));
		if (count($errors))
			$this->smarty->assign('errors', $errors);
		if (isset($this->context->cookie->ajax_blockcart_display))
			$this->smarty->assign('colapseExpandStatus', $this->context->cookie->ajax_blockcart_display);
	}

	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBlockCart'))
		{
			$ajax = Tools::getValue('PS_BLOCK_CART_AJAX');
			if ($ajax != 0 && $ajax != 1)
				$output .= $this->displayError($this->l('Ajax: Invalid choice.'));
			else
				Configuration::updateValue('PS_BLOCK_CART_AJAX', (int)($ajax));

			if (!($productNbr = Tools::getValue('PS_BLOCK_CART_XSELL_LIMIT')) || empty($productNbr))
				$output .= $this->displayError($this->l('Please complete the "Products to display" field.'));
			elseif ((int)($productNbr) == 0)
				$output .= $this->displayError($this->l('Invalid number.'));
			else
			{
				Configuration::updateValue('PS_BLOCK_CART_XSELL_LIMIT', (int)(Tools::getValue('PS_BLOCK_CART_XSELL_LIMIT')));
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
	}

	public function install()
	{
		if (
			parent::install() == false
			|| $this->registerHook('top') == false
			|| $this->registerHook('header') == false
			|| $this->registerHook('actionCartListOverride') == false
			|| Configuration::updateValue('PS_BLOCK_CART_AJAX', 1) == false
			|| Configuration::updateValue('PS_BLOCK_CART_XSELL_LIMIT', 12) == false)
			return false;
		return true;
	}

	public function hookRightColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		// @todo this variable seems not used
		$this->smarty->assign(array(
			'order_page' => (strpos($_SERVER['PHP_SELF'], 'order') !== false),
			'blockcart_top' => (isset($params['blockcart_top']) && $params['blockcart_top']) ? true : false,
		));
		$this->assignContentVars($params);
		return $this->display(__FILE__, 'blockcart.tpl');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookAjaxCall($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		$this->assignContentVars($params);
		$res = Tools::jsonDecode($this->display(__FILE__, 'blockcart-json.tpl'), true);
		if (is_array($res) && $id_product = Tools::getValue('id_product'))
		{
			$this->smarty->assign('orderProducts', OrderDetail::getCrossSells($id_product, $this->context->language->id, Configuration::get('PS_BLOCK_CART_XSELL_LIMIT')));
			$res['crossSelling'] = $this->display(__FILE__, 'crossselling.tpl');
		}
		$res = Tools::jsonEncode($res);
		return $res;
	}

	public function hookActionCartListOverride($params)
	{
		if (!Configuration::get('PS_BLOCK_CART_AJAX'))
			return;

		$this->assignContentVars(array('cookie' => $this->context->cookie, 'cart' => $this->context->cart));
		$params['json'] = $this->display(__FILE__, 'blockcart-json.tpl');
	}

	public function hookHeader()
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		$this->context->controller->addCSS(($this->_path).'blockcart.css', 'all');
		if ((int)(Configuration::get('PS_BLOCK_CART_AJAX')))
		{
			$this->context->controller->addJS(($this->_path).'ajax-cart.js');
			$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll', 'bxslider'));
		}
	}
	
	public function hookTop($params)
	{
		$params['blockcart_top'] = true;
		return $this->hookRightColumn($params);
	}
	
	public function hookDisplayNav($params)
	{
		$params['blockcart_top'] = true;
		return $this->hookTop($params);
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
						'label' => $this->l('Ajax cart'),
						'name' => 'PS_BLOCK_CART_AJAX',
						'is_bool' => true,
						'desc' => $this->l('Activate Ajax mode for the cart (compatible with the default theme).'),
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
						'label' => $this->l('Products to display in cross-selling'),
						'name' => 'PS_BLOCK_CART_XSELL_LIMIT',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products to be displayed in the cross-selling block.')
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
		$helper->submit_action = 'submitBlockCart';
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
			'PS_BLOCK_CART_AJAX' => Tools::getValue('PS_BLOCK_CART_AJAX', Configuration::get('PS_BLOCK_CART_AJAX')),
			'PS_BLOCK_CART_XSELL_LIMIT' => Tools::getValue('PS_BLOCK_CART_XSELL_LIMIT', Configuration::get('PS_BLOCK_CART_XSELL_LIMIT'))
		);
	}
}
