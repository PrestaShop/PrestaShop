<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy 502immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CartRuleCore extends ObjectModel
{
	public $id;
	public $name;
	public $id_customer;
	public $date_from;
	public $date_to;
	public $description;
	public $quantity = 1;
	public $quantity_per_user = 1;
	public $priority = 1;
	public $code;
	public $minimum_amount;
	public $minimum_amount_tax;
	public $minimum_amount_currency;
	public $minimum_amount_shipping;
	public $country_restriction;
	public $carrier_restriction;
	public $group_restriction;
	public $cart_rule_restriction;
	public $product_restriction;
	public $free_shipping;
	public $reduction_percent;
	public $reduction_amount;
	public $reduction_tax;
	public $reduction_currency;
	public $reduction_product;
	public $gift_product;
	public $active = 1;
	public $date_add;
	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'cart_rule',
		'primary' => 'id_cart_rule',
		'multilang' => true,
		'fields' => array(
			'id_customer' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'date_from' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
			'date_to' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
			'description' => 			array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65534),
			'quantity' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'quantity_per_user' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'priority' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'code' => 					array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),
			'minimum_amount' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'minimum_amount_tax' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'minimum_amount_currency' =>array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'minimum_amount_shipping' =>array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'country_restriction' =>	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'carrier_restriction' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'group_restriction' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'cart_rule_restriction' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'product_restriction' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'free_shipping' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'reduction_percent' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'reduction_amount' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'reduction_tax' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'reduction_currency' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'reduction_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'gift_product' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'active' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

			// Lang fields
			'name' => 					array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
		),
	);

	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues))
			return false;

		Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');
		return true;
	}

	public function delete()
	{
		if (!parent::delete())
			return false;

		Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', CartRule::isCurrentlyUsed($this->def['table'], true));
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart_rule` = '.(int)$this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_combination` WHERE `id_cart_rule_1` = '.(int)$this->id.' OR `id_cart_rule_2` = '.(int)$this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');
	}

	public static function copyConditions($id_cart_rule_source, $id_cart_rule_destination)
	{
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_carrier` (`id_cart_rule`, `id_carrier`)
		(SELECT '.(int)$id_cart_rule_destination.', id_carrier FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
		(SELECT '.(int)$id_cart_rule_destination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
		(SELECT '.(int)$id_cart_rule_destination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
		(SELECT '.(int)$id_cart_rule_destination.', IF(id_cart_rule_1 != '.(int)$id_cart_rule_source.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
		WHERE `id_cart_rule_1` = '.(int)$id_cart_rule_source.' OR `id_cart_rule_2` = '.(int)$id_cart_rule_source.')');

		// Todo : should be changed soon, be must be copied too
		// Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
		// Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');
	}

	public static function getIdByCode($code)
	{
		if (!Validate::isDiscountName($code))
	 		return false;
	 	return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` WHERE `code` = \''.pSQL($code).'\'');
	}

	public static function getCustomerCartRules($id_lang, $id_customer, $active = false, $includeGeneric = true, $inStock = false, Cart $cart = null)
	{
		if (!CartRule::isFeatureActive())
			return array();

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'cart_rule` cr
		LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cr.`id_cart_rule` = crl.`id_cart_rule` AND crl.`id_lang` = '.(int)$id_lang.')
		WHERE (
			cr.`id_customer` = '.(int)$id_customer.'
			'.($includeGeneric ? 'OR cr.`id_customer` = 0' : '').'
		)
		'.($active ? 'AND cr.`active` = 1' : '').'
		'.($inStock ? 'AND cr.`quantity` > 0' : ''));

		// Remove cart rule that does not match the customer groups
		if ($includeGeneric)
		{
			$customerGroups = Customer::getGroupsStatic($id_customer);
			foreach ($result as $key => $cart_rule)
				if ($cart_rule['group_restriction'])
				{
					$cartRuleGroups = Db::getInstance()->getValue('SELECT id_group FROM '._DB_PREFIX_.'cart_rule_group WHERE id_cart_rule = '.(int)$cart_rule['id_cart_rule']);
					foreach ($cartRuleGroups as $cartRuleGroup)
						if (in_array($cartRuleGroups['id_group'], $customerGroups))
							continue 2;
					unset($result[$key]);
				}
		}
		return $result;
	}

	public function usedByCustomer($id_customer)
	{
		return (bool)Db::getInstance()->getValue('
		SELECT id_cart_rule
		FROM `'._DB_PREFIX_.'order_cart_rule` ocr
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
		WHERE ocr.`id_cart_rule` = '.(int)$this->id.'
		AND o.`id_customer` = '.(int)$id_customer);
	}

	public static function cartRuleExists($name)
	{
		if (!CartRule::isFeatureActive())
			return false;

		return (bool)Db::getInstance()->getValue('
		SELECT `id_cart_rule`
		FROM `'._DB_PREFIX_.'cart_rule`
		WHERE `code` = \''.pSQL($name).'\'');
	}

	public static function deleteByIdCustomer($id_customer)
	{
		$return = true;
		$cart_rules = new Collection('CartRule');
		$cart_rules->where('id_customer', '=', $id_customer);
		foreach ($cart_rules as $cart_rule)
			$return &= $cart_rule->delete();
		return $return;
	}

	public function getProductRules()
	{
		if (!Validate::isLoadedObject($this) OR $this->product_restriction == 0)
			return array();

		$productRules = array();
		$results = Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'cart_rule_product_rule pr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value prv ON pr.id_product_rule = prv.id_product_rule
		WHERE pr.id_cart_rule = '.(int)$this->id, false);
		foreach ($results as $row)
		{
			if (!isset($productRules[$row['id_product_rule']]))
				$productRules[$row['id_product_rule']] = array('quantity' => $row['quantity'], 'type' => $row['type'], 'values' => array());
			$productRules[$row['id_product_rule']]['values'][] = $row['id_item'];
		}
		return $productRules;
	}

	// Todo : Add shop management
	public function checkValidity(Context $context, $alreadyInCart = false)
	{
		if (!CartRule::isFeatureActive())
			return false;

		if (!$this->active)
			return Tools::displayError('This voucher is disabled');
		if (!$this->quantity)
			return Tools::displayError('This voucher has already been used ');
		if (strtotime($this->date_from) > time())
			return Tools::displayError('This voucher is not valid yet');
		if (strtotime($this->date_to) < time())
			return Tools::displayError('This voucher has expired');

		if ($context->cart->id_customer)
		{
			$quantityUsed = Db::getInstance()->getValue('
			SELECT count(*)
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_cart_rule od ON o.id_order = od.id_order
			WHERE o.id_customer = '.$context->cart->id_customer.'
			AND od.id_cart_rule = '.(int)$this->id.'
			AND '.(int)Configuration::get('PS_OS_ERROR').' != (
				SELECT oh.id_order_state
				FROM '._DB_PREFIX_.'order_history oh
				WHERE oh.id_order = o.id_order
				ORDER BY oh.date_add DESC
				LIMIT 1
			)');
			if ($quantityUsed + 1 > $this->quantity_per_user)
				return Tools::displayError('You cannot use this voucher anymore (usage limit reached)');
		}

		$otherCartRules = $context->cart->getCartRules();
		if (count($otherCartRules))
			foreach ($otherCartRules as $otherCartRule)
			{
				if ($otherCartRule['id_cart_rule'] == $this->id && !$alreadyInCart)
					return Tools::displayError('This voucher is already in your cart');
				if ($this->carrier_restriction AND $otherCartRule['cart_rule_restriction'])
				{
					$combinable = Db::getInstance()->getValue('
					SELECT id_cart_rule_1
					FROM '._DB_PREFIX_.'cart_rule_combination
					WHERE (id_cart_rule_1 = '.(int)$this->id.' AND id_cart_rule_2 = '.(int)$otherCartRule['id_cart_rule'].')
					OR (id_cart_rule_2 = '.(int)$this->id.' AND id_cart_rule_1 = '.(int)$otherCartRule['id_cart_rule'].')');
					if (!$combinable)
					{
						$cartRule = new CartRule($otherCartRule['cart_rule_restriction'], $context->cart->id_lang);
						return Tools::displayError('This voucher is not combinable with an other voucher already in your cart:').' '.$this->name;
					}
				}
			}

		// Get an intersection of the customer groups and the cart rule groups (if the customer is not logged in, the default group is 1)
		if ($this->group_restriction)
		{
			$id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crg.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_group crg
			WHERE crg.id_cart_rule = '.(int)$this->id.'
			AND crg.id_group '.($context->cart->id_customer ? 'IN (SELECT cg.id_group FROM '._DB_PREFIX_.'customer_group cg WHERE cg.id_customer = '.(int)$context->cart->id_customer.')' : '= 1'));
			if (!$id_cart_rule)
				return Tools::displayError('You cannot use this voucher');
		}

		// Check if the customer delivery address is usable with the cart rule
		if ($this->country_restriction AND $context->cart->id_address_delivery)
		{
			$id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_country crc
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND crc.id_country = (SELECT a.id_country FROM '._DB_PREFIX_.'address a WHERE a.id_address = '.(int)$context->cart->id_address_delivery.' LIMIT 1)');
			if (!$id_cart_rule)
				return Tools::displayError('You cannot use this voucher in your country of delivery');
		}

		// Check if the carrier chosen by the customer is usable with the cart rule
		if ($this->carrier_restriction AND $context->cart->id_carrier)
		{
			$id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_carrier crc
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND crc.id_carrier = '.(int)$context->cart->id_carrier);
			if (!$id_cart_rule)
				return Tools::displayError('You cannot use this voucher with this carrier');
		}

		// Check if the products chosen by the customer are usable with the cart rule
		if ($this->product_restriction)
		{
			$productRules = $this->getProductRules();
			foreach ($productRules as $productRule)
			{
				switch ($productRule['type'])
				{
					case 'attributes':
						$cartAttributes = Db::getInstance()->ExecuteS('
						SELECT cp.quantity, pac.`id_attribute`
						FROM `'._DB_PREFIX_.'cart_product` cp
						LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON cp.id_product_attribute = pac.id_product_attribute
						WHERE cp.`id_cart` = '.(int)$context->cart->id.'
						AND cp.id_product_attribute > 0');
						$matchingProducts = 0;
						foreach ($cartAttributes as $cartAttribute)
							if (in_array($cartAttribute['id_attribute'], $productRule['values']))
								$matchingProducts += $cartAttribute['quantity'];
						if ($matchingProducts < $productRule['quantity'])
							return Tools::displayError('You cannot use this voucher with these products');
						break;
					case 'products':
						$cartProducts = Db::getInstance()->ExecuteS('
						SELECT cp.quantity, cp.`id_product`
						FROM `'._DB_PREFIX_.'cart_product` cp
						WHERE cp.`id_cart` = '.(int)$context->cart->id);
						$matchingProducts = 0;
						foreach ($cartProducts as $cartProduct)
							if (in_array($cartProduct['id_product'], $productRule['values']))
								$matchingProducts += $cartProduct['quantity'];
						if ($matchingProducts < $productRule['quantity'])
							return Tools::displayError('You cannot use this voucher with these products');
						break;
					case 'categories':
						$cartCategories = Db::getInstance()->ExecuteS('
						SELECT cp.quantity, catp.`id_category`
						FROM `'._DB_PREFIX_.'cart_product` cp
						LEFT JOIN `'._DB_PREFIX_.'category_product` catp ON cp.id_product = catp.id_product
						WHERE cp.`id_cart` = '.(int)$context->cart->id);
						$matchingProducts = 0;
						foreach ($cartCategories as $cartCategory)
							if (in_array($cartCategory['id_category'], $productRule['values']))
								$matchingProducts += $cartCategory['quantity'];
						if ($matchingProducts < $productRule['quantity'])
							return Tools::displayError('You cannot use this voucher with these products');
						break;
				}
			}
		}

		// Check if the cart rule is only usable by a specific customer, and if the current customer is the right one
		if ($this->id_customer && $context->cart->id_customer != $this->id_customer)
		{
			if (!Context::getContext()->customer->isLogged())
				return Tools::displayError('You cannot use this voucher').' - '.Tools::displayError('Please log in');
			return Tools::displayError('You cannot use this voucher');
		}

		if ($this->minimum_amount)
		{
			$minimum_amount = $this->minimum_amount;
			if ($this->minimum_amount_currency != Configuration::get('PS_CURRENCY_DEFAULT'))
			{
				$minimumAmountCurrency = new Currency($this->minimum_amount_currency);
				//p($this->minimum_amount_currency);

				if ($this->minimum_amount == 0 || $minimumAmountCurrency->conversion_rate == 0)
					$minimum_amount = 0;
				else
					$minimum_amount = $this->minimum_amount / $minimumAmountCurrency->convertion_rate;
			}
			$cartTotal = $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_PRODUCTS);
			if ($this->minimum_amount_shipping)
				$cartTotal += $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_SHIPPING);
			if ($cartTotal < $minimum_amount)
				return Tools::displayError('You do not reach the minimum amount required to use this voucher');
		}
	}

	// The reduction value is POSITIVE
	public function getContextualValue($useTax, Context $context = NULL)
	{
		if (!CartRule::isFeatureActive())
			return 0;
		if (!$context)
			$context = Context::getContext();

		$reductionValue = 0;

		// Free shipping on selected carriers
		if ($this->free_shipping)
		{
			if (!$this->carrier_restriction)
				$reductionValue += $context->cart->getTotalShippingCost(null, $useTax = true, $context->country);
			else
			{
				foreach ((int)Db::getInstance()->executeS('
					SELECT crc.id_cart_rule, crc.id_carrier
					FROM '._DB_PREFIX_.'cart_rule_carrier crc
					WHERE crc.id_cart_rule = '.(int)$this->id.'
					AND crc.id_carrier = '.(int)$context->cart->id_carrier)
					as $cart_rule
				)
					$reductionValue += $context->cart->getCarrierCost($cart_rule['id_carrier'], $useTax, $context->country);
			}
		}

		// Discount (%) on the whole order
		if ($this->reduction_percent AND $this->reduction_product == 0)
		{
			$reductionValue += $context->cart->getOrderTotal($useTax, Cart::ONLY_PRODUCTS) * $this->reduction_percent / 100;
		}

		// Discount (%) on a specific product
		if ($this->reduction_percent AND $this->reduction_product > 0)
		{
			foreach ($context->cart->getProducts() as $product)
				if ($product['id_product'] == $this->reduction_product)
					$reductionValue += ($useTax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
		}

		// Discount (%) on the cheapest product
		if ($this->reduction_percent AND $this->reduction_product == -1)
		{
			$minPrice = false;
			foreach ($context->cart->getProducts() as $product)
			{
				$price = ($useTax ? $product['price_wt'] : $product['price']);
				if ($price > 0 && ($minPrice === false || $minPrice > $price))
					$minPrice = $price;
				$reductionValue += $minPrice * $this->reduction_percent / 100;
			}
		}

		// Discount (�)
		if ($this->reduction_amount)
		{
			$reduction_amount = $this->reduction_amount;
			// If we need to convert the voucher value to the cart currency
			if ($this->reduction_currency != $context->currency->id)
			{
				$voucherCurrency = new Currency($this->reduction_currency);

				// First we convert the voucher value to the default currency
				if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0)
					$reduction_amount = 0;
				else
					$reduction_amount /= $voucherCurrency->conversion_rate;

				// Then we convert the voucher value in the default currency into the cart currency
				$reduction_amount *= $context->currency->conversion_rate;
				$reduction_amount = Tools::ps_round($reduction_amount);
			}

			// If it has the same tax application that you need, then it's the right value, whatever the product!
			if ($this->reduction_tax == $useTax)
				$reductionValue += $reduction_amount;
			else
			{
				if ($this->reduction_product > 0)
				{
					// Todo: optimize with an array_search (and do the same in the other foreach of this function)
					foreach ($context->cart->getProducts() as $product)
						if ($product['id_product'] == $this->reduction_product)
						{
							$product_price_ti = $product['price_wt'];
							$product_price_te = $product['price'];
							$product_vat_amount = $product_price_ti - $product_price_te;

							if ($product_vat_amount == 0 || $product_price_te == 0)
								$product_vat_rate = 0;
							else
								$product_vat_rate = $product_vat_amount / $product_price_te;

							if ($this->reduction_tax && !$useTax)
								$reductionValue += $reduction_amount / (1 + $product_vat_rate);
							elseif (!$this->reduction_tax && $useTax)
								$reductionValue += $reduction_amount * (1 + $product_vat_rate);
						}
				}
				// Discount (�) on the whole order
				elseif ($this->reduction_product == 0)
				{
					$cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
					$cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
					$cart_vat_amount = $cart_amount_ti - $cart_amount_te;

					if ($cart_vat_amount == 0 || $cart_amount_te == 0)
						$cart_average_vat_rate = 0;
					else
						$cart_average_vat_rate = $cart_vat_amount / $cart_amount_te;

					if ($this->reduction_tax && !$useTax)
						$reductionValue += $reduction_amount / (1 + $cart_average_vat_rate);
					elseif (!$this->reduction_tax && $useTax)
						$reductionValue += $reduction_amount * (1 + $cart_average_vat_rate);
				}
				// Todo: discount on the cheapest (but this is not meaningful)
			}
		}

		// Free gift
		if ($this->gift_product)
		{
			foreach ($context->cart->getProducts() as $product)
				if ($product['id_product'] == $this->gift_product)
					$reductionValue += ($useTax ? $product['price_wt'] : $product['price']);
		}

		return $reductionValue;
    }

	protected function getCartRuleCombinations()
	{
		$array = array();
		$array['selected'] = Db::getInstance()->ExecuteS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		WHERE cr.id_cart_rule != '.(int)$this->id.'
		AND (
			cr.cart_rule_restriction = 0
			OR cr.id_cart_rule IN (
				SELECT IF(id_cart_rule_1 = '.(int)$this->id.', id_cart_rule_2, id_cart_rule_1)
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE '.(int)$this->id.' = id_cart_rule_1
				OR '.(int)$this->id.' = id_cart_rule_2
			)
		)');
		$array['unselected'] = Db::getInstance()->ExecuteS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		WHERE cr.cart_rule_restriction = 1
		AND cr.id_cart_rule != '.(int)$this->id.'
		AND cr.id_cart_rule NOT IN (
			SELECT IF(id_cart_rule_1 = '.(int)$this->id.', id_cart_rule_2, id_cart_rule_1)
			FROM '._DB_PREFIX_.'cart_rule_combination
			WHERE '.(int)$this->id.' = id_cart_rule_1
			OR '.(int)$this->id.' = id_cart_rule_2
		)');
		return $array;
	}

	public function getAssociatedRestrictions($type, $active = 1)
	{
		$array = array('selected' => array(), 'unselected' => array());

		if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule')))
			return false;

		if (!Validate::isLoadedObject($this) OR $this->{$type.'_restriction'} == 0)
		{
			$array['selected'] = Db::getInstance()->ExecuteS('
			SELECT t.*, tl.*, 1 as selected
			FROM `'._DB_PREFIX_.$type.'` t
			LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.'
			WHERE 1
			'.($active ? 'AND t.active = 1' : '').'
			'.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id : '').'
			ORDER BY name ASC');
		}
		else
		{
			if ($type == 'cart_rule')
				$array = $this->getCartRuleCombinations();
			else
			{
				$result = Db::getInstance()->ExecuteS('
				SELECT t.*, tl.*, IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
				FROM `'._DB_PREFIX_.$type.'` t
				LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.'
				LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int)$this->id.') crt ON t.id_'.$type.' = crt.id_'.$type.'
				'.($active ? 'WHERE t.active = 1' : '').'
				ORDER BY name ASC',
				false);
				while ($row = Db::getInstance()->nextRow())
					$array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
			}
		}
		return $array;
	}

	public static function autoRemoveFromCart($context = NULL)
	{
		if (!CartRule::isFeatureActive())
			return;

		$errors = array();
		if (!$context)
			$context = Context::getContext();
		foreach ($context->cart->getCartRules() as $cart_rule)
		{
			if ($error = $cart_rule['obj']->checkValidity($context, true))
			{
				$context->cart->removeCartRule($cart_rule['obj']->id);
				$context->cart->update();
				$errors[] = $error;
			}
		}
		return $errors;
	}

	public static function autoAddToCart($context = NULL)
	{
		if ($context === NULL)
			$context = Context::getContext();
		if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart))
			return;

		$result = Db::getInstance()->ExecuteS('
		SELECT cr.*
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_carrier crca ON cr.id_cart_rule = crca.id_cart_rule
		LEFT JOIN '._DB_PREFIX_.'cart_rule_country crco ON cr.id_cart_rule = crco.id_cart_rule
		WHERE cr.active = 1
		AND cr.code = ""
		AND cr.quantity > 0
		AND cr.date_from < "'.date('Y-m-d H:i:s').'"
		AND cr.date_to > "'.date('Y-m-d H:i:s').'"
		AND cr.id_cart_rule NOT IN (SELECT id_cart_rule FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int)$context->cart->id.')
		AND (
			cr.id_customer = 0
			'.($context->customer->id ? 'OR cr.id_customer = '.(int)$context->cart->id_customer : '').'
		)
		AND (
			cr.carrier_restriction = 0
			'.($context->cart->id_carrier ? 'OR crca.id_carrier = '.(int)$context->cart->id_carrier : '').'
		)
		AND (
			cr.group_restriction = 0
			'.($context->customer->id ? 'OR 0 < (
				SELECT cg.id_group
				FROM '._DB_PREFIX_.'customer_group cg
				LEFT JOIN '._DB_PREFIX_.'cart_rule_group crg ON cg.id_group = crg.id_group
				WHERE cr.id_cart_rule = crg.id_cart_rule
				AND cg.id_customer = '.(int)$context->customer->id.'
			)' : '').'
		)
		AND (
			cr.reduction_product <= 0
			OR cr.reduction_product IN (
				SELECT id_product
				FROM '._DB_PREFIX_.'cart_product
				WHERE id_cart = '.(int)$context->cart->id.'
			)
		)
		ORDER BY priority');

		$cartRules = ObjectModel::hydrateCollection('CartRule', $result);

		// Todo: consider optimization (we can avoid many queries in checkValidity)
		foreach ($cartRules as $cartRule)
			if (!$cartRule->checkValidity($context))
				$context->cart->addCartRule($cartRule->id);
	}

	public static function isFeatureActive()
	{
		return (bool)Configuration::get('PS_CART_RULE_FEATURE_ACTIVE');
	}

	public static function getCartsRuleByCode($name, $id_lang)
	{
		return Db::getInstance()->ExecuteS('
			SELECT cr.*, crl.*
			FROM '._DB_PREFIX_.'cart_rule cr
			LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
			WHERE name LIKE \'%'.pSQL($name).'%\'
		');
	}
}