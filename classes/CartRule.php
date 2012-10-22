<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CartRuleCore extends ObjectModel
{
	/* Filters used when retrieving the cart rules applied to a cart of when calculating the value of a reduction */
	const FILTER_ACTION_ALL = 1;
	const FILTER_ACTION_SHIPPING = 2;
	const FILTER_ACTION_REDUCTION = 3;
	const FILTER_ACTION_GIFT = 4;
	
	/* This variable controls that a free gift is offered only once, even when multi-shippping is activated and the same product is delivered in both addresses */
	protected static $only_one_gift = array();

	public $id;
	public $name;
	public $id_customer;
	public $date_from;
	public $date_to;
	public $description;
	public $quantity = 1;
	public $quantity_per_user = 1;
	public $priority = 1;
	public $partial_use = 1;
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
	public $shop_restriction;
	public $free_shipping;
	public $reduction_percent;
	public $reduction_amount;
	public $reduction_tax;
	public $reduction_currency;
	public $reduction_product;
	public $gift_product;
	public $gift_product_attribute;
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
			'partial_use' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
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
			'shop_restriction' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'free_shipping' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'reduction_percent' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPercentage'),
			'reduction_amount' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'reduction_tax' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'reduction_currency' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'reduction_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'gift_product' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'gift_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'active' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

			// Lang fields
			'name' => 					array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
		),
	);

	/**
	 * @see ObjectModel::add()
	 */
	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues))
			return false;

		Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');
		return true;
	}

	/**
	 * @see ObjectModel::delete()
	 */
	public function delete()
	{
		if (!parent::delete())
			return false;

		Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', CartRule::isCurrentlyUsed($this->def['table'], true));

		$r = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_cart_rule` WHERE `id_cart_rule` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_combination` WHERE `id_cart_rule_1` = '.(int)$this->id.' OR `id_cart_rule_2` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_group` WHERE `id_cart_rule` = '.(int)$this->id);
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_product_rule_group` NOT IN (SELECT `id_product_rule_group` FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`)');
		$r &= Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');

		return $r;
	}

	/**
	 * Copy conditions from one cart rule to an other
	 *
	 * @static
	 * @param int $id_cart_rule_source
	 * @param int $id_cart_rule_destination
	 */
	public static function copyConditions($id_cart_rule_source, $id_cart_rule_destination)
	{
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
		(SELECT '.(int)$id_cart_rule_destination.', id_shop FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_carrier` (`id_cart_rule`, `id_carrier`)
		(SELECT '.(int)$id_cart_rule_destination.', id_carrier FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
		(SELECT '.(int)$id_cart_rule_destination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
		(SELECT '.(int)$id_cart_rule_destination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
		(SELECT '.(int)$id_cart_rule_destination.', IF(id_cart_rule_1 != '.(int)$id_cart_rule_source.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
		WHERE `id_cart_rule_1` = '.(int)$id_cart_rule_source.' OR `id_cart_rule_2` = '.(int)$id_cart_rule_source.')');

		// Todo : should be changed soon, be must be copied too
		// Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
		// Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');
	}

	/**
	 * Retrieves the id associated to the given code
	 *
	 * @static
	 * @param string $code
	 * @return int|bool
	 */
	public static function getIdByCode($code)
	{
		if (!Validate::isCleanHtml($code))
			return false;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` WHERE `code` = \''.pSQL($code).'\'');
	}

	/**
	 * @static
	 * @param $id_lang
	 * @param $id_customer
	 * @param bool $active
	 * @param bool $includeGeneric
	 * @param bool $inStock
	 * @param Cart|null $cart
	 * @return array
	 */
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

		foreach ($result as &$cart_rule)
			if ($cart_rule['quantity_per_user'])
			{
				$quantity_used = Order::getDiscountsCustomer((int)$id_customer, (int)$cart_rule['id_cart_rule']);
				if (isset($cart) && isset($cart->id))
					$quantity_used += $cart->getDiscountsCustomer((int)$cart_rule['id_cart_rule']);
				$cart_rule['quantity_for_user'] = $cart_rule['quantity_per_user'] - $quantity_used;
			}
			else
				$cart_rule['quantity_for_user'] = 0;

		// Retrocompatibility with 1.4 discounts
		foreach ($result as &$cart_rule)
		{
			$cart_rule['value'] = 0;
			$cart_rule['minimal'] = $cart_rule['minimum_amount'];
			$cart_rule['cumulable'] = !$cart_rule['cart_rule_restriction'];
			$cart_rule['id_discount_type'] = false;
			if ($cart_rule['free_shipping'])
				$cart_rule['id_discount_type'] = Discount::FREE_SHIPPING;
			elseif ($cart_rule['reduction_percent'] > 0)
			{
				$cart_rule['id_discount_type'] = Discount::PERCENT;
				$cart_rule['value'] = $cart_rule['reduction_percent'];
			}
			elseif ($cart_rule['reduction_amount'] > 0)
			{
				$cart_rule['id_discount_type'] = Discount::AMOUNT;
				$cart_rule['value'] = $cart_rule['reduction_amount'];
			}
		}

		return $result;
	}

	/**
	 * @param $id_customer
	 * @return bool
	 */
	public function usedByCustomer($id_customer)
	{
		return (bool)Db::getInstance()->getValue('
		SELECT id_cart_rule
		FROM `'._DB_PREFIX_.'order_cart_rule` ocr
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
		WHERE ocr.`id_cart_rule` = '.(int)$this->id.'
		AND o.`id_customer` = '.(int)$id_customer);
	}

	/**
	 * @static
	 * @param $name
	 * @return bool
	 */
	public static function cartRuleExists($name)
	{
		if (!CartRule::isFeatureActive())
			return false;

		return (bool)Db::getInstance()->getValue('
		SELECT `id_cart_rule`
		FROM `'._DB_PREFIX_.'cart_rule`
		WHERE `code` = \''.pSQL($name).'\'');
	}

	/**
	 * @static
	 * @param $id_customer
	 * @return bool
	 */
	public static function deleteByIdCustomer($id_customer)
	{
		$return = true;
		$cart_rules = new Collection('CartRule');
		$cart_rules->where('id_customer', '=', $id_customer);
		foreach ($cart_rules as $cart_rule)
			$return &= $cart_rule->delete();
		return $return;
	}

	/**
	 * @return array
	 */
	public function getProductRuleGroups()
	{
		if (!Validate::isLoadedObject($this) || $this->product_restriction == 0)
			return array();

		$productRuleGroups = array();
		$results = Db::getInstance()->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'cart_rule_product_rule_group prg
		WHERE prg.id_cart_rule = '.(int)$this->id, false);
		foreach ($results as $row)
		{
			if (!isset($productRuleGroups[$row['id_product_rule_group']]))
				$productRuleGroups[$row['id_product_rule_group']] = array('id_product_rule_group' => $row['id_product_rule_group'], 'quantity' => $row['quantity']);
			$productRuleGroups[$row['id_product_rule_group']]['product_rules'] = $this->getProductRules($row['id_product_rule_group']);
		}
		return $productRuleGroups;
	}

	/**
	 * @param $id_product_rule_group
	 * @return array ('type' => ? , 'values' => ?)
	 */
	public function getProductRules($id_product_rule_group)
	{
		if (!Validate::isLoadedObject($this) || $this->product_restriction == 0)
			return array();

		$productRules = array();
		$results = Db::getInstance()->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'cart_rule_product_rule pr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value prv ON pr.id_product_rule = prv.id_product_rule
		WHERE pr.id_product_rule_group = '.(int)$id_product_rule_group);
		foreach ($results as $row)
		{
			if (!isset($productRules[$row['id_product_rule']]))
				$productRules[$row['id_product_rule']] = array('type' => $row['type'], 'values' => array());
			$productRules[$row['id_product_rule']]['values'][] = $row['id_item'];
		}
		return $productRules;
	}

	/**
	 * Check if this cart rule can be applied
	 *
	 * @param Context $context
	 * @param bool $alreadyInCart Check if the voucher is already on the cart
	 * @param bool $display_error Display error
	 * @return bool|mixed|string
	 */
	public function checkValidity(Context $context, $alreadyInCart = false, $display_error = true)
	{
		if (!CartRule::isFeatureActive())
			return false;

		if (!$this->active)
			return (!$display_error) ? false : Tools::displayError('This voucher is disabled');
		if (!$this->quantity)
			return (!$display_error) ? false : Tools::displayError('This voucher has already been used');
		if (strtotime($this->date_from) > time())
			return (!$display_error) ? false : Tools::displayError('This voucher is not valid yet');
		if (strtotime($this->date_to) < time())
			return (!$display_error) ? false : Tools::displayError('This voucher has expired');

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
				return (!$display_error) ? false : Tools::displayError('You cannot use this voucher anymore (usage limit reached)');
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
				return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
		}

		// Check if the customer delivery address is usable with the cart rule
		if ($this->country_restriction && $context->cart->id_address_delivery)
		{
			$id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_country crc
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND crc.id_country = (SELECT a.id_country FROM '._DB_PREFIX_.'address a WHERE a.id_address = '.(int)$context->cart->id_address_delivery.' LIMIT 1)');
			if (!$id_cart_rule)
				return (!$display_error) ? false : Tools::displayError('You cannot use this voucher in your country of delivery');
		}

		// Check if the carrier chosen by the customer is usable with the cart rule
		if ($this->carrier_restriction && $context->cart->id_carrier)
		{
			$id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_carrier crc
			WHERE crc.id_cart_rule = '.(int)$this->id.'
			AND crc.id_carrier = '.(int)$context->cart->id_carrier);
			if (!$id_cart_rule)
				return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with this carrier');
		}

		// Check if the cart rules appliy to the shop browsed by the customer
		if ($this->shop_restriction && $context->shop->id && Shop::isFeatureActive())
		{
			$id_cart_rule = (int)Db::getInstance()->getValue('
			SELECT crs.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_shop crs
			WHERE crs.id_cart_rule = '.(int)$this->id.'
			AND crs.id_shop = '.(int)$context->shop->id);
			if (!$id_cart_rule)
				return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
		}

		// Check if the products chosen by the customer are usable with the cart rule
		if ($this->product_restriction)
		{
			$r = $this->checkProductRestrictions($context, false, $display_error);
			if ($r !== false && $display_error)
				return $r;
			elseif (!$r && !$display_error)
				return false;
		}

		// Check if the cart rule is only usable by a specific customer, and if the current customer is the right one
		if ($this->id_customer && $context->cart->id_customer != $this->id_customer)
		{
			if (!Context::getContext()->customer->isLogged())
				return (!$display_error) ? false : (Tools::displayError('You cannot use this voucher').' - '.Tools::displayError('Please log in'));
			return (!$display_error) ? false : Tools::displayError('You cannot use this voucher');
		}

		if ($this->minimum_amount)
		{
			// Minimum amount is converted to the default currency
			$minimum_amount = $this->minimum_amount;
			if ($this->minimum_amount_currency != Configuration::get('PS_CURRENCY_DEFAULT'))
			{
				$minimum_amount_currency = new Currency($this->minimum_amount_currency);
				if ($this->minimum_amount == 0 || $minimum_amount_currency->conversion_rate == 0)
					$minimum_amount = 0;
				else
					$minimum_amount = $this->minimum_amount / $minimum_amount_currency->conversion_rate;
			}

			$cartTotal = $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_PRODUCTS);
			if ($this->minimum_amount_shipping)
				$cartTotal += $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_SHIPPING);

			// If a product is given for free in this rule and already in the cart, the price is subtracted
			if ($this->gift_product && $alreadyInCart)
			{
				$query = new DbQuery();
				
				$query->select('id_product');
				$query->from('cart_product');
				$query->where('id_product = '.(int)$this->gift_product);
				$query->where('id_cart = '.(int)$context->cart->id);
				
				if ((int)$this->gift_product_attribute)
					$query->where('id_product_attribute = '.(int)$this->gift_product_attribute);
				
				if (Db::getInstance()->getValue($query))
				{
					$ref = false;
					$product_price = Product::getPriceStatic(
						$this->gift_product,
						$this->minimum_amount_tax,
						$this->gift_product_attribute,
						null, null, false, true, 1, null,
						$context->cart->id_customer ? $context->cart->id_customer : null,
						$context->cart->id,
						(int)$context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} ? (int)$context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} : null,
						$ref, true, true, $context, true
					);
					$cartTotal -= $product_price;
				}
			}

			if ($cartTotal < $minimum_amount)
				return (!$display_error) ? false : Tools::displayError('You have not reached the minimum amount required to use this voucher');
		}
		
		// Check if the voucher is already in the cart of if a non compatible voucher is in the cart
		// Important note: this MUST be the last check, because if the tested cart rule has priority over a non combinable one in the cart, we will switch them
		$otherCartRules = $context->cart->getCartRules();
		if (count($otherCartRules))
			foreach ($otherCartRules as $otherCartRule)
			{
				if ($otherCartRule['id_cart_rule'] == $this->id && !$alreadyInCart)
					return (!$display_error) ? false : Tools::displayError('This voucher is already in your cart');
				if ($this->cart_rule_restriction && $otherCartRule['cart_rule_restriction'] && $otherCartRule['id_cart_rule'] != $this->id)
				{
					$combinable = Db::getInstance()->getValue('
					SELECT id_cart_rule_1
					FROM '._DB_PREFIX_.'cart_rule_combination
					WHERE (id_cart_rule_1 = '.(int)$this->id.' AND id_cart_rule_2 = '.(int)$otherCartRule['id_cart_rule'].')
					OR (id_cart_rule_2 = '.(int)$this->id.' AND id_cart_rule_1 = '.(int)$otherCartRule['id_cart_rule'].')');
					if (!$combinable)
					{
						$cart_rule = new CartRule((int)$otherCartRule['id_cart_rule'], $context->cart->id_lang);
						// The cart rules are not combinable and the cart rule currently in the cart has priority over the one tested
						if ($cart_rule->priority <= $this->priority)
							return (!$display_error) ? false : Tools::displayError('This voucher is not combinable with an other voucher already in your cart:').' '.$cart_rule->name;
						// But if the cart rule that is tested has priority over the one in the cart, we remove the one in the cart and keep this new one
						else
							$context->cart->removeCartRule($cart_rule->id);
					}
				}
			}
		
		if (!$display_error)
			return true;
	}

	protected function checkProductRestrictions(Context $context, $return_products = false, $display_error = true)
	{
		$selectedProducts = array();

		// Check if the products chosen by the customer are usable with the cart rule
		if ($this->product_restriction)
		{
			$productRuleGroups = $this->getProductRuleGroups();
			foreach ($productRuleGroups as $id_product_rule_group => $productRuleGroup)
			{
				$eligibleProductsList = array();
				foreach ($context->cart->getProducts() as $product)
					$eligibleProductsList[] = (int)$product['id_product'].'-'.(int)$product['id_product_attribute'];
				if (!count($eligibleProductsList))
					return (!$display_error) ? false : Tools::displayError('You cannot use this voucher in an empty cart');
				
				$productRules = $this->getProductRules($id_product_rule_group);
				foreach ($productRules as $productRule)
				{
					switch ($productRule['type'])
					{
						case 'attributes':
							$cartAttributes = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, pac.`id_attribute`, cp.`id_product_attribute`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON cp.id_product_attribute = pac.id_product_attribute
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(array_map('intval', $eligibleProductsList), ',').')
							AND cp.id_product_attribute > 0');
							$countMatchingProducts = 0;
							$matchingProductsList = array();
							foreach ($cartAttributes as $cartAttribute)
								if (in_array($cartAttribute['id_attribute'], $productRule['values']))
								{
									$countMatchingProducts += $cartAttribute['quantity'];
									$matchingProductsList[] = $cartAttribute['id_product'].'-'.$cartAttribute['id_product_attribute'];
								}
							if ($countMatchingProducts < $productRuleGroup['quantity'])
								return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
							$eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
							break;
						case 'products':
							$cartProducts = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`
							FROM `'._DB_PREFIX_.'cart_product` cp
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(array_map('intval', $eligibleProductsList), ',').')');
							$countMatchingProducts = 0;
							$matchingProductsList = array();
							foreach ($cartProducts as $cartProduct)
								if (in_array($cartProduct['id_product'], $productRule['values']))
								{
									$countMatchingProducts += $cartProduct['quantity'];
									$matchingProductsList[] = $cartProduct['id_product'].'-0';
								}
							if ($countMatchingProducts < $productRuleGroup['quantity'])
								return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
							$eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
							break;
						case 'categories':
							$cartCategories = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, cp.`id_product_attribute`, catp.`id_category`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'category_product` catp ON cp.id_product = catp.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(array_map('intval', $eligibleProductsList), ',').')');
							$countMatchingProducts = 0;
							$matchingProductsList = array();
							foreach ($cartCategories as $cartCategory)
								if (in_array($cartCategory['id_category'], $productRule['values'])
									// We also check that the product is not already in the matching product list, because there are doubles in the query results (when the product is in multiple categories)
									&& !in_array($cartCategory['id_product'].'-'.$cartCategory['id_product_attribute'], $matchingProductsList))
								{
									$countMatchingProducts += $cartCategory['quantity'];
									$matchingProductsList[] = $cartCategory['id_product'].'-'.$cartCategory['id_product_attribute'];
								}
							if ($countMatchingProducts < $productRuleGroup['quantity'])
								return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
							// Attribute id is not important for this filter in the global list, so the ids are replaced by 0
							foreach ($matchingProductsList as &$matchingProduct)
								$matchingProduct = preg_replace('/^([0-9]+)-[0-9]+$/', '$1-0', $matchingProduct);
							$eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
							break;
						case 'manufacturers':
							$cartManufacturers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_manufacturer`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(array_map('intval', $eligibleProductsList), ',').')');
							$countMatchingProducts = 0;
							$matchingProductsList = array();
							foreach ($cartManufacturers as $cartManufacturer)
								if (in_array($cartManufacturer['id_manufacturer'], $productRule['values']))
								{
									$countMatchingProducts += $cartManufacturer['quantity'];
									$matchingProductsList[] = $cartManufacturer['id_product'].'-0';
								}
							if ($countMatchingProducts < $productRuleGroup['quantity'])
								return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
							$eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
							break;
						case 'suppliers':
							$cartSuppliers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_supplier`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int)$context->cart->id.'
							AND cp.`id_product` IN ('.implode(array_map('intval', $eligibleProductsList), ',').')');
							$countMatchingProducts = 0;
							$matchingProductsList = array();
							foreach ($cartSuppliers as $cartSupplier)
								if (in_array($cartSupplier['id_supplier'], $productRule['values']))
								{
									$countMatchingProducts += $cartSupplier['quantity'];
									$matchingProductsList[] = $cartSupplier['id_product'].'-0';
								}
							if ($countMatchingProducts < $productRuleGroup['quantity'])
								return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
							$eligibleProductsList = CartRule::array_uintersect($eligibleProductsList, $matchingProductsList);
							break;
					}

					if (!count($eligibleProductsList))
						return (!$display_error) ? false : Tools::displayError('You cannot use this voucher with these products');
				}
				$selectedProducts = array_merge($selectedProducts, $eligibleProductsList);
			}
		}

		if ($return_products)
			return $selectedProducts;
		return (!$display_error) ? true : false;
	}

	protected static function array_uintersect($array1, $array2)
	{
		$intersection = array();
		foreach ($array1 as $value1)
			foreach ($array2 as $value2)
				if (CartRule::array_uintersect_compare($value1, $value2) == 0)
				{
					$intersection[] = $value1;
					break 1;
				}
		return $intersection;
	}

	protected static function array_uintersect_compare($a, $b)
	{
		if ($a == $b)
			return 0;

		$asplit = explode('-', $a);
		$bsplit = explode('-', $b);
		if ($asplit[0] == $bsplit[0] && (!(int)$asplit[1] || !(int)$bsplit[1]))
			return 0;

		return 1;
	}

	/**
	 * The reduction value is POSITIVE
	 *
	 * @param bool $use_tax
	 * @param Context $context
	 * @param boolean $use_cache Allow using cache to avoid multiple free gift using multishipping
	 * @return float|int|string
	 */
	public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true)
	{
		if (!CartRule::isFeatureActive())
			return 0;
		if (!$context)
			$context = Context::getContext();
		if (!$filter)
			$filter = CartRule::FILTER_ACTION_ALL;
		
		$all_products = $context->cart->getProducts();
		$package_products = (is_null($package) ? $all_products : $package['products']);

		$reduction_value = 0;

		$cache_id = 'getContextualValue_'.(int)$use_tax.'_'.(int)$context->cart->id.'_'.(int)$filter;
		foreach ($package_products as $product)
			$cache_id .= (int)$product['id_product'].'_'.(int)$product['id_product_attribute'];

		if (Cache::isStored($cache_id))
			return Cache::retrieve($cache_id);

		// Free shipping on selected carriers
		if ($this->free_shipping && ($filter == CartRule::FILTER_ACTION_ALL || $filter == CartRule::FILTER_ACTION_SHIPPING))
		{
			if (!$this->carrier_restriction)
				$reduction_value += $context->cart->getOrderTotal($use_tax, Cart::ONLY_SHIPPING, is_null($package) ? null : $package['products'], is_null($package) ? null : $package['id_carrier']);
			else
			{
				$data = Db::getInstance()->executeS('
					SELECT crc.id_cart_rule, crc.id_carrier
					FROM '._DB_PREFIX_.'cart_rule_carrier crc
					WHERE crc.id_cart_rule = '.(int)$this->id.'
					AND crc.id_carrier = '.(int)$context->cart->id_carrier);

				if ($data)
					foreach ($data as $cart_rule)
						$reduction_value += $context->cart->getCarrierCost((int)$cart_rule['id_carrier'], $use_tax, $context->country);
			}
		}

		if ($filter == CartRule::FILTER_ACTION_ALL || $filter == CartRule::FILTER_ACTION_REDUCTION)
		{
			// Discount (%) on the whole order
			if ($this->reduction_percent && $this->reduction_product == 0)
			{
				// Do not give a reduction on free products!
				$order_total = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package_products);
				foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cart_rule)
					$order_total -= Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), 2);

				$reduction_value += $order_total * $this->reduction_percent / 100;
			}

			// Discount (%) on a specific product
			if ($this->reduction_percent && $this->reduction_product > 0)
			{
				foreach ($package_products as $product)
					if ($product['id_product'] == $this->reduction_product)
						$reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
			}

			// Discount (%) on the cheapest product
			if ($this->reduction_percent && $this->reduction_product == -1)
			{
				$minPrice = false;
				$cheapest_product = null;
				foreach ($all_products as $product)
				{
					$price = ($use_tax ? $product['price_wt'] : $product['price']);
					if ($price > 0 && ($minPrice === false || $minPrice > $price))
					{
						$minPrice = $price;
						$cheapest_product = $product['id_product'].'-'.$product['id_product_attribute'];
					}
				}
				
				// Check if the cheapest product is in the package
				$in_package = false;
				foreach ($package_products as $product)
					if ($product['id_product'].'-'.$product['id_product_attribute'] == $cheapest_product || $product['id_product'].'-0' == $cheapest_product)
						$in_package = true;
				if ($in_package)
					$reduction_value += $minPrice * $this->reduction_percent / 100;
			}

			// Discount (%) on the selection of products
			if ($this->reduction_percent && $this->reduction_product == -2)
			{
				$selected_products_reduction = 0;
				$selected_products = $this->checkProductRestrictions($context, true);
				if (is_array($selected_products))
					foreach ($package_products as $product)
						if (in_array($product['id_product'].'-'.$product['id_product_attribute'], $selected_products)
							|| in_array($product['id_product'].'-0', $selected_products))
						{
							$price = ($use_tax ? $product['price_wt'] : $product['price']);
							$selected_products_reduction += $price * $product['cart_quantity'];
						}
				$reduction_value += $selected_products_reduction * $this->reduction_percent / 100;
			}

			// Discount (¤)
			if ($this->reduction_amount)
			{
				$prorata = 1;
				if (!is_null($package) && count($all_products))
				{
					$total_products = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
					if ($total_products)
						$prorata = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package['products']) / $total_products;
				}

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
				if ($this->reduction_tax == $use_tax)
					$reduction_value += $prorata * $reduction_amount;
				else
				{
					if ($this->reduction_product > 0)
					{
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

								if ($this->reduction_tax && !$use_tax)
									$reduction_value += $prorata * $reduction_amount / (1 + $product_vat_rate);
								elseif (!$this->reduction_tax && $use_tax)
									$reduction_value += $prorata * $reduction_amount * (1 + $product_vat_rate);
							}
					}
					// Discount (¤) on the whole order
					elseif ($this->reduction_product == 0)
					{
						// TODO : this should not use the prorata
						$cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
						$cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

						$cart_vat_amount = $cart_amount_ti - $cart_amount_te;

						if ($cart_vat_amount == 0 || $cart_amount_te == 0)
							$cart_average_vat_rate = 0;
						else
							$cart_average_vat_rate = $cart_vat_amount / $cart_amount_te;

						if ($this->reduction_tax && !$use_tax)
							$reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
						elseif (!$this->reduction_tax && $use_tax)
							$reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);
					}
					/*
					 * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
					 * Please keep this code, so it won't be considered as a bug
					 * elseif ($this->reduction_product == -1)
					 * elseif ($this->reduction_product == -2)
					*/
				}
			}
		}

		// Free gift
		if ((int)$this->gift_product && ($filter == CartRule::FILTER_ACTION_ALL || $filter == CartRule::FILTER_ACTION_GIFT))
		{
			$id_address = (is_null($package) ? 0 : $package['id_address']);
			foreach ($package_products as $product)
				if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int)$this->gift_product_attribute))
				{
					// The free gift coupon must be applied to one product only (needed for multi-shipping which manage multiple product lists)
					if (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product])
						|| CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == $id_address
						|| CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0
						|| $id_address == 0
						|| !$use_cache)
					{
						$reduction_value += ($use_tax ? $product['price_wt'] : $product['price']);
						if ($use_cache && (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product]) || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0))
							CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] = $id_address;
						break;
					}
				}
		}

		Cache::store($cache_id, $reduction_value);
		return $reduction_value;
	}
	
	/**
	 * Make sure caches are empty
	 * Must be called before calling multiple time getContextualValue()
	 */
	public static function cleanCache()
	{
		self::$only_one_gift = array();
	}

	protected function getCartRuleCombinations()
	{
		$array = array();
		$array['selected'] = Db::getInstance()->executeS('
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
		$array['unselected'] = Db::getInstance()->executeS('
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

	public function getAssociatedRestrictions($type, $active_only, $i18n)
	{
		$array = array('selected' => array(), 'unselected' => array());

		if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop')))
			return false;

		$shop_list = '';
		if ($type == 'shop')
		{
			$shops = Context::getContext()->employee->getAssociatedShops();
			if (count($shops))
				$shop_list = ' AND t.id_shop IN ('.implode(array_map('intval', $shops), ',').') ';
		}

		if (!Validate::isLoadedObject($this) OR $this->{$type.'_restriction'} == 0)
		{
			$array['selected'] = Db::getInstance()->executeS('
			SELECT t.*'.($i18n ? ', tl.*' : '').', 1 as selected
			FROM `'._DB_PREFIX_.$type.'` t
			'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
			WHERE 1
			'.($active_only ? 'AND t.active = 1' : '').'
			'.(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').'
			'.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id : '').
			$shop_list.
			' ORDER BY name ASC');
		}
		else
		{
			if ($type == 'cart_rule')
				$array = $this->getCartRuleCombinations();
			else
			{
				$resource = Db::getInstance()->query('
				SELECT t.*'.($i18n ? ', tl.*' : '').', IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
				FROM `'._DB_PREFIX_.$type.'` t
				'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
				LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int)$this->id.') crt ON t.id_'.$type.' = crt.id_'.$type.'
				WHERE 1 '.($active_only ? ' AND t.active = 1' : '').
				$shop_list.
				' ORDER BY name ASC',
				false);
				while ($row = Db::getInstance()->nextRow($resource))
					$array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
			}
		}
		return $array;
	}

	public static function autoRemoveFromCart($context = null)
	{
		if (!$context)
			$context = Context::getContext();
		if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart))
			return array();

		$errors = array();
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

	/**
	 * @static
	 * @param Context|null $context
	 * @return mixed
	 */
	public static function autoAddToCart(Context $context = null)
	{
		if ($context === null)
			$context = Context::getContext();
		if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart))
			return;

		$result = Db::getInstance()->executeS('
		SELECT cr.*
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_shop crs ON cr.id_cart_rule = crs.id_cart_rule
		LEFT JOIN '._DB_PREFIX_.'cart_rule_carrier crca ON cr.id_cart_rule = crca.id_cart_rule
		LEFT JOIN '._DB_PREFIX_.'cart_rule_country crco ON cr.id_cart_rule = crco.id_cart_rule
		WHERE cr.active = 1
		AND cr.code = ""
		AND cr.quantity > 0
		AND cr.date_from < "'.date('Y-m-d H:i:s').'"
		AND cr.date_to > "'.date('Y-m-d H:i:s').'"
		AND (
			cr.id_customer = 0
			'.($context->customer->id ? 'OR cr.id_customer = '.(int)$context->cart->id_customer : '').'
		)
		AND (
			cr.carrier_restriction = 0
			'.($context->cart->id_carrier ? 'OR crca.id_carrier = '.(int)$context->cart->id_carrier : '').'
		)
		AND (
			cr.shop_restriction = 0
			'.((Shop::isFeatureActive() && $context->shop->id) ? 'OR crs.id_shop = '.(int)$context->shop->id : '').'
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
		AND cr.id_cart_rule NOT IN (SELECT id_cart_rule	FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int)$context->cart->id.')
		ORDER BY priority');
		
		if ($result)
		{
			$cart_rules = ObjectModel::hydrateCollection('CartRule', $result);
			if ($cart_rules)
				foreach ($cart_rules as $cart_rule)
					if ($cart_rule->checkValidity($context, false, false))
						$context->cart->addCartRule($cart_rule->id);
		}
	}

	/**
	 * @static
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		static $is_feature_active = null;
		if ($is_feature_active === null)
			$is_feature_active = (bool)Configuration::get('PS_CART_RULE_FEATURE_ACTIVE');
		return $is_feature_active;
	}
	
	/* When an entity associated to a product rule (product, category, attribute, supplier, manufacturer...) is deleted, the product rules must be updated */
	public static function cleanProductRuleIntegrity($type, $list)
	{
		// Type must be available in the 'type' enum of the table cart_rule_product_rule
		if (!in_array($type, array('products', 'categories', 'attributes', 'manufacturers', 'suppliers')))
			return false;
		
		// This check must not be removed because this var is used a few lines below
		$list = (is_array($list) ? implode(',', array_map('intval', $list)) : (int)$list);
		if (!preg_match('/^[0-9,]+$/', $list))
			return false;
			
		// Delete associated restrictions on cart rules
		Db::getInstance()->execute('
		DELETE crprv
		FROM `'._DB_PREFIX_.'cart_rule_product_rule` crpr
		LEFT JOIN `'._DB_PREFIX_.'cart_rule_product_rule_value` crprv ON crpr.`id_product_rule` = crprv.`id_product_rule`
		WHERE crpr.`type` = "'.pSQL($type).'"
		AND crprv.`id_item` IN ('.$list.')'); // $list is checked a few lines above
			
		// Delete the product rules that does not have any values
		if (Db::getInstance()->Affected_Rows() > 0)
			Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule`
			WHERE `id_product_rule` NOT IN (SELECT id_product_rule FROM `'._DB_PREFIX_.'cart_rule_product_rule_value`)');
		// If the product rules were the only conditions of a product rule group, delete the product rule group
		if (Db::getInstance()->Affected_Rows() > 0)
			Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`
			WHERE `id_product_rule_group` NOT IN (SELECT id_product_rule_group FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');
		// If the product rule group were the only restrictions of a cart rule, update de cart rule restriction cache
		if (Db::getInstance()->Affected_Rows() > 0)
			Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'cart_rule` cr
			LEFT JOIN `'._DB_PREFIX_.'cart_rule_product_rule_group` crprg ON cr.id_cart_rule = crprg.id_cart_rule
			SET product_restriction = IF(crprg.id_product_rule_group IS NULL, 0, 1)');

		return true;
	}

	/**
	 * @static
	 * @param $name
	 * @param $id_lang
	 * @return array
	 */
	public static function getCartsRuleByCode($name, $id_lang)
	{
		return Db::getInstance()->executeS('
			SELECT cr.*, crl.*
			FROM '._DB_PREFIX_.'cart_rule cr
			LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
			WHERE code LIKE \'%'.pSQL($name).'%\'
		');
	}
}

