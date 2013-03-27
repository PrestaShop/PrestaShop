<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CartControllerCore extends FrontController
{
	public $php_self = 'cart';

	protected $id_product;
	protected $id_product_attribute;
	protected $id_address_delivery;
	protected $customization_id;
	protected $qty;

	protected $ajax_refresh = false;

	/**
	 * This is not a public page, so the canonical redirection is disabled
	 */
	public function canonicalRedirection($canonicalURL = '')
	{
	}

	/**
	 * Initialize cart controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		// Get page main parameters
		$this->id_product = (int)Tools::getValue('id_product', null);
		$this->id_product_attribute = (int)Tools::getValue('id_product_attribute', Tools::getValue('ipa'));
		$this->customization_id = (int)Tools::getValue('id_customization');
		$this->qty = abs(Tools::getValue('qty', 1));
		$this->id_address_delivery = (int)Tools::getValue('id_address_delivery');
	}

	public function postProcess()
	{
		// Update the cart ONLY if $this->cookies are available, in order to avoid ghost carts created by bots
		if ($this->context->cookie->exists() && !$this->errors && !($this->context->customer->isLogged() && !$this->isTokenValid()))
		{
			if (Tools::getIsset('add') || Tools::getIsset('update'))
				$this->processChangeProductInCart();
			else if (Tools::getIsset('delete'))
				$this->processDeleteProductInCart();
			else if (Tools::getIsset('changeAddressDelivery'))
				$this->processChangeProductAddressDelivery();
			else if (Tools::getIsset('allowSeperatedPackage'))
				$this->processAllowSeperatedPackage();
			else if (Tools::getIsset('duplicate'))
				$this->processDuplicateProduct();
			// Make redirection
			if (!$this->errors && !$this->ajax)
			{
				$queryString = Tools::safeOutput(Tools::getValue('query', null));
				if ($queryString && !Configuration::get('PS_CART_REDIRECT'))
					Tools::redirect('index.php?controller=search&search='.$queryString);

				// Redirect to previous page
				if (isset($_SERVER['HTTP_REFERER']))
				{
					preg_match('!http(s?)://(.*)/(.*)!', $_SERVER['HTTP_REFERER'], $regs);
					if (isset($regs[3]) && !Configuration::get('PS_CART_REDIRECT'))
						Tools::redirect($_SERVER['HTTP_REFERER']);
				}

				Tools::redirect('index.php?controller=order&'.(isset($this->id_product) ? 'ipa='.$this->id_product : ''));
			}

		}
		elseif (!$this->isTokenValid())
			Tools::redirect('index.php');
	}

	/**
	 * This process delete a product from the cart
	 */
	protected function processDeleteProductInCart()
	{
		if ($this->context->cart->deleteProduct($this->id_product, $this->id_product_attribute, $this->customization_id, $this->id_address_delivery))
		{
			if (!Cart::getNbProducts((int)($this->context->cart->id)))
			{
				$this->context->cart->setDeliveryOption(null);
				$this->context->cart->gift = 0;
				$this->context->cart->gift_message = '';
				$this->context->cart->update();
			}
		}
		$removed = CartRule::autoAddToCart();
		if (count($removed) && (int)Tools::getValue('allow_refresh'))
			$this->ajax_refresh = true;
	}

	protected function processChangeProductAddressDelivery()
	{
		if (!Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return;

		$old_id_address_delivery = (int)Tools::getValue('old_id_address_delivery');
		$new_id_address_delivery = (int)Tools::getValue('new_id_address_delivery');

		if (!count(Carrier::getAvailableCarrierList(new Product($this->id_product), null, $new_id_address_delivery)))
			die(Tools::jsonEncode(array(
				'hasErrors' => true,
				'error' => Tools::displayError('It is not possible to deliver this product to the selected address.', false),
			)));
		
		$this->context->cart->setProductAddressDelivery(
			$this->id_product,
			$this->id_product_attribute,
			$old_id_address_delivery,
			$new_id_address_delivery);
	}

	protected function processAllowSeperatedPackage()
	{
		if (!Configuration::get('PS_SHIP_WHEN_AVAILABLE'))
			return;

		if (Tools::getValue('value') === false)
			die('{"error":true, "error_message": "No value setted"}');

		$this->context->cart->allow_seperated_package = (boolean)Tools::getValue('value');
		$this->context->cart->update();
		die('{"error":false}');
	}

	protected function processDuplicateProduct()
	{
		if (!Configuration::get('PS_ALLOW_MULTISHIPPING'))
			return;

		if (!$this->context->cart->duplicateProduct(
				$this->id_product,
				$this->id_product_attribute,
				$this->id_address_delivery,
				(int)Tools::getValue('new_id_address_delivery')
			))
		{
			//$error_message = $this->l('Error durring product duplication');
			// For the moment no translations
			$error_message = 'Error durring product duplication';
		}
	}

	/**
	 * This process add or update a product in the cart
	 */
	protected function processChangeProductInCart()
	{
		$mode = (Tools::getIsset('update') && $this->id_product) ? 'update' : 'add';

		if ($this->qty == 0)
			$this->errors[] = Tools::displayError('Null quantity.');
		else if (!$this->id_product)
			$this->errors[] = Tools::displayError('Product not found');

		$product = new Product($this->id_product, true, $this->context->language->id);
		if (!$product->id || !$product->active)
		{
			$this->errors[] = Tools::displayError('This product is no longer available.', false);
			return;
		}

		// Check product quantity availability
		if ($this->id_product_attribute)
		{
			if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $this->qty))
				$this->errors[] = Tools::displayError('There isn\'t enough product in stock.');
		}
		else if ($product->hasAttributes())
		{
			$minimumQuantity = ($product->out_of_stock == 2) ? !Configuration::get('PS_ORDER_OUT_OF_STOCK') : !$product->out_of_stock;
			$this->id_product_attribute = Product::getDefaultAttribute($product->id, $minimumQuantity);
			// @todo do something better than a redirect admin !!
			if (!$this->id_product_attribute)
				Tools::redirectAdmin($this->context->link->getProductLink($product));
			else if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty($this->id_product_attribute, $this->qty))
				$this->errors[] = Tools::displayError('There isn\'t enough product in stock.');
		}
		else if (!$product->checkQty($this->qty))
			$this->errors[] = Tools::displayError('There isn\'t enough product in stock.');

		// If no errors, process product addition
		if (!$this->errors && $mode == 'add')
		{
			// Add cart if no cart found
			if (!$this->context->cart->id)
			{
				if (Context::getContext()->cookie->id_guest)
				{
					$guest = new Guest(Context::getContext()->cookie->id_guest);
					$this->context->cart->mobile_theme = $guest->mobile_theme;
				}
				$this->context->cart->add();
				if ($this->context->cart->id)
					$this->context->cookie->id_cart = (int)$this->context->cart->id;
			}

			// Check customizable fields
			if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id)
				$this->errors[] = Tools::displayError('Please fill in all of the required fields, and then save your customizations.');

			if (!$this->errors)
			{
				$cart_rules = $this->context->cart->getCartRules();
				$update_quantity = $this->context->cart->updateQty($this->qty, $this->id_product, $this->id_product_attribute, $this->customization_id, Tools::getValue('op', 'up'), $this->id_address_delivery);
				if ($update_quantity < 0)
				{
					// If product has attribute, minimal quantity is set with minimal quantity of attribute
					$minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
					$this->errors[] = sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity);
				}
				elseif (!$update_quantity)
					$this->errors[] = Tools::displayError('You already have the maximum quantity available for this product.', false);
				elseif ((int)Tools::getValue('allow_refresh'))
				{
					// If the cart rules has changed, we need to refresh the whole cart
					$cart_rules2 = $this->context->cart->getCartRules();
					if (count($cart_rules2) != count($cart_rules))
						$this->ajax_refresh = true;
					else
					{
						$rule_list = array();
						foreach ($cart_rules2 as $rule)
							$rule_list[] = $rule['id_cart_rule'];
						foreach ($cart_rules as $rule)
							if (!in_array($rule['id_cart_rule'], $rule_list))
							{
								$this->ajax_refresh = true;
								break;
							}
					}
				}
			}
		}

		$removed = CartRule::autoRemoveFromCart();
		CartRule::autoAddToCart();
		if (count($removed) && (int)Tools::getValue('allow_refresh'))
			$this->ajax_refresh = true;
	}

	/**
	 * Remove discounts on cart
	 */
	protected function processRemoveDiscounts()
	{
		Tools::displayAsDeprecated();
		$this->errors = array_merge($this->errors, CartRule::autoRemoveFromCart());
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->setTemplate(_PS_THEME_DIR_.'errors.tpl');
		if (!$this->ajax)
			parent::initContent();
	}

	/**
	 * Display ajax content (this function is called instead of classic display, in ajax mode)
	 */
	public function displayAjax()
	{
		if ($this->errors)
			die(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));
		if ($this->ajax_refresh)
			die(Tools::jsonEncode(array('refresh' => true)));

		if (Tools::getIsset('summary'))
		{
			$result = array();
			if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
			{
				$groups = (Validate::isLoadedObject($this->context->customer)) ? $this->context->customer->getGroups() : array(1);
				if ($this->context->cart->id_address_delivery)
					$deliveryAddress = new Address($this->context->cart->id_address_delivery);
				$id_country = (isset($deliveryAddress) && $deliveryAddress->id) ? $deliveryAddress->id_country : Configuration::get('PS_COUNTRY_DEFAULT');

				Cart::addExtraCarriers($result);
			}
			$result['summary'] = $this->context->cart->getSummaryDetails(null, true);
			$result['customizedDatas'] = Product::getAllCustomizedDatas($this->context->cart->id, null, true);
			$result['HOOK_SHOPPING_CART'] = Hook::exec('displayShoppingCartFooter', $result['summary']);
			$result['HOOK_SHOPPING_CART_EXTRA'] = Hook::exec('displayShoppingCart', $result['summary']);

			foreach ($result['summary']['products'] as $key => &$product)
			{
				$product['quantity_without_customization'] = $product['quantity'];
				if ($result['customizedDatas'] && isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']]))
				{
					foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses)
						foreach ($addresses as $customization)
							$product['quantity_without_customization'] -= (int)$customization['quantity'];
				}
				$product['price_without_quantity_discount'] = Product::getPriceStatic(
					$product['id_product'],
					!Product::getTaxCalculationMethod(),
					$product['id_product_attribute'],
					6,
					null,
					false,
					false
				);
			}
			if ($result['customizedDatas'])
				Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);

			die(Tools::jsonEncode($result));
		}
		// @todo create a hook
		elseif (file_exists(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php'))
			require_once(_PS_MODULE_DIR_.'/blockcart/blockcart-ajax.php');
	}
}
