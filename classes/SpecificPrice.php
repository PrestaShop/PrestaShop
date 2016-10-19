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

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

/**
 * Class SpecificPriceCore
 */
class SpecificPriceCore extends ObjectModel
{
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
    public static $definition = array(
        'table' => 'specific_price',
        'primary' => 'id_specific_price',
        'fields' => array(
            'id_shop_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_specific_price_rule' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'price' => array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'from_quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'reduction' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_tax' => array('type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true),
            'reduction_type' => array('type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true),
            'from' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'to' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
        ),
    );

    protected $webserviceParameters = array(
        'objectsNodeName' => 'specific_prices',
        'objectNodeName' => 'specific_price',
        'fields' => array(
            'id_shop_group' => array('xlink_resource' => 'shop_groups'),
            'id_shop' => array('xlink_resource' => 'shops', 'required' => true),
            'id_cart' => array('xlink_resource' => 'carts', 'required' => true),
            'id_product' => array('xlink_resource' => 'products', 'required' => true),
            'id_product_attribute' => array('xlink_resource' => 'product_attributes'),
            'id_currency' => array('xlink_resource' => 'currencies', 'required' => true),
            'id_country' => array('xlink_resource' => 'countries', 'required' => true),
            'id_group' => array('xlink_resource' => 'groups', 'required' => true),
            'id_customer' => array('xlink_resource' => 'customers', 'required' => true),
        ),
    );


    protected static $_specificPriceCache = array();
    protected static $_filterOutCache = array();
    protected static $_cache_priorities = array();
    protected static $_no_specific_values = array();

    public function add($autoDate = true, $nullValues = false)
    {
        if (parent::add($autoDate, $nullValues)) {
            // Flush cache when we adding a new specific price
            SpecificPrice::$_specificPriceCache = array();
            Product::flushPriceCache();
            // Set cache of feature detachable to true
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', '1');

            return true;
        }

        return false;
    }

    /**
     * @param bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = false)
    {
        if (parent::update($nullValues)) {
            // Flush cache when we updating a new specific price
            SpecificPrice::$_specificPriceCache = array();
            Product::flushPriceCache();

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if (parent::delete()) {
            // Flush cache when we deletind a new specific price
            SpecificPrice::$_specificPriceCache = array();
            Product::flushPriceCache();
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed($this->def['table']));

            return true;
        }

        return false;
    }

    /**
     * Get by Product ID
     *
     * @param int  $idProduct          Product ID
     * @param bool $idProductAttribute Product Attribute ID
     * @param bool $idCart             Cart ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getByProductId($idProduct, $idProductAttribute = false, $idCart = false)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$idProduct.
            ($idProductAttribute ? ' AND id_product_attribute = '.(int) $idProductAttribute : '').'
			AND id_cart = '.(int)$idCart);
    }

    /**
     * Delete by Cart ID
     *
     * @param int  $idCart             Cart ID
     * @param bool $idProduct          Product ID
     * @param bool $idProductAttribute Product Attribute ID
     *
     * @return bool
     */
    public static function deleteByIdCart($idCart, $idProduct = false, $idProductAttribute = false)
    {
        return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'specific_price`
			WHERE id_cart='.(int) $idCart.
            ($idProduct ? ' AND id_product='.(int) $idProduct.' AND id_product_attribute='.(int) $idProductAttribute : ''));
    }

    /**
     * Get IDs by Product ID
     *
     * @param int  $idProduct          Product ID
     * @param bool $idProductAttribute Product Attribute ID
     * @param int  $idCart             Cart ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getIdsByProductId($idProduct, $idProductAttribute = false, $idCart = 0)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_specific_price`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int) $idProduct.'
			AND id_product_attribute='.(int) $idProductAttribute.'
			AND id_cart='.(int) $idCart);
    }

    /**
     * @param int $idProduct
     * @param int $idShop
     * @param int $idCurrency
     * @param int $idCountry
     * @param int $idGroup
     * @param int $idCustomer
     *
     * @return string
     *
     * @deprecated 1.7.0
     */
    protected static function _getScoreQuery($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idCustomer)
    {
        return self::getScoreQuery($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idCustomer);
    }

    /**
     * score generation for quantity discount
     *
     * @param int $idProduct
     * @param int $idShop
     * @param int $id_currency
     * @param int $idCountry
     * @param int $idGroup
     * @param int $idCustomer
     *
     * @return string
     *
     * @since 1.7.0
     */
    protected static function getScoreQuery($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idCustomer)
    {
        $converter = new CamelCaseToSnakeCaseNameConverter();

        $select = '(';

        $priority = SpecificPrice::getPriority($idProduct);
        foreach (array_reverse($priority) as $k => $field) {
            if (!empty($field)) {
                // snake_case to camelCase to access variable in local function scope
                $ccField = lcfirst($converter->denormalize($field));
                $select .= ' IF (`'.bqSQL($field).'` = '.(int) $$ccField.', '.pow(2, $k + 1).', 0) + ';
            }
        }

        return rtrim($select, ' +').') AS `score`';
    }

    /**
     * Get priority
     *
     * @param int $idProduct Product ID
     *
     * @return array
     */
    public static function getPriority($idProduct)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return explode(';', Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES'));
        }

        if (!isset(SpecificPrice::$_cache_priorities[(int) $idProduct])) {
            SpecificPrice::$_cache_priorities[(int) $idProduct] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `priority`, `id_specific_price_priority`
				FROM `'._DB_PREFIX_.'specific_price_priority`
				WHERE `id_product` = '.(int) $idProduct.'
				ORDER BY `id_specific_price_priority` DESC
			');
        }

        $priority = SpecificPrice::$_cache_priorities[(int) $idProduct];

        if (!$priority) {
            $priority = Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES');
        }
        $priority = 'id_customer;'.$priority;

        return preg_split('/;/', $priority);
    }

    /**
     * Remove or add a field value to a query if values are present in the database (cache friendly)
     *
     * @param string $fieldName
     * @param int    $fieldValue
     * @param int    $threshold
     *
     * @return string
     * @throws PrestaShopDatabaseException
     */
    protected static function filterOutField($fieldName, $fieldValue, $threshold = 1000)
    {
        $name = Db::getInstance()->escape($fieldName);
        $queryExtra = 'AND `'.$name.'` = 0 ';
        if ($fieldValue == 0 || array_key_exists($fieldName, self::$_no_specific_values)) {
            return $queryExtra;
        }
        $keyCache = __FUNCTION__.'-'.$fieldName.'-'.$threshold;
        $specificList = array();
        if (!array_key_exists($keyCache, SpecificPrice::$_filterOutCache)) {
            $queryCount    = 'SELECT COUNT(DISTINCT `'.$name.'`) FROM `'._DB_PREFIX_.'specific_price` WHERE `'.$fieldName.'` != 0';
            $specificCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($queryCount);
            if ($specificCount == 0) {
                self::$_no_specific_values[$fieldName] = true;

                return $queryExtra;
            }
            if ($specificCount < $threshold) {
                $query = 'SELECT DISTINCT `'.$name.'` FROM `'._DB_PREFIX_.'specific_price` WHERE `'.$name.'` != 0';
                $tmpSpecificList = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                foreach ($tmpSpecificList as $key => $value) {
                    $specificList[] = $value[$fieldName];
                }
            }
            SpecificPrice::$_filterOutCache[$keyCache] = $specificList;
        } else {
            $specificList = SpecificPrice::$_filterOutCache[$keyCache];
        }

        if (in_array($fieldValue, $specificList)) {
            $queryExtra = 'AND `'.$name.'` '.self::formatIntInQuery(0, $fieldValue).' ';
        }

        return $queryExtra;
    }

    /**
     * Remove or add useless fields value depending on the values in the database (cache friendly)
     *
     * @param int|null    $idProduct
     * @param int|null    $idProductAttribute
     * @param int|null    $idCart
     * @param string|null $beginning
     * @param string|null $ending
     *
     * @return string
     */
    protected static function computeExtraConditions($idProduct, $idProductAttribute, $idCustomer, $idCart, $beginning = null, $ending = null)
    {
        $firstDate = date('Y-m-d 00:00:00');
        $lastDate = date('Y-m-d 23:59:59');
        $now = date('Y-m-d H:i:00');
        if ($beginning === null) {
            $beginning = $now;
        }
        if ($ending === null) {
            $ending = $now;
        }
        $idCustomer = (int) $idCustomer;

        $queryExtra = '';

        if ($idProduct !== null) {
            $queryExtra .= self::filterOutField('id_product', $idProduct);
        }

        if ($idCustomer !== null) {
            $queryExtra .= self::filterOutField('id_customer', $idCustomer);
        }

        if ($idProductAttribute !== null) {
            $queryExtra .= self::filterOutField('id_product_attribute', $idProductAttribute);
        }

        if ($idCart !== null) {
            $queryExtra .= self::filterOutField('id_cart', $idCart);
        }

        if ($ending == $now && $beginning == $now) {
            $key = __FUNCTION__.'-'.$firstDate.'-'.$lastDate;
            if (!array_key_exists($key, SpecificPrice::$_filterOutCache)) {
                $queryFromCount    = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` WHERE `from` BETWEEN \''.$firstDate.'\' AND \''.$lastDate.'\'';
                $fromSpecificCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($queryFromCount);

                $queryToCount                       = 'SELECT 1 FROM `'._DB_PREFIX_.'specific_price` WHERE `to` BETWEEN \''.$firstDate.'\' AND \''.$lastDate.'\'';

                $toSpecificCount                    = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($queryToCount);
                SpecificPrice::$_filterOutCache[$key] = array($fromSpecificCount, $toSpecificCount);
            } else {
                list($fromSpecificCount, $toSpecificCount) = SpecificPrice::$_filterOutCache[$key];
            }
        } else {
            $fromSpecificCount = $toSpecificCount = 1;
        }

        // if the from and to is not reached during the current day, just change $ending & $beginning to any date of the day to improve the cache
        if (!$fromSpecificCount && !$toSpecificCount) {
            $ending = $beginning = $firstDate;
        }
        $db = Db::getInstance();
        $beginning = $db->escape($beginning);
        $ending = $db->escape($ending);

        $queryExtra .= ' AND (`from` = \'0000-00-00 00:00:00\' OR \''.$beginning.'\' >= `from`)'
                       .' AND (`to` = \'0000-00-00 00:00:00\' OR \''.$ending.'\' <= `to`)';

        return $queryExtra;
    }

    /**
     * Format integer in query
     *
     * @param int $firstValue
     * @param int $secondValue
     *
     * @return string
     */
    protected static function formatIntInQuery($firstValue, $secondValue)
    {
        $firstValue = (int) $firstValue;
        $secondValue = (int) $secondValue;
        if ($firstValue != $secondValue) {
            return 'IN ('.$firstValue.', '.$secondValue.')';
        } else {
            return ' = '.$firstValue;
        }
    }

    /**
     * Get specific price
     *
     * @param int  $idProduct
     * @param int  $idShop
     * @param int  $idCurrency
     * @param int  $idCountry
     * @param int  $idGroup
     * @param int  $quantity
     * @param null $idProductAttribute
     * @param int  $idCustomer
     * @param int  $idCart
     * @param int  $realQuantity
     *
     * @return array
     */
    public static function getSpecificPrice($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $quantity, $idProductAttribute = null, $idCustomer = 0, $idCart = 0, $realQuantity = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }
        /*
        ** The date is not taken into account for the cache, but this is for the better because it keeps the consistency for the whole script.
        ** The price must not change between the top and the bottom of the page
        */

        $key = ((int) $idProduct.'-'.(int) $idShop.'-'.(int) $idCurrency.'-'.(int) $idCountry.'-'.(int) $idGroup.'-'.(int) $quantity.'-'.(int) $idProductAttribute.'-'.(int) $idCart.'-'.(int) $idCustomer.'-'.(int) $realQuantity);
        if (!array_key_exists($key, SpecificPrice::$_specificPriceCache)) {
            $queryExtra = self::computeExtraConditions($idProduct, $idProductAttribute, $idCustomer, $idCart);
            $query = '
			SELECT *, '.SpecificPrice::getScoreQuery($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idCustomer).'
				FROM `'._DB_PREFIX_.'specific_price`
				WHERE
                `id_shop` '.self::formatIntInQuery(0, $idShop).' AND
                `id_currency` '.self::formatIntInQuery(0, $idCurrency).' AND
                `id_country` '.self::formatIntInQuery(0, $idCountry).' AND
                `id_group` '.self::formatIntInQuery(0, $idGroup).' '.$queryExtra.'
				AND IF(`from_quantity` > 1, `from_quantity`, 0) <= ';

            $query .= (Configuration::get('PS_QTY_DISCOUNT_ON_COMBINATION') || !$idCart || !$realQuantity) ? (int) $quantity : max(1, (int) $realQuantity);
            $query .= ' ORDER BY `id_product_attribute` DESC, `from_quantity` DESC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC';

            SpecificPrice::$_specificPriceCache[$key] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        }

        return SpecificPrice::$_specificPriceCache[$key];
    }

    /**
     * Set priorities
     *
     * @param array $priorities
     *
     * @return bool
     */
    public static function setPriorities($priorities)
    {
        $value = '';
        if (is_array($priorities)) {
            foreach ($priorities as $priority) {
                $value .= pSQL($priority).';';
            }
        }

        SpecificPrice::deletePriorities();

        return Configuration::updateValue('PS_SPECIFIC_PRICE_PRIORITIES', rtrim($value, ';'));
    }

    /**
     * Delete priorities
     *
     * @return bool
     */
    public static function deletePriorities()
    {
        return Db::getInstance()->execute('
		TRUNCATE `'._DB_PREFIX_.'specific_price_priority`
		');
    }

    /**
     * Set specific priority
     *
     * @param int   $idProduct Product ID
     * @param array $priorities
     *
     * @return bool
     */
    public static function setSpecificPriority($idProduct, $priorities)
    {
        $value = '';
        foreach ($priorities as $priority) {
            $value .= pSQL($priority).';';
        }

        return Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'specific_price_priority` (`id_product`, `priority`)
		VALUES ('.(int)$idProduct.',\''.pSQL(rtrim($value, ';')).'\')
		ON DUPLICATE KEY UPDATE `priority` = \''.pSQL(rtrim($value, ';')).'\'
		');
    }

    /**
     * @param int  $idProduct
     * @param int  $idShop
     * @param int  $idCurrency
     * @param int  $idCountry
     * @param int  $idGroup
     * @param null $idProductAttribute
     * @param bool $allCombinations
     * @param int  $idCustomer
     *
     * @return array
     */
    public static function getQuantityDiscounts($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idProductAttribute = null, $allCombinations = false, $idCustomer = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $queryExtra = self::computeExtraConditions($idProduct, ((!$allCombinations)?$idProductAttribute:null), $idCustomer, null);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *,
					'.SpecificPrice::_getScoreQuery($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idCustomer).'
				FROM `'._DB_PREFIX_.'specific_price`
				WHERE
					`id_shop` '.self::formatIntInQuery(0, $idShop).' AND
					`id_currency` '.self::formatIntInQuery(0, $idCurrency).' AND
					`id_country` '.self::formatIntInQuery(0, $idCountry).' AND
					`id_group` '.self::formatIntInQuery(0, $idGroup).' '.$queryExtra.'
					ORDER BY `from_quantity` ASC, `id_specific_price_rule` ASC, `score` DESC, `to` DESC, `from` DESC
		', false, false);

        $targetedPrices = array();
        $lastQuantity = array();

        while ($specificPrice = Db::getInstance()->nextRow($result)) {
            if (!isset($lastQuantity[(int) $specificPrice['id_product_attribute']])) {
                $lastQuantity[(int) $specificPrice['id_product_attribute']] = $specificPrice['from_quantity'];
            } elseif ($lastQuantity[(int) $specificPrice['id_product_attribute']] == $specificPrice['from_quantity']) {
                continue;
            }

            $lastQuantity[(int) $specificPrice['id_product_attribute']] = $specificPrice['from_quantity'];
            if ($specificPrice['from_quantity'] > 1) {
                $targetedPrices[] = $specificPrice;
            }
        }

        return $targetedPrices;
    }

    /**
     * @param      $idProduct
     * @param      $idShop
     * @param      $idCurrency
     * @param      $idCountry
     * @param      $idGroup
     * @param      $quantity
     * @param null $idProductAttribute
     * @param int  $idCustomer
     *
     * @return array|bool|null|object
     */
    public static function getQuantityDiscount($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $quantity, $idProductAttribute = null, $idCustomer = 0)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $queryExtra = self::computeExtraConditions($idProduct, $idProductAttribute, $idCustomer, null);
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT *,
					'.SpecificPrice::_getScoreQuery($idProduct, $idShop, $idCurrency, $idCountry, $idGroup, $idCustomer).'
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE
					`id_shop` '.self::formatIntInQuery(0, $idShop).' AND
					`id_currency` '.self::formatIntInQuery(0, $idCurrency).' AND
					`id_country` '.self::formatIntInQuery(0, $idCountry).' AND
					`id_group` '.self::formatIntInQuery(0, $idGroup).' AND
					`from_quantity` >= '.(int)$quantity.' '.$queryExtra.'
					ORDER BY `from_quantity` DESC, `score` DESC, `to` DESC, `from` DESC
		');
    }

    /**
     * @param int  $idShop
     * @param int  $idCurrency
     * @param int  $idCountry
     * @param int  $idGroup
     * @param int  $beginning
     * @param int  $ending
     * @param int  $idCustomer
     * @param bool $withCombinationId
     *
     * @return array
     */
    public static function getProductIdByDate($idShop, $idCurrency, $idCountry, $idGroup, $beginning, $ending, $idCustomer = 0, $withCombinationId = false)
    {
        if (!SpecificPrice::isFeatureActive()) {
            return array();
        }

        $queryExtra = self::computeExtraConditions(null, null, $idCustomer, null, $beginning, $ending);
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_shop` '.self::formatIntInQuery(0, $idShop).' AND
					`id_currency` '.self::formatIntInQuery(0, $idCurrency).' AND
					`id_country` '.self::formatIntInQuery(0, $idCountry).' AND
					`id_group` '.self::formatIntInQuery(0, $idGroup).' AND
					`from_quantity` = 1 AND
					`reduction` > 0
		'.$queryExtra);
        $idsProduct = array();
        foreach ($results as $key => $value) {
            $idsProduct[] = $withCombinationId ? array('id_product' => (int) $value['id_product'], 'id_product_attribute' => (int) $value['id_product_attribute']) : (int) $value['id_product'];
        }

        return $idsProduct;
    }

    /**
     * Delete by Product ID
     *
     * @param int $idProduct Product ID
     *
     * @return bool
     */
    public static function deleteByProductId($idProduct)
    {
        if (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)$idProduct)) {
            // Refresh cache of feature detachable
            Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', SpecificPrice::isCurrentlyUsed('specific_price'));

            return true;
        }

        return false;
    }

    /**
     * Duplicate
     *
     * @param int $idProduct
     *
     * @return bool
     */
    public function duplicate($idProduct = false)
    {
        if ($idProduct) {
            $this->id_product = (int)$idProduct;
        }
        unset($this->id);

        return $this->add();
    }

    /**
     * This method is allow to know if a feature is used or active
     *
     * @return bool
     *
     * @since 1.5.0.1
     */
    public static function isFeatureActive()
    {
        static $feature_active = null;

        if ($feature_active === null) {
            $feature_active = Configuration::get('PS_SPECIFIC_PRICE_FEATURE_ACTIVE');
        }
        return $feature_active;
    }

    /**
     * @param int    $idProduct
     * @param int    $idProductAttribute
     * @param int    $idShop
     * @param int    $idGroup
     * @param int    $idCountry
     * @param int    $idCurrency
     * @param int    $idCustomer
     * @param string $fromQuantity
     * @param string $from
     * @param string $to
     * @param bool   $rule
     *
     * @return int
     */
    public static function exists($idProduct, $idProductAttribute, $idShop, $idGroup, $idCountry, $idCurrency, $idCustomer, $fromQuantity, $from, $to, $rule = false)
    {
        $rule = ' AND `id_specific_price_rule`'.(!$rule ? '=0' : '!=0');
        return (int) Db::getInstance()->getValue('SELECT `id_specific_price`
												FROM '._DB_PREFIX_.'specific_price
												WHERE `id_product`='.(int) $idProduct.' AND
													`id_product_attribute`='.(int) $idProductAttribute.' AND
													`id_shop`='.(int) $idShop.' AND
													`id_group`='.(int) $idGroup.' AND
													`id_country`='.(int) $idCountry.' AND
													`id_currency`='.(int) $idCurrency.' AND
													`id_customer`='.(int) $idCustomer.' AND
													`from_quantity`='.(int) $fromQuantity.' AND
													`from` >= \''.pSQL($from).'\' AND
													`to` <= \''.pSQL($to).'\''.$rule);
    }
}
