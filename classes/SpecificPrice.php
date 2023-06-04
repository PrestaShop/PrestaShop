<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update\SpecificPricePriorityUpdater;

class SpecificPriceCore extends ObjectModel
{
    public const ORDER_DEFAULT_FROM_QUANTITY = 1;
    public const ORDER_DEFAULT_DATE = '0000-00-00 00:00:00';

    public $id_product;
    public $id_specific_price_rule = 0;
    public $id_cart = 0;
    public $id_product_attribute;
    public $id_shop;
    public $id_shop_group;
    public $id_currency;
    public $id_country;
    public $id_group;
    public $id_customer;
    public $price;
    public $from_quantity;
    public $reduction;
    public $reduction_tax = 1;
    public $reduction_type;
    public $from;
    public $to;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'specific_price',
        'primary' => 'id_specific_price',
        'fields' => [
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_product_attribute' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_currency' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_specific_price_rule' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_country' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true],
            'from_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true],
            'reduction' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true],
            'reduction_tax' => ['type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true],
            'reduction_type' => ['type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true],
            'from' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
            'to' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true],
        ],
    ];

    protected $webserviceParameters = [
        'objectsNodeName' => 'specific_prices',
        'objectNodeName' => 'specific_price',
        'fields' => [
            'id_shop_group' => ['xlink_resource' => 'shop_groups'],
            'id_shop' => ['xlink_resource' => 'shops', 'required' => true],
            'id_cart' => ['xlink_resource' => 'carts', 'required' => true],
            'id_product' => ['xlink_resource' => 'products', 'required' => true],
            'id_product_attribute' => ['xlink_resource' => 'product_attributes'],
            'id_currency' => ['xlink_resource' => 'currencies', 'required' => true],
            'id_country' => ['xlink_resource' => 'countries', 'required' => true],
            'id_group' => ['xlink_resource' => 'groups', 'required' => true],
            'id_customer' => ['xlink_resource' => 'customers', 'required' => true],
        ],
    ];

    /**
     * Local cache for getSpecificPrice function results.
     *
     * @var array
     */
    protected static $_specificPriceCache = [];

    /**
     * Local cache which stores if a product could have an associated specific price.
     *
     * @var array
     */
    protected static $_couldHaveSpecificPriceCache = [];

    /**
     * Store if the specific_price table contains any global rules in the productId columns
     * i.e. if there is a product_id == 0 somewhere in the specific_price table.
     *
     * @var bool|null
     */
    protected static $_hasGlobalProductRules = null;

    /**
     * Local cache for the filterOutField function. It stores the different existing values in the specific_price table
     * for a given column name.
     *
     * @var array
     */
    protected static $_filterOutCache = [];

    /**
     * Local cache for getPriority function.
     *
     * @var array
     */
    protected static $_cache_priorities = [];

    /**
     * Local cache which stores if a given column name could have a value != 0 in the specific_price table
     * i.e. if columnName != 0 somewhere in the specific_price table.
     *
     * @var array
     */
    protected static $_no_specific_values = [];

    protected static $psQtyDiscountOnCombination = null;

    public static function resetStaticCache()
    {
        parent::resetStaticCache();
        static::flushCache();
    }

    /**
     * Flush local cache.
     */
    public static function flushCache()
    {
        self::$_specificPriceCache = [];
        self::$_couldHaveSpecificPriceCache = [];
        self::$_hasGlobalProductRules = null;
        self::$_filterOutCache = [];
        self::$_cache_priorities = [];
        self::$_no_specific_values = [];
        self::$psQtyDiscountOnCombination = null;
        Product::flushPriceCache();
    }

    public function add($autodate = true, $nullValues = false)
    {
        if (parent::add($autodate, $nullValues)) {
            // Flush cache when we adding a new specific price
            $this->flushCache();
            // Set cache of feature detachable to true
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '1');

            return true;
        }

        return false;
    }

    public function update($null_values = false)
    {
        if (parent::update($null_values)) {
            // Flush cache when we updating a new specific price
            $this->flushCache();

            return true;
        }

        return false;
    }

    public function delete()
    {
        if (parent::delete()) {
            // Flush cache when we deletind a new specific price
            $this->flushCache();
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed($this->def['table']));

            return true;
        }

        return false;
    }

    public static function getByProductId($id_product, $id_product_attribute = false, $id_cart = false, $id_price_rule = null)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'specific_price`
			WHERE `id_product` = ' . (int) $id_product .
            ($id_product_attribute ? ' AND id_product_attribute = ' . (int) $id_product_attribute : '') . '
			AND id_cart = ' . (int) $id_cart .
            ($id_price_rule !== null ? ' AND id_specific_price_rule = ' . (int) $id_price_rule : ''));
    }

    public static function deleteByIdCart($id_cart, $id_product = false, $id_product_attribute = false)
    {
        return Db::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'specific_price`
			WHERE id_cart=' . (int) $id_cart .
            ($id_product ? ' AND id_product=' . (int) $id_product . ' AND id_product_attribute=' . (int) $id_product_attribute : ''));
    }

    public static function getIdsByProductId($id_product, $id_product_attribute = false, $id_cart = 0)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_specific_price`
            FROM `' . _DB_PREFIX_ . 'specific_price`
            WHERE `id_product` = ' . (int) $id_product .
            ($id_product_attribute !== false ? ' AND id_product_attribute = ' . (int) $id_product_attribute : '') . '
            AND id_cart = ' . (int) $id_cart);
    }

    /**
     * score generation for quantity discount.
     */
    protected static function _getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer)
    {
        $select = '(';

        $priority = SpecificPrice::getPriority($id_product);
        foreach (array_reverse($priority) as $k => $field) {
            if (!empty($field)) {
                $select .= ' IF (`' . bqSQL($field) . '` = ' . (int) $$field . ', ' . 2 ** ($k + 1) . ', 0) + ';
            }
        }

        return rtrim($select, ' +') . ') AS `score`';
    }

    public static function getPriority($id_product)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return explode(';', Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES'));
        }

        if (!isset(self::$_cache_priorities[(int) $id_product])) {
            self::$_cache_priorities[(int) $id_product] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `priority`, `id_specific_price_priority`
				FROM `' . _DB_PREFIX_ . 'specific_price_priority`
				WHERE `id_product` = ' . (int) $id_product . '
				ORDER BY `id_specific_price_priority` DESC
			');
        }

        $priority = self::$_cache_priorities[(int) $id_product];

        if (!$priority) {
            $priority = Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES');
        }
        $priority = 'id_customer;' . $priority;

        return explode(';', $priority);
    }

    /**
     * Remove or add a field value to a query if values are present in the database (cache friendly).
     *
     * @param string $field_name
     * @param int $field_value
     * @param int $threshold
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     */
    protected static function filterOutField($field_name, $field_value, $threshold = 1000)
    {
        $name = Db::getInstance()->escape($field_name, false, true);
        $query_extra = 'AND `' . $name . '` = 0 ';
        if ($field_value == 0 || array_key_exists($field_name, self::$_no_specific_values)) {
            return $query_extra;
        }
        $key_cache = __FUNCTION__ . '-' . $field_name . '-' . $threshold;
        $specific_list = [];
        if (!array_key_exists($key_cache, self::$_filterOutCache)) {
            // Check if a specific price with this key exists
            $query = 'SELECT 1 FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `' . $name . '` != 0';
            $has_product_specific_price = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
            if ($has_product_specific_price == 0) {
                self::$_no_specific_values[$field_name] = true;

                return $query_extra;
            }
            // Fetch the approximate count of specific price. explain can be 100x faster than count.
            $query_count = 'EXPLAIN SELECT COUNT(DISTINCT `' . $name . '`) FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `' . $name . '` != 0';
            $specific_count_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query_count);
            $specific_count = $specific_count_result['rows'];

            if ($specific_count < $threshold) {
                $query = 'SELECT DISTINCT `' . $name . '` FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `' . $name . '` != 0';
                $tmp_specific_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                foreach ($tmp_specific_list as $value) {
                    $specific_list[] = $value[$field_name];
                }
            }
            self::$_filterOutCache[$key_cache] = $specific_list;
        } else {
            $specific_list = self::$_filterOutCache[$key_cache];
        }

        // $specific_list is empty if the threshold is reached
        if (empty($specific_list) || in_array($field_value, $specific_list)) {
            if ($name == 'id_product' && !self::$_hasGlobalProductRules) {
                $query_extra = 'AND `' . $name . '` = ' . (int) $field_value . ' ';
            } else {
                $query_extra = 'AND `' . $name . '` ' . self::formatIntInQuery(0, $field_value) . ' ';
            }
        }

        return $query_extra;
    }

    /**
     * Remove or add useless fields value depending on the values in the database (cache friendly).
     *
     * @param int|null $id_product
     * @param int|null $id_product_attribute
     * @param int|null $id_cart
     * @param string|null $beginning
     * @param string|null $ending
     *
     * @return string
     */
    protected static function computeExtraConditions($id_product, $id_product_attribute, $id_customer, $id_cart, $beginning = null, $ending = null)
    {
        $first_date = date('Y-m-d 00:00:00');
        $last_date = date('Y-m-d 23:59:59');
        $now = date('Y-m-d H:i:00');
        if ($beginning === null) {
            $beginning = $now;
        }
        if ($ending === null) {
            $ending = $now;
        }
        $id_customer = (int) $id_customer;
        $id_cart = (int) $id_cart;

        $query_extra = '';

        if ($id_product !== null) {
            $query_extra .= self::filterOutField('id_product', $id_product);
        }

        if ($id_customer !== null) {
            $query_extra .= self::filterOutField('id_customer', $id_customer);
        }

        if ($id_product_attribute !== null) {
            $query_extra .= self::filterOutField('id_product_attribute', $id_product_attribute);
        }

        $query_extra .= self::filterOutField('id_cart', $id_cart);

        if ($ending == $now && $beginning == $now) {
            $key = __FUNCTION__ . '-' . $first_date . '-' . $last_date;
            if (!array_key_exists($key, self::$_filterOutCache)) {
                $query_from_count = 'SELECT 1 FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `from` BETWEEN \'' . $first_date . '\' AND \'' . $last_date . '\'';
                $from_specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_from_count);

                $query_to_count = 'SELECT 1 FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `to` BETWEEN \'' . $first_date . '\' AND \'' . $last_date . '\'';

                $to_specific_count = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query_to_count);
                self::$_filterOutCache[$key] = [$from_specific_count, $to_specific_count];
            } else {
                list($from_specific_count, $to_specific_count) = self::$_filterOutCache[$key];
            }
        } else {
            $from_specific_count = $to_specific_count = 1;
        }

        // if the from and to is not reached during the current day, just change $ending & $beginning to any date of the day to improve the cache
        if (!$from_specific_count && !$to_specific_count) {
            $ending = $beginning = $first_date;
        }
        $db = Db::getInstance();
        $beginning = $db->escape($beginning);
        $ending = $db->escape($ending);

        $query_extra .= ' AND (`from` = \'0000-00-00 00:00:00\' OR \'' . $beginning . '\' >= `from`)'
                       . ' AND (`to` = \'0000-00-00 00:00:00\' OR \'' . $ending . '\' <= `to`)';

        return $query_extra;
    }

    protected static function formatIntInQuery($first_value, $second_value)
    {
        $first_value = (int) $first_value;
        $second_value = (int) $second_value;
        if ($first_value != $second_value) {
            return 'IN (' . $first_value . ', ' . $second_value . ')';
        } else {
            return ' = ' . $first_value;
        }
    }

    /**
     * Check if the given product could have a specific price.
     *
     * @param int $idProduct
     *
     * @return bool
     */
    final protected static function couldHaveSpecificPrice($idProduct)
    {
        if (self::$_hasGlobalProductRules === null) {
            $queryHasGlobalRule = 'SELECT 1 FROM `' . _DB_PREFIX_ . 'specific_price` WHERE id_product = 0';
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($queryHasGlobalRule);
            self::$_hasGlobalProductRules = !empty($row);
        }
        if (self::$_hasGlobalProductRules) {
            return true;
        }

        if (!array_key_exists($idProduct, self::$_couldHaveSpecificPriceCache)) {
            $query = 'SELECT 1 FROM `' . _DB_PREFIX_ . 'specific_price` WHERE id_product = ' . (int) $idProduct;
            $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            self::$_couldHaveSpecificPriceCache[$idProduct] = !empty($row);
        }

        return self::$_couldHaveSpecificPriceCache[$idProduct];
    }

    /**
     * Compute the cache key by setting to 0 the fields which doesn't have any specific values in the DB.
     *
     * @param int $id_product
     * @param int $id_shop
     * @param int $id_currency
     * @param int $id_country
     * @param int $id_group
     * @param int $quantity
     * @param int $id_product_attribute
     * @param int $id_customer
     * @param int $id_cart
     * @param int $real_quantity
     *
     * @return string
     */
    final protected static function computeKey(
        $id_product,
        $id_shop,
        $id_currency,
        $id_country,
        $id_group,
        $quantity,
        $id_product_attribute,
        $id_customer,
        $id_cart,
        $real_quantity
    ) {
        if (self::$_no_specific_values !== null) {
            // $_no_specific_values contains the fieldName from the DB which don't have values != 0
            // So it's ok the set the value to 0 for those fields to improve the cache efficiency
            // Note that the variableName from the DB needs to match the function args name
            // I.e. if the computeKey args are converted at some point in camelCase, we will need to introduce a
            // snakeCase to camelCase conversion of $variableName
            foreach (array_keys(self::$_no_specific_values) as $variableName) {
                ${$variableName} = 0;
            }
        }

        return (int) $id_product . '-' . (int) $id_shop . '-' . (int) $id_currency . '-' . (int) $id_country . '-' .
            (int) $id_group . '-' . (int) $quantity . '-' . (int) $id_product_attribute . '-' . (int) $id_cart . '-' .
            (int) $id_customer . '-' . (int) $real_quantity;
    }

    /**
     * Returns the specificPrice information related to a given productId and context.
     *
     * @param int $id_product
     * @param int $id_shop
     * @param int $id_currency
     * @param int $id_country
     * @param int $id_group
     * @param int $quantity
     * @param int $id_product_attribute
     * @param int $id_customer
     * @param int $id_cart
     * @param int $real_quantity
     *
     * @return array
     */
    public static function getSpecificPrice(
        $id_product,
        $id_shop,
        $id_currency,
        $id_country,
        $id_group,
        $quantity,
        $id_product_attribute = null,
        $id_customer = 0,
        $id_cart = 0,
        $real_quantity = 0
    ) {
        if (!SpecificPrice::isFeatureActive()) {
            return [];
        }
        /*
         * The date is not taken into account for the cache, but this is for the better because it keeps the consistency
         * for the whole script.
         * The price must not change between the top and the bottom of the page
        */

        if (!self::couldHaveSpecificPrice($id_product)) {
            return [];
        }

        if (static::$psQtyDiscountOnCombination === null) {
            static::$psQtyDiscountOnCombination = Configuration::get('PS_QTY_DISCOUNT_ON_COMBINATION');
            // no need to compute the key the first time the function is called, we know the cache has not
            // been computed yet
            $key = null;
        } else {
            $key = self::computeKey(
                $id_product,
                $id_shop,
                $id_currency,
                $id_country,
                $id_group,
                $quantity,
                $id_product_attribute,
                $id_customer,
                $id_cart,
                $real_quantity
            );
        }

        if (!array_key_exists($key, self::$_specificPriceCache)) {
            $query_extra = self::computeExtraConditions($id_product, $id_product_attribute, $id_customer, $id_cart);
            if ($key === null) {
                // compute the key after calling computeExtraConditions as it initializes some useful cache
                $key = self::computeKey(
                    $id_product,
                    $id_shop,
                    $id_currency,
                    $id_country,
                    $id_group,
                    $quantity,
                    $id_product_attribute,
                    $id_customer,
                    $id_cart,
                    $real_quantity
                );
            }
            $query = '
			SELECT *, ' . SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer) . '
				FROM `' . _DB_PREFIX_ . 'specific_price`
				WHERE
                `id_shop` ' . self::formatIntInQuery(0, $id_shop) . ' AND
                `id_currency` ' . self::formatIntInQuery(0, $id_currency) . ' AND
                `id_country` ' . self::formatIntInQuery(0, $id_country) . ' AND
                `id_group` ' . self::formatIntInQuery(0, $id_group) . ' ' . $query_extra . '
				AND IF(`from_quantity` > 1, `from_quantity`, 0) <= ';

            $query .= (static::$psQtyDiscountOnCombination || !$id_cart || !$real_quantity) ? (int) $quantity : max(1, (int) $real_quantity);
            $query .= ' ORDER BY `id_product_attribute` DESC, `id_cart` DESC, `from_quantity` DESC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC';
            self::$_specificPriceCache[$key] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        }

        return self::$_specificPriceCache[$key];
    }

    /**
     * @deprecated since 8.0 and will be removed in next major version.
     * @see SpecificPricePriorityUpdater::updateDefaultPriorities()
     *
     * @param array $priorities
     *
     * @return bool
     */
    public static function setPriorities($priorities)
    {
        @trigger_error(
            sprintf(
                '%s is deprecated since version 8.0. Use %s instead.',
                __METHOD__,
                SpecificPricePriorityUpdater::class . '::updateDefaultPriorities()'
            ),
            E_USER_DEPRECATED
        );

        $value = '';
        if (is_array($priorities)) {
            foreach ($priorities as $priority) {
                $value .= pSQL($priority) . ';';
            }
        }

        SpecificPrice::deletePriorities();

        return Configuration::updateValue('PS_SPECIFIC_PRICE_PRIORITIES', rtrim($value, ';'));
    }

    /**
     * Truncate the specific price priorities.
     *
     * @return bool
     */
    public static function deletePriorities()
    {
        return Db::getInstance()->execute('
		TRUNCATE `' . _DB_PREFIX_ . 'specific_price_priority`
		');
    }

    public static function setSpecificPriority($id_product, $priorities)
    {
        $value = '';
        foreach ($priorities as $priority) {
            $value .= pSQL($priority) . ';';
        }

        return Db::getInstance()->execute('
		INSERT INTO `' . _DB_PREFIX_ . 'specific_price_priority` (`id_product`, `priority`)
		VALUES (' . (int) $id_product . ',\'' . pSQL(rtrim($value, ';')) . '\')
		ON DUPLICATE KEY UPDATE `priority` = \'' . pSQL(rtrim($value, ';')) . '\'
		');
    }

    public static function getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_product_attribute = null, $all_combinations = false, $id_customer = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return [];
        }

        $query_extra = self::computeExtraConditions($id_product, ((!$all_combinations) ? $id_product_attribute : null), $id_customer, null);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *,
					' . SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer) . '
				FROM `' . _DB_PREFIX_ . 'specific_price`
				WHERE
					`id_shop` ' . self::formatIntInQuery(0, $id_shop) . ' AND
					`id_currency` ' . self::formatIntInQuery(0, $id_currency) . ' AND
					`id_country` ' . self::formatIntInQuery(0, $id_country) . ' AND
					`id_group` ' . self::formatIntInQuery(0, $id_group) . ' ' . $query_extra . '
					ORDER BY `from_quantity` ASC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC
		', false, false);

        $targeted_prices = [];
        $last_quantity = [];

        while ($specific_price = Db::getInstance()->nextRow($result)) {
            if (!isset($last_quantity[(int) $specific_price['id_product_attribute']])) {
                $last_quantity[(int) $specific_price['id_product_attribute']] = $specific_price['from_quantity'];
            } elseif ($last_quantity[(int) $specific_price['id_product_attribute']] == $specific_price['from_quantity']) {
                continue;
            }

            $last_quantity[(int) $specific_price['id_product_attribute']] = $specific_price['from_quantity'];
            if ($specific_price['from_quantity'] > 1) {
                $targeted_prices[] = $specific_price;
            }
        }

        return $targeted_prices;
    }

    public static function getQuantityDiscount($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity, $id_product_attribute = null, $id_customer = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return [];
        }

        $query_extra = self::computeExtraConditions($id_product, $id_product_attribute, $id_customer, null);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT *,
					' . SpecificPrice::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group, $id_customer) . '
			FROM `' . _DB_PREFIX_ . 'specific_price`
			WHERE
					`id_shop` ' . self::formatIntInQuery(0, $id_shop) . ' AND
					`id_currency` ' . self::formatIntInQuery(0, $id_currency) . ' AND
					`id_country` ' . self::formatIntInQuery(0, $id_country) . ' AND
					`id_group` ' . self::formatIntInQuery(0, $id_group) . ' AND
					`from_quantity` >= ' . (int) $quantity . ' ' . $query_extra . '
					ORDER BY `from_quantity` DESC, `score` DESC, `to` DESC, `from` DESC
		');
    }

    public static function getProductIdByDate($id_shop, $id_currency, $id_country, $id_group, $beginning, $ending, $id_customer = 0, $with_combination_id = false)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return [];
        }

        $query_extra = self::computeExtraConditions(null, null, $id_customer, null, $beginning, $ending);
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_product`, `id_product_attribute`
			FROM `' . _DB_PREFIX_ . 'specific_price`
			WHERE	`id_shop` ' . self::formatIntInQuery(0, $id_shop) . ' AND
					`id_currency` ' . self::formatIntInQuery(0, $id_currency) . ' AND
					`id_country` ' . self::formatIntInQuery(0, $id_country) . ' AND
					`id_group` ' . self::formatIntInQuery(0, $id_group) . ' AND
					`from_quantity` = 1 AND
					`reduction` > 0
		' . $query_extra);
        $ids_product = [];
        foreach ($results as $value) {
            $ids_product[] = $with_combination_id ?
                [
                    'id_product' => (int) $value['id_product'],
                    'id_product_attribute' => (int) $value['id_product_attribute'],
                ] : (int) $value['id_product'];
        }

        return $ids_product;
    }

    /**
     * Delete a product from its id.
     *
     * @param int $id_product
     *
     * @return bool
     */
    public static function deleteByProductId($id_product)
    {
        if (Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'specific_price` WHERE `id_product` = ' . (int) $id_product)) {
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed('specific_price'));

            return true;
        }

        return false;
    }

    /**
     * Duplicate a product.
     *
     * @param bool|int $id_product The product ID to duplicate, false when duplicating the current product
     * @param array $combination_associations Associations between the ids of base combinations and their duplicates
     *
     * @return bool
     */
    public function duplicate($id_product = false, array $combination_associations = []): bool
    {
        if ($id_product) {
            $this->id_product = (int) $id_product;
        }
        if ($this->id_product_attribute && isset($combination_associations[$this->id_product_attribute])) {
            $this->id_product_attribute = (int) $combination_associations[$this->id_product_attribute];
        }
        unset($this->id);
        // specific price row may already have been created for catalog specific price rule
        if (static::exists(
            $this->id_product,
            $this->id_product_attribute,
            $this->id_shop,
            $this->id_group,
            $this->id_country,
            $this->id_currency,
            $this->id_customer,
            $this->from_quantity,
            $this->from,
            $this->to,
            $this->id_specific_price_rule != 0
        )) {
            return true;
        }

        return $this->add();
    }

    /**
     * This method is allow to know if a feature is used or active.
     *
     * @since 1.5.0.1
     *
     * @return bool
     */
    public static function isFeatureActive()
    {
        return (bool) Configuration::get('PS_SPECIFIC_PRICE_FEATURE_ACTIVE');
    }

    /**
     * Check if a specific price exists based on given parameters and return the specific rule id.
     *
     * @param int $id_product
     * @param int $id_product_attribute Set at 0 when the specific price was set for all attributes
     * @param int $id_shop Set at 0 when the specific price was set for all shops
     * @param int $id_group Set at 0 when the specific price was set for all groups
     * @param int $id_country Set at 0 when the specific price was set for all countries
     * @param int $id_currency Set at 0 when the specific price was set for all currencies
     * @param int $id_customer Set at 0 when the specific price was set for all customers
     * @param int $from_quantity The starting quantity for which the specific price is applied
     * @param string $from Date from which the specific price start. 0000-00-00 00:00:00 if no starting date
     * @param string $to Date from which the specific price end. 0000-00-00 00:00:00 if no ending date
     * @param bool $rule if a specific price rule (from specific_price_rule) was set or not
     * @param int|null $id_cart if a specific cart was set or not (default: null no additional check is performed)
     *
     * @return int The specific rule id, 0 if no corresponding rule found
     */
    public static function exists($id_product, $id_product_attribute, $id_shop, $id_group, $id_country, $id_currency, $id_customer, $from_quantity, $from, $to, $rule = false, $id_cart = null)
    {
        $rule = ' AND `id_specific_price_rule`' . (!$rule ? '=0' : '!=0');
        if (null !== $id_cart) {
            $rule .= ' AND id_cart = ' . (int) $id_cart;
        }

        return (int) Db::getInstance()->getValue('SELECT `id_specific_price`
												FROM ' . _DB_PREFIX_ . 'specific_price
												WHERE `id_product`=' . (int) $id_product . ' AND
													`id_product_attribute`=' . (int) $id_product_attribute . ' AND
													`id_shop`=' . (int) $id_shop . ' AND
													`id_group`=' . (int) $id_group . ' AND
													`id_country`=' . (int) $id_country . ' AND
													`id_currency`=' . (int) $id_currency . ' AND
													`id_customer`=' . (int) $id_customer . ' AND
													`from_quantity`=' . (int) $from_quantity . ' AND
													`from` >= \'' . pSQL($from) . '\' AND
													`to` <= \'' . pSQL($to) . '\'' . $rule);
    }
}
