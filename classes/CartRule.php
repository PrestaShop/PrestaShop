<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class CartRuleCore
 */
class CartRuleCore extends ObjectModel
{
    /* Filters used when retrieving the cart rules applied to a cart of when calculating the value of a reduction */
    const FILTER_ACTION_ALL = 1;
    const FILTER_ACTION_SHIPPING = 2;
    const FILTER_ACTION_REDUCTION = 3;
    const FILTER_ACTION_GIFT = 4;
    const FILTER_ACTION_ALL_NOCAP = 5;

    const BO_ORDER_CODE_PREFIX = 'BO_ORDER_';

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
    public $reduction_exclude_special;
    public $gift_product;
    public $gift_product_attribute;
    public $highlight;
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
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_from' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_to' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'description' =>            array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65534),
            'quantity' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'quantity_per_user' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'priority' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'partial_use' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'code' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254),
            'minimum_amount' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'minimum_amount_tax' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'minimum_amount_currency' =>array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'minimum_amount_shipping' =>array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'country_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'carrier_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'group_restriction' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'cart_rule_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'product_restriction' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'shop_restriction' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'free_shipping' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'reduction_percent' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isPercentage'),
            'reduction_amount' =>        array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'reduction_tax' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'reduction_currency' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'reduction_product' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'reduction_exclude_special' =>      array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'gift_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'gift_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'highlight' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            /* Lang fields */
            'name' =>                    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 254),
        ),
    );

    /**
     * Adds current CartRule as a new Object to the database
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the CartRule has been successfully added
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if (!$this->reduction_currency) {
            $this->reduction_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }

        if (!parent::add($autoDate, $nullValues)) {
            return false;
        }

        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', '1');

        return true;
    }

    /**
     * Updates the current object in the database
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the CartRule has been successfully updated
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        Cache::clean('getContextualValue_'.$this->id.'_*');

        if (!$this->reduction_currency) {
            $this->reduction_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        }

        return parent::update($nullValues);
    }

    /**
     * Deletes current CartRule from the database
     *
     * @return bool True if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!parent::delete()) {
            return false;
        }

        Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', CartRule::isCurrentlyUsed($this->def['table'], true));

        $r = Db::getInstance()->delete('cart_cart_rule', '`id_cart_rule` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_carrier', '`id_cart_rule` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_shop', '`id_cart_rule` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_group', '`id_cart_rule` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_country', '`id_cart_rule` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_combination', '`id_cart_rule_1` = '.(int) $this->id.' OR `id_cart_rule_2` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_product_rule_group', '`id_cart_rule` = '.(int) $this->id);
        $r &= Db::getInstance()->delete('cart_rule_product_rule', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`
			WHERE `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule_group` = `'._DB_PREFIX_.'cart_rule_product_rule_group`.`id_product_rule_group`)');
        $r &= Db::getInstance()->delete('cart_rule_product_rule_value', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule`
			WHERE `'._DB_PREFIX_.'cart_rule_product_rule_value`.`id_product_rule` = `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule`)');

        return $r;
    }

    /**
     * Copy conditions from one CartRule to another
     *
     * @param int $idCartRuleSource      Source CartRule ID
     * @param int $idCartRuleDestination Destination CartRule ID
     */
    public static function copyConditions($idCartRuleSource, $idCartRuleDestination)
    {
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
		(SELECT '.(int)$idCartRuleDestination.', id_shop FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int) $idCartRuleSource.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_carrier` (`id_cart_rule`, `id_carrier`)
		(SELECT '.(int)$idCartRuleDestination.', id_carrier FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int) $idCartRuleSource.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
		(SELECT '.(int)$idCartRuleDestination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int) $idCartRuleSource.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
		(SELECT '.(int)$idCartRuleDestination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int) $idCartRuleSource.')');
        Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
		(SELECT '.(int)$idCartRuleDestination.', IF(id_cart_rule_1 != '.(int) $idCartRuleSource.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
		WHERE `id_cart_rule_1` = '.(int) $idCartRuleSource.' OR `id_cart_rule_2` = '.(int) $idCartRuleSource.')');

        // Todo : should be changed soon, be must be copied too
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');

        // Copy products/category filters
        $productsRulesGroupSource = Db::getInstance()->ExecuteS('
		SELECT id_product_rule_group,quantity FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`
		WHERE `id_cart_rule` = '.(int) $idCartRuleSource.' ');

        foreach ($productsRulesGroupSource as $productRuleGroupSource) {
            Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
			VALUES ('.(int) $idCartRuleDestination.','.(int) $productRuleGroupSource['quantity'].')');
            $idProductRuleGroupDestination = Db::getInstance()->Insert_ID();

            $productsRulesSource = Db::getInstance()->ExecuteS('
			SELECT id_product_rule,type FROM `'._DB_PREFIX_.'cart_rule_product_rule`
			WHERE `id_product_rule_group` = '.(int) $productRuleGroupSource['id_product_rule_group'].' ');

            foreach ($productsRulesSource as $productRuleSource) {
                Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
				VALUES ('.(int) $idProductRuleGroupDestination.',"'.pSQL($productRuleSource['type']).'")');
                $idProductRuleDestination = Db::getInstance()->Insert_ID();

                $productsRulesValuesSource = Db::getInstance()->ExecuteS('
				SELECT id_item FROM `'._DB_PREFIX_.'cart_rule_product_rule_value`
				WHERE `id_product_rule` = '.(int) $productRuleSource['id_product_rule'].' ');

                foreach ($productsRulesValuesSource as $productRuleValueSource) {
                    Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`)
					VALUES ('.(int) $idProductRuleDestination.','.(int) $productRuleValueSource['id_item'].')');
                }
            }
        }
    }

    /**
     * Retrieves the CartRule ID associated with the given voucher code
     *
     * @param string $code Voucher code
     *
     * @return int|bool CartRule ID
     *                  false if not found
     */
    public static function getIdByCode($code)
    {
        if (!Validate::isCleanHtml($code)) {
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` WHERE `code` = \''.pSQL($code).'\'');
    }

    /**
     * Get CartRules for the given Customer
     *
     * @param int       $idLang           Language ID
     * @param int       $idCustomer       Customer ID
     * @param bool      $active           Active vouchers only
     * @param bool      $includeGeneric   Include generic AND highlighted vouchers, regardless of highlight_only setting
     * @param bool      $inStock          Vouchers in stock only
     * @param Cart|null $cart             Cart
     * @param bool      $freeShippingOnly Free shipping only
     * @param bool      $highlightOnly    Highlighted vouchers only
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getCustomerCartRules(
        $idLang,
        $idCustomer,
        $active = false,
        $includeGeneric = true,
        $inStock = false,
        Cart $cart = null,
        $freeShippingOnly = false,
        $highlightOnly = false
    ) {
        if (!CartRule::isFeatureActive()) {
            return array();
        }

        $sqlPart1 = '* FROM `'._DB_PREFIX_.'cart_rule` cr
				LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cr.`id_cart_rule` = crl.`id_cart_rule` AND crl.`id_lang` = '.(int) $idLang.')';

        $sqlPart2 = ' AND cr.date_from < "'.date('Y-m-d H:i:s').'"
				AND cr.date_to > "'.date('Y-m-d H:i:s').'"
				'.($active ? 'AND cr.`active` = 1' : '').'
				'.($inStock ? 'AND cr.`quantity` > 0' : '');

        if ($freeShippingOnly) {
            $sqlPart2 .= ' AND free_shipping = 1 AND carrier_restriction = 1';
        }

        if ($highlightOnly) {
            $sqlPart2 .= ' AND highlight = 1 AND code NOT LIKE "'.pSQL(CartRule::BO_ORDER_CODE_PREFIX).'%"';
        }

        $sql = '(SELECT SQL_NO_CACHE '.$sqlPart1.' WHERE cr.`id_customer` = '.(int) $idCustomer.' '.$sqlPart2.')';
        $sql .= ' UNION (SELECT '.$sqlPart1.' WHERE cr.`group_restriction` = 1 '.$sqlPart2.')';
        if ($includeGeneric && (int) $idCustomer != 0) {
            $sql .= ' UNION (SELECT '.$sqlPart1.' WHERE cr.`id_customer` = 0 '.$sqlPart2.')';
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (empty($result)) {
            return array();
        }

        // Remove cart rule that does not match the customer groups
        $customerGroups = Customer::getGroupsStatic($idCustomer);

        foreach ($result as $key => $cartRule) {
            if ($cartRule['group_restriction']) {
                $cartRuleGroups = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'cart_rule_group WHERE id_cart_rule = '.(int) $cartRule['id_cart_rule']);
                foreach ($cartRuleGroups as $cartRuleGroup) {
                    if (in_array($cartRuleGroup['id_group'], $customerGroups)) {
                        continue 2;
                    }
                }
                unset($result[$key]);
            }
        }

        foreach ($result as &$cartRule) {
            if ($cartRule['quantity_per_user']) {
                $quantityUsed = Order::getDiscountsCustomer((int) $idCustomer, (int) $cartRule['id_cart_rule']);
                if (isset($cart) && isset($cart->id)) {
                    $quantityUsed += $cart->getDiscountsCustomer((int) $cartRule['id_cart_rule']);
                }
                $cartRule['quantity_for_user'] = $cartRule['quantity_per_user'] - $quantityUsed;
            } else {
                $cartRule['quantity_for_user'] = 0;
            }
        }
        unset($cartRule);

        foreach ($result as $key => $cartRule) {
            if ($cartRule['shop_restriction']) {
                $cartRuleShops = Db::getInstance()->executeS('SELECT id_shop FROM '._DB_PREFIX_.'cart_rule_shop WHERE id_cart_rule = '.(int) $cartRule['id_cart_rule']);
                foreach ($cartRuleShops as $cartRuleShop) {
                    if (Shop::isFeatureActive() && ($cartRuleShop['id_shop'] == Context::getContext()->shop->id)) {
                        continue 2;
                    }
                }
                unset($result[$key]);
            }
        }

        if (isset($cart) && isset($cart->id)) {
            foreach ($result as $key => $cartRule) {
                if ($cartRule['product_restriction']) {
                    $cr = new CartRule((int) $cartRule['id_cart_rule']);
                    $r = $cr->checkProductRestrictions(Context::getContext(), false, false);
                    if ($r !== false) {
                        continue;
                    }
                    unset($result[$key]);
                }
            }
        }

        $resultBak = $result;
        $result = array();
        $countryRestriction = false;
        foreach ($resultBak as $key => $cartRule) {
            if ($cartRule['country_restriction']) {
                $countryRestriction = true;
                $countries = Db::getInstance()->ExecuteS('
                    SELECT `id_country`
                    FROM `'._DB_PREFIX_.'address`
                    WHERE `id_customer` = '.(int) $idCustomer.'
                    AND `deleted` = 0'
                );

                if (is_array($countries) && count($countries)) {
                    foreach ($countries as $country) {
                        $idCartRule = (bool)Db::getInstance()->getValue('
                            SELECT crc.id_cart_rule
                            FROM '._DB_PREFIX_.'cart_rule_country crc
                            WHERE crc.id_cart_rule = '.(int) $cartRule['id_cart_rule'].'
                            AND crc.id_country = '.(int) $country['id_country']);
                        if ($idCartRule) {
                            $result[] = $resultBak[$key];
                            break;
                        }
                    }
                }
            } else {
                $result[] = $resultBak[$key];
            }
        }

        if (!$countryRestriction) {
            $result = $resultBak;
        }

        return $result;
    }

    public static function getCustomerHighlightedDiscounts(
        $languageId,
        $customerId,
        Cart $cart
    )
    {
       return self::getCustomerCartRules(
           $languageId,
           $customerId,
           $active = true,
           $includeGeneric = true,
           $inStock = true,
           $cart,
           $freeShippingOnly = false,
           $highlightOnly = true
       );
    }


    /**
     * Check if the CartRule has been used by the given Customer
     *
     * @param int $idCustomer Customer ID
     *
     * @return bool Indicates if the CartRule has been used by a Customer
     *              The Cart must have been converted into an Order, otherwise it doesn't count
     */
    public function usedByCustomer($idCustomer)
    {
        return (bool)Db::getInstance()->getValue('
		SELECT id_cart_rule
		FROM `'._DB_PREFIX_.'order_cart_rule` ocr
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON ocr.`id_order` = o.`id_order`
		WHERE ocr.`id_cart_rule` = '.(int) $this->id.'
		AND o.`id_customer` = '.(int) $idCustomer);
    }

    /**
     * Check if the CartRule exists
     *
     * @param string $name CartRule name
     *
     * @return bool Indicates whether the CartRule can be found
     */
    public static function cartRuleExists($name)
    {
        if (!CartRule::isFeatureActive()) {
            return false;
        }

        return (bool) Db::getInstance()->getValue('
		SELECT `id_cart_rule`
		FROM `'._DB_PREFIX_.'cart_rule`
		WHERE `code` = \''.pSQL($name).'\'');
    }

    /**
     * Delete CartRules by Customer ID
     *
     * @param int $idCustomer Customer ID
     *
     * @return bool Indicates if the CartRules were successfully deleted
     */
    public static function deleteByIdCustomer($idCustomer)
    {
        $return = true;
        $cartRules = new PrestaShopCollection('CartRule');
        $cartRules->where('id_customer', '=', $idCustomer);
        foreach ($cartRules as $cartRule) {
            $return &= $cartRule->delete();
        }

        return $return;
    }

    /**
     *
     *
     * @return array
     */
    public function getProductRuleGroups()
    {
        if (!Validate::isLoadedObject($this) || $this->product_restriction == 0) {
            return array();
        }

        $productRuleGroups = array();
        $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'cart_rule_product_rule_group WHERE id_cart_rule = '.(int) $this->id);
        foreach ($result as $row) {
            if (!isset($productRuleGroups[$row['id_product_rule_group']])) {
                $productRuleGroups[$row['id_product_rule_group']] = array('id_product_rule_group' => $row['id_product_rule_group'], 'quantity' => $row['quantity']);
            }
            $productRuleGroups[$row['id_product_rule_group']]['product_rules'] = $this->getProductRules($row['id_product_rule_group']);
        }

        return $productRuleGroups;
    }

    /**
     * @param $idProductRuleGroup
     *
     * @return array ('type' => ? , 'values' => ?)
     */
    public function getProductRules($idProductRuleGroup)
    {
        if (!Validate::isLoadedObject($this) || $this->product_restriction == 0) {
            return array();
        }

        $productRules = array();
        $results = Db::getInstance()->executeS('
		SELECT *
		FROM '._DB_PREFIX_.'cart_rule_product_rule pr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_product_rule_value prv ON pr.id_product_rule = prv.id_product_rule
		WHERE pr.id_product_rule_group = '.(int) $idProductRuleGroup);
        foreach ($results as $row) {
            if (!isset($productRules[$row['id_product_rule']])) {
                $productRules[$row['id_product_rule']] = array('type' => $row['type'], 'values' => array());
            }
            $productRules[$row['id_product_rule']]['values'][] = $row['id_item'];
        }
        return $productRules;
    }

    /**
     * Check if this CartRule can be applied
     *
     * @param Context $context       Context instance
     * @param bool    $alreadyInCart Check if the voucher is already on the cart
     * @param bool    $displayError  Display error
     *
     * @return bool|mixed|string
     */
    public function checkValidity(Context $context, $alreadyInCart = false, $displayError = true, $checkCarrier = true)
    {
        if (!CartRule::isFeatureActive()) {
            return false;
        }

        if (!$this->active) {
            return (!$displayError) ? false : Tools::displayError('This voucher is disabled');
        }
        if (!$this->quantity) {
            return (!$displayError) ? false : Tools::displayError('This voucher has already been used');
        }
        if (strtotime($this->date_from) > time()) {
            return (!$displayError) ? false : Tools::displayError('This voucher is not valid yet');
        }
        if (strtotime($this->date_to) < time()) {
            return (!$displayError) ? false : Tools::displayError('This voucher has expired');
        }

        if ($context->cart->id_customer) {
            $quantityUsed = Db::getInstance()->getValue('
			SELECT count(*)
			FROM '._DB_PREFIX_.'orders o
			LEFT JOIN '._DB_PREFIX_.'order_cart_rule od ON o.id_order = od.id_order
			WHERE o.id_customer = '.$context->cart->id_customer.'
			AND od.id_cart_rule = '.(int) $this->id.'
			AND '.(int) Configuration::get('PS_OS_ERROR').' != o.current_state
			');
            if ($quantityUsed + 1 > $this->quantity_per_user) {
                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher anymore (usage limit reached)');
            }
        }

        // Get an intersection of the customer groups and the cart rule groups (if the customer is not logged in, the default group is Visitors)
        if ($this->group_restriction) {
            $idCartRule = (int) Db::getInstance()->getValue('
			SELECT crg.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_group crg
			WHERE crg.id_cart_rule = '.(int) $this->id.'
			AND crg.id_group '.($context->cart->id_customer ? 'IN (SELECT cg.id_group FROM '._DB_PREFIX_.'customer_group cg WHERE cg.id_customer = '.(int) $context->cart->id_customer.')' : '= '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP')));
            if (!$idCartRule) {
                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher');
            }
        }

        // Check if the customer delivery address is usable with the cart rule
        if ($this->country_restriction) {
            if (!$context->cart->id_address_delivery) {
                return (!$displayError) ? false : Tools::displayError('You must choose a delivery address before applying this voucher to your order');
            }
            $idCartRule = (int) Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_country crc
			WHERE crc.id_cart_rule = '.(int) $this->id.'
			AND crc.id_country = (SELECT a.id_country FROM '._DB_PREFIX_.'address a WHERE a.id_address = '.(int) $context->cart->id_address_delivery.' LIMIT 1)');
            if (!$idCartRule) {
                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher in your country of delivery');
            }
        }

        // Check if the carrier chosen by the customer is usable with the cart rule
        if ($this->carrier_restriction && $checkCarrier) {
            if (!$context->cart->id_carrier) {
                return (!$displayError) ? false : Tools::displayError('You must choose a carrier before applying this voucher to your order');
            }
            $idCartRule = (int)Db::getInstance()->getValue('
			SELECT crc.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_carrier crc
			INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
			WHERE crc.id_cart_rule = '.(int) $this->id.'
			AND c.id_carrier = '.(int) $context->cart->id_carrier);
            if (!$idCartRule) {
                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with this carrier');
            }
        }

        if ($this->reduction_exclude_special) {
            $products = $context->cart->getProducts();
            $isOk = false;
            foreach ($products as $product) {
                if (!$product['reduction_applies']) {
                    $isOk = true;
                    break;
                }
            }
            if (!$isOk) {
                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher on reduced products');
            }
        }

        // Check if the cart rules appliy to the shop browsed by the customer
        if ($this->shop_restriction && $context->shop->id && Shop::isFeatureActive()) {
            $idCartRule = (int) Db::getInstance()->getValue('
			SELECT crs.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule_shop crs
			WHERE crs.id_cart_rule = '.(int) $this->id.'
			AND crs.id_shop = '.(int) $context->shop->id);
            if (!$idCartRule) {
                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher');
            }
        }

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction) {
            $r = $this->checkProductRestrictions($context, false, $displayError, $alreadyInCart);
            if ($r !== false && $displayError) {
                return $r;
            } elseif (!$r && !$displayError) {
                return false;
            }
        }

        // Check if the cart rule is only usable by a specific customer, and if the current customer is the right one
        if ($this->id_customer && $context->cart->id_customer != $this->id_customer) {
            if (!Context::getContext()->customer->isLogged()) {
                return (!$displayError) ? false : (Tools::displayError('You cannot use this voucher').' - '.Tools::displayError('Please log in first'));
            }
            return (!$displayError) ? false : Tools::displayError('You cannot use this voucher');
        }

        if ($this->minimum_amount && $checkCarrier) {
            // Minimum amount is converted to the contextual currency
            $minimumAmount = $this->minimum_amount;
            if ($this->minimum_amount_currency != Context::getContext()->currency->id) {
                $minimumAmount = Tools::convertPriceFull($minimumAmount, new Currency($this->minimum_amount_currency), Context::getContext()->currency);
            }

            $cartTotal = $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_PRODUCTS);
            if ($this->minimum_amount_shipping) {
                $cartTotal += $context->cart->getOrderTotal($this->minimum_amount_tax, Cart::ONLY_SHIPPING);
            }
            $products = $context->cart->getProducts();
            $cartRules = $context->cart->getCartRules();

            foreach ($cartRules as &$cartRule) {
                if ($cartRule['gift_product']) {
                    foreach ($products as $key => &$product) {
                        if (empty($product['gift']) && $product['id_product'] == $cartRule['gift_product'] && $product['id_product_attribute'] == $cartRule['gift_product_attribute']) {
                            $cartTotal = Tools::ps_round($cartTotal - $product[$this->minimum_amount_tax ? 'price_wt' : 'price'], (int) $context->currency->decimals * _PS_PRICE_COMPUTE_PRECISION_);
                        }
                    }
                }
            }

            if ($cartTotal < $minimumAmount) {
                return (!$displayError) ? false : Tools::displayError('You have not reached the minimum amount required to use this voucher');
            }
        }

        /* This loop checks:
            - if the voucher is already in the cart
            - if a non compatible voucher is in the cart
            - if there are products in the cart (gifts excluded)
            Important note: this MUST be the last check, because if the tested cart rule has priority over a non combinable one in the cart, we will switch them
        */
        $nbProducts = Cart::getNbProducts($context->cart->id);
        $otherCartRules = array();
        if ($checkCarrier) {
            $otherCartRules = $context->cart->getCartRules();
        }
        if (count($otherCartRules)) {
            foreach ($otherCartRules as $otherCartRule) {
                if ($otherCartRule['id_cart_rule'] == $this->id && !$alreadyInCart) {
                    return (!$displayError) ? false : Tools::displayError('This voucher is already in your cart');
                }
                if ($otherCartRule['gift_product']) {
                    --$nbProducts;
                }

                if ($this->cart_rule_restriction && $otherCartRule['cart_rule_restriction'] && $otherCartRule['id_cart_rule'] != $this->id) {
                    $combinable = Db::getInstance()->getValue('
					SELECT id_cart_rule_1
					FROM '._DB_PREFIX_.'cart_rule_combination
					WHERE (id_cart_rule_1 = '.(int) $this->id.' AND id_cart_rule_2 = '.(int) $otherCartRule['id_cart_rule'].')
					OR (id_cart_rule_2 = '.(int) $this->id.' AND id_cart_rule_1 = '.(int) $otherCartRule['id_cart_rule'].')');
                    if (!$combinable) {
                        $cartRule = new CartRule((int) $otherCartRule['id_cart_rule'], $context->cart->id_lang);
                        // The cart rules are not combinable and the cart rule currently in the cart has priority over the one tested
                        if ($cartRule->priority <= $this->priority) {
                            return (!$displayError) ? false : Tools::displayError('This voucher is not combinable with an other voucher already in your cart:').' '.$cartRule->name;
                        }
                        // But if the cart rule that is tested has priority over the one in the cart, we remove the one in the cart and keep this new one
                        else {
                            $context->cart->removeCartRule($cartRule->id);
                        }
                    }
                }
            }
        }

        if (!$nbProducts) {
            return (!$displayError) ? false : Tools::displayError('Cart is empty');
        }

        if (!$displayError) {
            return true;
        }
    }

    protected function checkProductRestrictions(Context $context, $returnProducts = false, $displayError = true, $alreadyInCart = false)
    {
        $selectedProducts = array();

        // Check if the products chosen by the customer are usable with the cart rule
        if ($this->product_restriction) {
            $productRuleGroups = $this->getProductRuleGroups();
            foreach ($productRuleGroups as $idProductRuleGroup => $productRuleGroup) {
                $eligibleProductsList = array();
                if (isset($context->cart) && is_object($context->cart) && is_array($products = $context->cart->getProducts())) {
                    foreach ($products as $product) {
                        $eligibleProductsList[] = (int) $product['id_product'].'-'.(int) $product['id_product_attribute'];
                    }
                }
                if (!count($eligibleProductsList)) {
                    return (!$displayError) ? false : Tools::displayError('You cannot use this voucher in an empty cart');
                }

                $productRules = $this->getProductRules($idProductRuleGroup);
                foreach ($productRules as $productRule) {
                    switch ($productRule['type']) {
                        case 'attributes':
                            $cartAttributes = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, pac.`id_attribute`, cp.`id_product_attribute`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON cp.id_product_attribute = pac.id_product_attribute
							WHERE cp.`id_cart` = '.(int) $context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')
							AND cp.id_product_attribute > 0');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartAttributes as $cartAttribute) {
                                if (in_array($cartAttribute['id_attribute'], $productRule['values'])) {
                                    $countMatchingProducts += $cartAttribute['quantity'];
                                    if ($alreadyInCart && $this->gift_product == $cartAttribute['id_product']
                                        && $this->gift_product_attribute == $cartAttribute['id_product_attribute']) {
                                        --$countMatchingProducts;
                                    }
                                    $matchingProductsList[] = $cartAttribute['id_product'].'-'.$cartAttribute['id_product_attribute'];
                                }
                            }
                            if ($countMatchingProducts < $productRuleGroup['quantity']) {
                                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligibleProductsList = array_uintersect($eligibleProductsList, $matchingProductsList, array('self', 'cartRuleCompare'));
                            break;
                        case 'products':
                            $cartProducts = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`
							FROM `'._DB_PREFIX_.'cart_product` cp
							WHERE cp.`id_cart` = '.(int) $context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartProducts as $cartProduct) {
                                if (in_array($cartProduct['id_product'], $productRule['values'])) {
                                    $countMatchingProducts += $cartProduct['quantity'];
                                    if ($alreadyInCart && $this->gift_product == $cartProduct['id_product']) {
                                        --$countMatchingProducts;
                                    }
                                    $matchingProductsList[] = $cartProduct['id_product'].'-0';
                                }
                            }
                            if ($countMatchingProducts < $productRuleGroup['quantity']) {
                                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligibleProductsList = array_uintersect($eligibleProductsList, $matchingProductsList, array('self', 'cartRuleCompare'));
                            break;
                        case 'categories':
                            $cartCategories = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, cp.`id_product_attribute`, catp.`id_category`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'category_product` catp ON cp.id_product = catp.id_product
							WHERE cp.`id_cart` = '.(int) $context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')
							AND cp.`id_product` <> '.(int) $this->gift_product);
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartCategories as $cartCategory) {
                                if (in_array($cartCategory['id_category'], $productRule['values'])
                                    /**
                                     * We also check that the product is not already in the matching product list,
                                     * because there are doubles in the query results (when the product is in multiple categories)
                                     */
                                    && !in_array($cartCategory['id_product'].'-'.$cartCategory['id_product_attribute'], $matchingProductsList)) {
                                    $countMatchingProducts += $cartCategory['quantity'];
                                    $matchingProductsList[] = $cartCategory['id_product'].'-'.$cartCategory['id_product_attribute'];
                                }
                            }
                            if ($countMatchingProducts < $productRuleGroup['quantity']) {
                                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            // Attribute id is not important for this filter in the global list, so the ids are replaced by 0
                            foreach ($matchingProductsList as &$matchingProduct) {
                                $matchingProduct = preg_replace('/^([0-9]+)-[0-9]+$/', '$1-0', $matchingProduct);
                            }
                            $eligibleProductsList = array_uintersect($eligibleProductsList, $matchingProductsList, array('self', 'cartRuleCompare'));
                            break;
                        case 'manufacturers':
                            $cartManufacturers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_manufacturer`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int) $context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartManufacturers as $cartManufacturer) {
                                if (in_array($cartManufacturer['id_manufacturer'], $productRule['values'])) {
                                    $countMatchingProducts += $cartManufacturer['quantity'];
                                    $matchingProductsList[] = $cartManufacturer['id_product'].'-0';
                                }
                            }
                            if ($countMatchingProducts < $productRuleGroup['quantity']) {
                                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligibleProductsList = array_uintersect($eligibleProductsList, $matchingProductsList, array('self', 'cartRuleCompare'));
                            break;
                        case 'suppliers':
                            $cartSuppliers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_supplier`
							FROM `'._DB_PREFIX_.'cart_product` cp
							LEFT JOIN `'._DB_PREFIX_.'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = '.(int) $context->cart->id.'
							AND cp.`id_product` IN ('.implode(',', array_map('intval', $eligibleProductsList)).')');
                            $countMatchingProducts = 0;
                            $matchingProductsList = array();
                            foreach ($cartSuppliers as $cartSupplier) {
                                if (in_array($cartSupplier['id_supplier'], $productRule['values'])) {
                                    $countMatchingProducts += $cartSupplier['quantity'];
                                    $matchingProductsList[] = $cartSupplier['id_product'].'-0';
                                }
                            }
                            if ($countMatchingProducts < $productRuleGroup['quantity']) {
                                return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with these products');
                            }
                            $eligibleProductsList = array_uintersect($eligibleProductsList, $matchingProductsList, array('self', 'cartRuleCompare'));
                            break;
                    }

                    if (!count($eligibleProductsList)) {
                        return (!$displayError) ? false : Tools::displayError('You cannot use this voucher with these products');
                    }
                }
                $selectedProducts = array_merge($selectedProducts, $eligibleProductsList);
            }
        }

        if ($returnProducts) {
            return $selectedProducts;
        }

        return (!$displayError) ? true : false;
    }

    /**
     * CartRule compare function to use for array_uintersect
     *
     * @param array $a List A
     * @param array $b List B
     *
     * @return int 0 = same
     *             1 = different
     */
    protected static function cartRuleCompare($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        $asplit = explode('-', $a);
        $bsplit = explode('-', $b);
        if ($asplit[0] == $bsplit[0] && (!(int) $asplit[1] || !(int) $bsplit[1])) {
            return 0;
        }

        return 1;
    }

    /**
     * The reduction value is POSITIVE
     *
     * @param bool    $useTax   Apply taxes
     * @param Context $context  Context instance
     * @param null    $filter
     * @param null    $package
     * @param bool    $useCache Allow using cache to avoid multiple free gift using multishipping
     *
     * @return float|int|string
     */
    public function getContextualValue($useTax, Context $context = null, $filter = null, $package = null, $useCache = true)
    {
        if (!CartRule::isFeatureActive()) {
            return 0;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if (!$filter) {
            $filter = CartRule::FILTER_ACTION_ALL;
        }

        $allProducts = $context->cart->getProducts();
        $packageProducts = (is_null($package) ? $allProducts : $package['products']);

        $allCartRulesIds = $context->cart->getOrderedCartRulesIds();

        $cartAmountTi = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cartAmountTe = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $reductionValue = 0;

        $cacheId = 'getContextualValue_'.(int) $this->id.'_'.(int) $useTax.'_'.(int) $context->cart->id.'_'.(int) $filter;
        foreach ($packageProducts as $product) {
            $cacheId .= '_'.(int) $product['id_product'].'_'.(int) $product['id_product_attribute'].(isset($product['in_stock']) ? '_'.(int) $product['in_stock'] : '');
        }

        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        // Free shipping on selected carriers
        if ($this->free_shipping && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_SHIPPING))) {
            if (!$this->carrier_restriction) {
                $reductionValue += $context->cart->getOrderTotal($useTax, Cart::ONLY_SHIPPING, is_null($package) ? null : $package['products'], is_null($package) ? null : $package['id_carrier']);
            } else {
                $data = Db::getInstance()->executeS('
					SELECT crc.id_cart_rule, c.id_carrier
					FROM '._DB_PREFIX_.'cart_rule_carrier crc
					INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)
					WHERE crc.id_cart_rule = '.(int) $this->id.'
					AND c.id_carrier = '.(int) $context->cart->id_carrier);

                if ($data) {
                    foreach ($data as $cartRule) {
                        $reductionValue += $context->cart->getCarrierCost((int) $cartRule['id_carrier'], $useTax, $context->country);
                    }
                }
            }
        }

        if (in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_REDUCTION))) {
            // Discount (%) on the whole order
            if ($this->reduction_percent && $this->reduction_product == 0) {
                // Do not give a reduction on free products!
                $orderTotal = $context->cart->getOrderTotal($useTax, Cart::ONLY_PRODUCTS, $packageProducts);
                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cartRule) {
                    $orderTotal -= Tools::ps_round($cartRule['obj']->getContextualValue($useTax, $context, CartRule::FILTER_ACTION_GIFT, $package), _PS_PRICE_COMPUTE_PRECISION_);
                }

                // Remove products that are on special
                if ($this->reduction_exclude_special) {
                    foreach ($packageProducts as $product) {
                        if ($product['reduction_applies']) {
                            if ($useTax) {
                                $orderTotal -= Tools::ps_round($product['total_wt'], _PS_PRICE_COMPUTE_PRECISION_);
                            } else {
                                $orderTotal -= Tools::ps_round($product['total'], _PS_PRICE_COMPUTE_PRECISION_);
                            }
                        }
                    }
                }

                $reductionValue += $orderTotal * $this->reduction_percent / 100;
            }

            // Discount (%) on a specific product
            if ($this->reduction_percent && $this->reduction_product > 0) {
                foreach ($packageProducts as $product) {
                    if ($product['id_product'] == $this->reduction_product && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                        $reductionValue += ($useTax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
                    }
                }
            }

            // Discount (%) on the cheapest product
            if ($this->reduction_percent && $this->reduction_product == -1) {
                $minPrice = false;
                $cheapestProduct = null;
                foreach ($allProducts as $product) {
                    $price = $product['price'];
                    if ($useTax) {
                        // since later on we won't be able to know the product the cart rule was applied to,
                        // use average cart VAT for price_wt
                        $price *= (1 + $context->cart->getAverageProductsTaxRate());
                    }

                    if ($price > 0 && ($minPrice === false || $minPrice > $price) && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                        $minPrice = $price;
                        $cheapestProduct = $product['id_product'].'-'.$product['id_product_attribute'];
                    }
                }

                // Check if the cheapest product is in the package
                $inPackage = false;
                foreach ($packageProducts as $product) {
                    if ($product['id_product'].'-'.$product['id_product_attribute'] == $cheapestProduct || $product['id_product'].'-0' == $cheapestProduct) {
                        $inPackage = true;
                    }
                }
                if ($inPackage) {
                    $reductionValue += $minPrice * $this->reduction_percent / 100;
                }
            }

            // Discount (%) on the selection of products
            if ($this->reduction_percent && $this->reduction_product == -2) {
                $selectedProductsReduction = 0;
                $selectedProducts = $this->checkProductRestrictions($context, true);
                if (is_array($selectedProducts)) {
                    foreach ($packageProducts as $product) {
                        if (in_array($product['id_product'].'-'.$product['id_product_attribute'], $selectedProducts)
                            || in_array($product['id_product'].'-0', $selectedProducts)
                            && (($this->reduction_exclude_special && !$product['reduction_applies']) || !$this->reduction_exclude_special)) {
                            $price = $product['price'];
                            if ($useTax) {
                                $infos = Product::getTaxesInformations($product, $context);
                                $taxRate = $infos['rate'] / 100;
                                $price *= (1 + $taxRate);
                            }

                            $selectedProductsReduction += $price * $product['cart_quantity'];
                        }
                    }
                }
                $reductionValue += $selectedProductsReduction * $this->reduction_percent / 100;
            }

            // Discount (¤)
            if ((float) $this->reduction_amount > 0) {
                $prorata = 1;
                if (!is_null($package) && count($allProducts)) {
                    $totalProducts = $context->cart->getOrderTotal($useTax, Cart::ONLY_PRODUCTS);
                    if ($totalProducts) {
                        $prorata = $context->cart->getOrderTotal($useTax, Cart::ONLY_PRODUCTS, $package['products']) / $totalProducts;
                    }
                }

                $reductionAmount = $this->reduction_amount;
                // If we need to convert the voucher value to the cart currency
                if (isset($context->currency) && $this->reduction_currency != $context->currency->id) {
                    $voucherCurrency = new Currency($this->reduction_currency);

                    // First we convert the voucher value to the default currency
                    if ($reductionAmount == 0 || $voucherCurrency->conversion_rate == 0) {
                        $reductionAmount = 0;
                    } else {
                        $reductionAmount /= $voucherCurrency->conversion_rate;
                    }

                    // Then we convert the voucher value in the default currency into the cart currency
                    $reductionAmount *= $context->currency->conversion_rate;
                    $reductionAmount = Tools::ps_round($reductionAmount, _PS_PRICE_COMPUTE_PRECISION_);
                }

                // If it has the same tax application that you need, then it's the right value, whatever the product!
                if ($this->reduction_tax == $useTax) {
                    // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                    if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                        $cartAmount = $context->cart->getOrderTotal($useTax, Cart::ONLY_PRODUCTS);
                        $reductionAmount = min($reductionAmount, $cartAmount);
                    }
                    $reductionValue += $prorata * $reductionAmount;
                } else {
                    if ($this->reduction_product > 0) {
                        foreach ($context->cart->getProducts() as $product) {
                            if ($product['id_product'] == $this->reduction_product) {
                                $productPriceTi = $product['price_wt'];
                                $productPriceTe = $product['price'];
                                $productVatAmount = $productPriceTi - $productPriceTe;

                                if ($productVatAmount == 0 || $productPriceTe == 0) {
                                    $productVatRate = 0;
                                } else {
                                    $productVatRate = $productVatAmount / $productPriceTe;
                                }

                                if ($this->reduction_tax && !$useTax) {
                                    $reductionValue += $prorata * $reductionAmount / (1 + $productVatRate);
                                } elseif (!$this->reduction_tax && $useTax) {
                                    $reductionValue += $prorata * $reductionAmount * (1 + $productVatRate);
                                }
                            }
                        }
                    } elseif ($this->reduction_product == 0) {
                        // Discount (¤) on the whole order
                        $cartAmountTe = null;
                        $cartAmountTi = null;
                        $cartAverageVatRate = $context->cart->getAverageProductsTaxRate($cartAmountTe, $cartAmountTi);

                        // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                            $reductionAmount = min($reductionAmount, $this->reduction_tax ? $cartAmountTi : $cartAmountTe);
                        }

                        if ($this->reduction_tax && !$useTax) {
                            $reductionValue += $prorata * $reductionAmount / (1 + $cartAverageVatRate);
                        } elseif (!$this->reduction_tax && $useTax) {
                            $reductionValue += $prorata * $reductionAmount * (1 + $cartAverageVatRate);
                        }
                    }
                    /*
                     * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
                     * Please keep this code, so it won't be considered as a bug
                     * elseif ($this->reduction_product == -1)
                     * elseif ($this->reduction_product == -2)
                    */
                }

                // Take care of the other cart rules values if the filter allow it
                if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP) {
                    // Cart values
                    $cart = Context::getContext()->cart;

                    if (!Validate::isLoadedObject($cart)) {
                        $cart = new Cart();
                    }

                    $cartAverageVatRate = $cart->getAverageProductsTaxRate();
                    $currentCartAmount = $useTax ? $cartAmountTi : $cartAmountTe;

                    foreach ($allCartRulesIds as $currentCartRuleId) {
                        if ((int) $currentCartRuleId['id_cart_rule'] == (int) $this->id) {
                            break;
                        }

                        $previousCartRule = new CartRule((int) $currentCartRuleId['id_cart_rule']);
                        $previousReductionAmount = $previousCartRule->reduction_amount;

                        if ($previousCartRule->reduction_tax && !$useTax) {
                            $previousReductionAmount = $prorata * $previousReductionAmount / (1 + $cartAverageVatRate);
                        } elseif (!$previousCartRule->reduction_tax && $useTax) {
                            $previousReductionAmount = $prorata * $previousReductionAmount * (1 + $cartAverageVatRate);
                        }

                        $currentCartAmount = max($currentCartAmount - (float) $previousReductionAmount, 0);
                    }

                    $reductionValue = min($reductionValue, $currentCartAmount);
                }
            }
        }

        // Free gift
        if ((int) $this->gift_product && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT))) {
            $idAddress = (is_null($package) ? 0 : $package['id_address']);
            foreach ($packageProducts as $product) {
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int)$this->gift_product_attribute)) {
                    // The free gift coupon must be applied to one product only (needed for multi-shipping which manage multiple product lists)
                    if (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product])
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == $idAddress
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0
                        || $idAddress == 0
                        || !$useCache) {
                        $reductionValue += ($useTax ? $product['price_wt'] : $product['price']);
                        if ($useCache && (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product]) || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0)) {
                            CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] = $idAddress;
                        }
                        break;
                    }
                }
            }
        }

        Cache::store($cacheId, $reductionValue);

        return $reductionValue;
    }

    /**
     * Make sure caches are empty
     * Must be called before calling multiple time getContextualValue()
     */
    public static function cleanCache()
    {
        self::$only_one_gift = array();
    }

    /**
     * Get CartRule combinations
     *
     * @param int    $offset Offset
     * @param int    $limit Limit
     * @param string $search Search query
     *
     * @return array CartRule search results
     */
    protected function getCartRuleCombinations($offset = null, $limit = null, $search = '')
    {
        $array = array();
        if ($offset !== null && $limit !== null) {
            $sqlLimit = ' LIMIT '.(int) $offset.', '.(int) ($limit+1);
        } else {
            $sqlLimit = '';
        }

        $array['selected'] = Db::getInstance()->executeS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')
		WHERE cr.id_cart_rule != '.(int) $this->id.($search ? ' AND crl.name LIKE "%'.pSQL($search).'%"' : '').'
		AND (
			cr.cart_rule_restriction = 0
			OR EXISTS (
				SELECT 1
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE cr.id_cart_rule = '._DB_PREFIX_.'cart_rule_combination.id_cart_rule_1 AND '.(int) $this->id.' = id_cart_rule_2
			)
			OR EXISTS (
				SELECT 1
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE cr.id_cart_rule = '._DB_PREFIX_.'cart_rule_combination.id_cart_rule_2 AND '.(int) $this->id.' = id_cart_rule_1
			)
		) ORDER BY cr.id_cart_rule'.$sqlLimit);

        $array['unselected'] = Db::getInstance()->executeS('
		SELECT cr.*, crl.*, 1 as selected
		FROM '._DB_PREFIX_.'cart_rule cr
		INNER JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int) Context::getContext()->language->id.')
		LEFT JOIN '._DB_PREFIX_.'cart_rule_combination crc1 ON (cr.id_cart_rule = crc1.id_cart_rule_1 AND crc1.id_cart_rule_2 = '.(int) $this->id.')
		LEFT JOIN '._DB_PREFIX_.'cart_rule_combination crc2 ON (cr.id_cart_rule = crc2.id_cart_rule_2 AND crc2.id_cart_rule_1 = '.(int) $this->id.')
		WHERE cr.cart_rule_restriction = 1
		AND cr.id_cart_rule != '.(int) $this->id.($search ? ' AND crl.name LIKE "%'.pSQL($search).'%"' : '').'
		AND crc1.id_cart_rule_1 IS NULL
		AND crc2.id_cart_rule_1 IS NULL  ORDER BY cr.id_cart_rule'.$sqlLimit);

        return $array;
    }

    /**
     * Get associated restrictions
     *
     * @param string $type               Restriction type
     *                                   Can be one of the following:
     *                                   - country
     *                                   - carrier
     *                                   - group
     *                                   - cart_rule
     *                                   - shop
     * @param bool   $activeOnly         Only return active restrictions
     * @param bool   $i18n               Join with associated language table
     * @param int    $offset             Search offset
     * @param int    $limit              Search results limit
     * @param string $searchCartRuleName CartRule name to search for
     *
     * @return array|bool Array with DB rows of requested type
     * @throws PrestaShopDatabaseException
     */
    public function getAssociatedRestrictions($type, $activeOnly, $i18n, $offset = null, $limit = null, $searchCartRuleName = '')
    {
        $array = array('selected' => array(), 'unselected' => array());

        if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop'))) {
            return false;
        }

        $shopList = '';
        if ($type == 'shop') {
            $shops = Context::getContext()->employee->getAssociatedShops();
            if (count($shops)) {
                $shopList = ' AND t.id_shop IN ('.implode(array_map('intval', $shops), ',').') ';
            }
        }

        if ($offset !== null && $limit !== null) {
            $sqlLimit = ' LIMIT '.(int) $offset.', '.(int) ($limit+1);
        } else {
            $sqlLimit = '';
        }

        if (!Validate::isLoadedObject($this) || $this->{$type.'_restriction'} == 0) {
            $array['selected'] = Db::getInstance()->executeS('
			SELECT t.*'.($i18n ? ', tl.*' : '').', 1 as selected
			FROM `'._DB_PREFIX_.$type.'` t
			'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int) Context::getContext()->language->id.')' : '').'
			WHERE 1
			'.($activeOnly ? 'AND t.active = 1' : '').'
			'.(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').'
			'.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int) $this->id : '').
            $shopList.
            (in_array($type, array('carrier', 'shop')) ? ' ORDER BY t.name ASC ' : '').
            (in_array($type, array('country', 'group', 'cart_rule')) && $i18n ? ' ORDER BY tl.name ASC ' : '').
            $sqlLimit);
        } else {
            if ($type == 'cart_rule') {
                $array = $this->getCartRuleCombinations($offset, $limit, $searchCartRuleName);
            } else {
                $resource = Db::getInstance()->query('
				SELECT t.*'.($i18n ? ', tl.*' : '').', IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
				FROM `'._DB_PREFIX_.$type.'` t
				'.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
				LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int) $this->id.') crt ON t.id_'.($type == 'carrier' ? 'reference' : $type).' = crt.id_'.$type.'
				WHERE 1 '.($activeOnly ? ' AND t.active = 1' : '').
                $shopList
                .(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').
                (in_array($type, array('carrier', 'shop')) ? ' ORDER BY t.name ASC ' : '').
                (in_array($type, array('country', 'group', 'cart_rule')) && $i18n ? ' ORDER BY tl.name ASC ' : '').
                $sqlLimit,
                false);
                while ($row = Db::getInstance()->nextRow($resource)) {
                    $array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
                }
            }
        }

        return $array;
    }

    /**
     * Automatically add this CartRule to the Cart
     *
     * @param Context|null $context Context instance
     *
     * @return void
     */
    public static function autoAddToCart(Context $context = null)
    {
        if ($context === null) {
            $context = Context::getContext();
        }
        if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart)) {
            return;
        }

        $sql = '
		SELECT SQL_NO_CACHE cr.*
		FROM '._DB_PREFIX_.'cart_rule cr
		LEFT JOIN '._DB_PREFIX_.'cart_rule_shop crs ON cr.id_cart_rule = crs.id_cart_rule
		'.(!$context->customer->id && Group::isFeatureActive() ? ' LEFT JOIN '._DB_PREFIX_.'cart_rule_group crg ON cr.id_cart_rule = crg.id_cart_rule' : '').'
		LEFT JOIN '._DB_PREFIX_.'cart_rule_carrier crca ON cr.id_cart_rule = crca.id_cart_rule
		'.($context->cart->id_carrier ? 'LEFT JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crca.id_carrier AND c.deleted = 0)' : '').'
		LEFT JOIN '._DB_PREFIX_.'cart_rule_country crco ON cr.id_cart_rule = crco.id_cart_rule
		WHERE cr.active = 1
		AND cr.code = ""
		AND cr.quantity > 0
		AND cr.date_from < "'.date('Y-m-d H:i:s').'"
		AND cr.date_to > "'.date('Y-m-d H:i:s').'"
		AND (
			cr.id_customer = 0
			'.($context->customer->id ? 'OR cr.id_customer = '.(int) $context->cart->id_customer : '').'
		)
		AND (
			cr.`carrier_restriction` = 0
			'.($context->cart->id_carrier ? 'OR c.id_carrier = '.(int) $context->cart->id_carrier : '').'
		)
		AND (
			cr.`shop_restriction` = 0
			'.((Shop::isFeatureActive() && $context->shop->id) ? 'OR crs.id_shop = '.(int) $context->shop->id : '').'
		)
		AND (
			cr.`group_restriction` = 0
			'.($context->customer->id ? 'OR EXISTS (
				SELECT 1
				FROM `'._DB_PREFIX_.'customer_group` cg
				INNER JOIN `'._DB_PREFIX_.'cart_rule_group` crg ON cg.id_group = crg.id_group
				WHERE cr.`id_cart_rule` = crg.`id_cart_rule`
				AND cg.`id_customer` = '.(int) $context->customer->id.'
				LIMIT 1
			)' : (Group::isFeatureActive() ? 'OR crg.`id_group` = '.(int) Configuration::get('PS_UNIDENTIFIED_GROUP') : '')).'
		)
		AND (
			cr.`reduction_product` <= 0
			OR EXISTS (
				SELECT 1
				FROM `'._DB_PREFIX_.'cart_product`
				WHERE `'._DB_PREFIX_.'cart_product`.`id_product` = cr.`reduction_product` AND `id_cart` = '.(int) $context->cart->id.'
			)
		)
		AND NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'cart_cart_rule WHERE cr.id_cart_rule = '._DB_PREFIX_.'cart_cart_rule.id_cart_rule
																			AND id_cart = '.(int) $context->cart->id.')
		ORDER BY priority';
        $result = Db::getInstance()->executeS($sql, true, false);
        if ($result) {
            $cartRules = ObjectModel::hydrateCollection('CartRule', $result);
            if ($cartRules) {
                foreach ($cartRules as $cartRule) {
                    /** @var CartRule $cartRule */
                    if ($cartRule->checkValidity($context, false, false)) {
                        $context->cart->addCartRule($cartRule->id);
                    }
                }
            }
        }
    }

    /**
     * Automatically remove this CartRule from the Cart
     *
     * @param Context|null $context Context instance
     *
     * @return array Error messages
     */
    public static function autoRemoveFromCart(Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        if (!CartRule::isFeatureActive() || !Validate::isLoadedObject($context->cart)) {
            return array();
        }

        static $errors = array();
        foreach ($context->cart->getCartRules() as $cartRule) {
            if ($error = $cartRule['obj']->checkValidity($context, true)) {
                $context->cart->removeCartRule($cartRule['obj']->id);
                $context->cart->update();
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * Check if the CartRule feature is active
     * It becomes active after adding the first CartRule to the store
     *
     * @return bool Indicates whether the CartRule feature is active
     */
    public static function isFeatureActive()
    {
        static $isFeatureActive = null;
        if ($isFeatureActive === null) {
            $isFeatureActive = (bool) Configuration::get('PS_CART_RULE_FEATURE_ACTIVE');
        }
        return $isFeatureActive;
    }

    /**
     * CartRule cleanup
     * When an entity associated to a product rule
     * (product, category, attribute, supplier, manufacturer...)
     * is deleted, the product rules must be updated
     *
     * @param string $type Entity type
     *                     Can be one of the following:
     *                     - products
     *                     - categories
     *                     - attributes
     *                     - manufacturers
     *                     - suppliers
     * @param array  $list Entities
     *
     * @return bool Indicates whether the cleanup was successful
     */
    public static function cleanProductRuleIntegrity($type, $list)
    {
        //

        // Type must be available in the 'type' enum of the table cart_rule_product_rule
        if (!in_array($type, array('products', 'categories', 'attributes', 'manufacturers', 'suppliers'))) {
            return false;
        }

        // This check must not be removed because this var is used a few lines below
        $list = (is_array($list) ? implode(',', array_map('intval', $list)) : (int) $list);
        if (!preg_match('/^[0-9,]+$/', $list)) {
            return false;
        }

        // Delete associated restrictions on cart rules
        Db::getInstance()->execute('
		DELETE crprv
		FROM `'._DB_PREFIX_.'cart_rule_product_rule` crpr
		LEFT JOIN `'._DB_PREFIX_.'cart_rule_product_rule_value` crprv ON crpr.`id_product_rule` = crprv.`id_product_rule`
		WHERE crpr.`type` = "'.pSQL($type).'"
		AND crprv.`id_item` IN ('.$list.')'); // $list is checked a few lines above

        // Delete the product rules that does not have any values
        if (Db::getInstance()->Affected_Rows() > 0) {
            Db::getInstance()->delete('cart_rule_product_rule', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule_value`
																							WHERE `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule` = `'._DB_PREFIX_.'cart_rule_product_rule_value`.`id_product_rule`)');
        }
        // If the product rules were the only conditions of a product rule group, delete the product rule group
        if (Db::getInstance()->Affected_Rows() > 0) {
            Db::getInstance()->delete('cart_rule_product_rule_group', 'NOT EXISTS (SELECT 1 FROM `'._DB_PREFIX_.'cart_rule_product_rule`
																						WHERE `'._DB_PREFIX_.'cart_rule_product_rule`.`id_product_rule_group` = `'._DB_PREFIX_.'cart_rule_product_rule_group`.`id_product_rule_group`)');
        }

        // If the product rule group were the only restrictions of a cart rule, update de cart rule restriction cache
        if (Db::getInstance()->Affected_Rows() > 0) {
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'cart_rule` cr
				LEFT JOIN `'._DB_PREFIX_.'cart_rule_product_rule_group` crprg ON cr.id_cart_rule = crprg.id_cart_rule
				SET product_restriction = IF(crprg.id_product_rule_group IS NULL, 0, 1)');
        }

        return true;
    }

    /**
     * Get CartRules by voucher code
     *
     * @param string $name     Name of voucher code
     * @param int    $idLang   Language ID
     * @param bool   $extended Also search by voucher name
     *
     * @return array Result from database
     */
    public static function getCartsRuleByCode($name, $idLang, $extended = false)
    {
        $sqlBase = 'SELECT cr.*, crl.*
						FROM '._DB_PREFIX_.'cart_rule cr
						LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int) $idLang.')';
        if ($extended) {
            return Db::getInstance()->executeS('('.$sqlBase.' WHERE code LIKE \'%'.pSQL($name).'%\') UNION ('.$sqlBase.' WHERE name LIKE \'%'.pSQL($name).'%\')');
        } else {
            return Db::getInstance()->executeS($sqlBase.' WHERE code LIKE \'%'.pSQL($name).'%\'');
        }
    }
}
