<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ConfigurationCore extends ObjectModel
{
    public $id;

    /** @var string Key */
    public $name;

    public $id_shop_group;
    public $id_shop;

    /** @var string Value */
    public $value;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'configuration',
        'primary' => 'id_configuration',
        'multilang' => true,
        'fields' => array(
            'name' =>            array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 254),
            'id_shop_group' =>    array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
            'id_shop' =>        array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
            'value' =>            array('type' => self::TYPE_STRING),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /** @var array Configuration cache (kept for backward compat) */
    protected static $_cache = null;

    /** @var array Configuration cache with optimised key order */
    protected static $_new_cache_shop = null;
    protected static $_new_cache_group = null;
    protected static $_new_cache_global = null;
    protected static $_initialized = false;

    /** @var array Vars types */
    protected static $types = array();

    protected $webserviceParameters = array(
        'fields' => array(
            'value' => array(),
        )
    );

    /**
     * @see ObjectModel::getFieldsLang()
     * @return bool|array Multilingual fields
     */
    public function getFieldsLang()
    {
        if (!is_array($this->value)) {
            return true;
        }
        return parent::getFieldsLang();
    }

    /**
     * Return ID a configuration key
     *
     * @param string $key
     * @param int $id_shop_group
     * @param int $id_shop
     * @return int Configuration key ID
     */
    public static function getIdByName($key, $id_shop_group = null, $id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        $sql = 'SELECT `'.bqSQL(self::$definition['primary']).'`
                FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'`
                WHERE name = \''.pSQL($key).'\'
                '.Configuration::sqlRestriction($id_shop_group, $id_shop);
        return (int)Db::getInstance()->getValue($sql);
    }

    public static function configurationIsLoaded()
    {
        return self::$_initialized;
    }

    /**
     * WARNING: For testing only. Do NOT rely on this method, it may be removed at any time.
     * @todo Delegate static calls from Configuration to an instance
     * of a class to be created.
     */
    public static function clearConfigurationCacheForTesting()
    {
        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;
    }

    /**
     * Load all configuration data
     */
    public static function loadConfiguration()
    {
        $sql     = 'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value, c.id_shop_group, c.id_shop
                FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'` c
                LEFT JOIN `'._DB_PREFIX_.bqSQL(self::$definition['table']).'_lang` cl ON (c.`'.bqSQL(
                self::$definition['primary']
            ).'` = cl.`'.bqSQL(self::$definition['primary']).'`)';
        $db      = Db::getInstance();
        $results = $db->executeS($sql);
        if ($results) {
            foreach ($results as $row) {
                $lang                      = ($row['id_lang']) ? $row['id_lang'] : 0;
                self::$types[$row['name']] = ($lang) ? 'lang' : 'normal';

                if (!isset(self::$_cache[self::$definition['table']][$lang])) {
                    self::$_cache[self::$definition['table']][$lang] = array(
                        'global' => array(),
                        'group'  => array(),
                        'shop'   => array(),
                    );
                }

                if ($row['value'] === null) {
                    $row['value'] = '';
                }

                if ($row['id_shop']) {
                    self::$_cache[self::$definition['table']][$lang]['shop'][$row['id_shop']][$row['name']] = $row['value'];
                    self::$_new_cache_shop[$row['name']][$lang][$row['id_shop']]                            = $row['value'];
                } elseif ($row['id_shop_group']) {
                    self::$_cache[self::$definition['table']][$lang]['group'][$row['id_shop_group']][$row['name']] = $row['value'];
                    self::$_new_cache_group[$row['name']][$lang][$row['id_shop_group']]                            = $row['value'];
                } else {
                    self::$_cache[self::$definition['table']][$lang]['global'][$row['name']] = $row['value'];
                    self::$_new_cache_global[$row['name']][$lang]                            = $row['value'];
                }
            }
            self::$_initialized = true;
        }
    }

    /**
     * Get a single configuration value (in one language only)
     *
     * @param string $key Key wanted
     * @param int $id_lang Language ID
     * @return string Value
     */
    public static function get($key, $id_lang = null, $id_shop_group = null, $id_shop = null, $default = false)
    {
        if (defined('_PS_DO_NOT_LOAD_CONFIGURATION_') && _PS_DO_NOT_LOAD_CONFIGURATION_) {
            return false;
        }

        // Init the cache on demand
        if (!self::$_initialized) {
            Configuration::loadConfiguration();
        }
        $id_lang = (int)$id_lang;

        if (!self::isLangKey($key)) {
            $id_lang = 0;
        }

        if (self::$_new_cache_shop === null) {
            $id_shop = 0;
        } else {
            if ($id_shop === null || !Shop::isFeatureActive()) {
                $id_shop = Shop::getContextShopID(true);
            }
        }

        if (self::$_new_cache_group === null) {
            $id_shop_group = 0;
        } else {
            if ($id_shop_group === null || !Shop::isFeatureActive()) {
                $id_shop_group = Shop::getContextShopGroupID(true);
            }
        }

        if ($id_shop && Configuration::hasKey($key, $id_lang, null, $id_shop)) {
            return self::$_new_cache_shop[$key][$id_lang][$id_shop];
        } elseif ($id_shop_group && Configuration::hasKey($key, $id_lang, $id_shop_group)) {
            return self::$_new_cache_group[$key][$id_lang][$id_shop_group];
        } elseif (Configuration::hasKey($key, $id_lang)) {
            return self::$_new_cache_global[$key][$id_lang];
        }

        return $default;
    }

    public static function getGlobalValue($key, $id_lang = null)
    {
        return Configuration::get($key, $id_lang, 0, 0);
    }

    /**
     * Get a single configuration value (in multiple languages)
     *
     * @param string $key Key wanted
     * @param int $id_shop_group
     * @param int $id_shop
     * @return array Values in multiple languages
     */
    public static function getInt($key, $id_shop_group = null, $id_shop = null)
    {
        $results_array = array();
        foreach (Language::getIDs() as $id_lang) {
            $results_array[$id_lang] = Configuration::get($key, $id_lang, $id_shop_group, $id_shop);
        }

        return $results_array;
    }

    /**
     * Get a single configuration value for all shops
     *
     * @param string $key Key wanted
     * @param int $id_lang
     * @return array Values for all shops
     */
    public static function getMultiShopValues($key, $id_lang = null)
    {
        $shops = Shop::getShops(false, null, true);
        $resultsArray = array();
        foreach ($shops as $id_shop) {
            $resultsArray[$id_shop] = Configuration::get($key, $id_lang, null, $id_shop);
        }
        return $resultsArray;
    }


    /**
     * Get several configuration values (in one language only)
     *
     * @throws PrestaShopException
     * @param array $keys Keys wanted
     * @param int $id_lang Language ID
     * @param int $id_shop_group
     * @param int $id_shop
     * @return array Values
     */
    public static function getMultiple($keys, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        if (!is_array($keys)) {
            throw new PrestaShopException('keys var is not an array');
        }

        $id_lang = (int)$id_lang;
        if ($id_shop === null) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        $results = array();
        foreach ($keys as $key) {
            $results[$key] = Configuration::get($key, $id_lang, $id_shop_group, $id_shop);
        }
        return $results;
    }

    /**
     * Check if key exists in configuration
     *
     * @param string $key
     * @param int $id_lang
     * @param int $id_shop_group
     * @param int $id_shop
     * @return bool
     */
    public static function hasKey($key, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        if (!is_int($key) && !is_string($key)) {
            return false;
        }

        $id_lang = (int)$id_lang;

        if ($id_shop) {
            return isset(self::$_new_cache_shop[$key][$id_lang][$id_shop]);
        } elseif ($id_shop_group) {
            return isset(self::$_new_cache_group[$key][$id_lang][$id_shop_group]);
        }

        return isset(self::$_new_cache_global[$key][$id_lang]);
    }

    /**
     * Set TEMPORARY a single configuration value (in one language only)
     *
     * @param string $key Key wanted
     * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
     * @param int $id_shop_group
     * @param int $id_shop
     */
    public static function set($key, $values, $id_shop_group = null, $id_shop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(sprintf(Tools::displayError('[%s] is not a valid configuration key'), Tools::htmlentitiesUTF8($key)));
        }

        if ($id_shop === null) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        foreach ($values as $lang => $value) {
            if ($id_shop) {
                self::$_new_cache_shop[$key][$lang][$id_shop] = $value;
                self::$_cache[self::$definition['table']][$lang]['shop'][$id_shop][$key] = $value;
            } elseif ($id_shop_group) {
                self::$_new_cache_group[$key][$lang][$id_shop_group] = $value;
                self::$_cache[self::$definition['table']][$lang]['group'][$id_shop_group][$key] = $value;
            } else {
                self::$_new_cache_global[$key][$lang] = $value;
                self::$_cache[self::$definition['table']][$lang]['global'][$key] = $value;
            }
        }
    }

    /**
     * Update configuration key for global context only
     *
     * @param string $key
     * @param mixed $values
     * @param bool $html
     * @return bool
     */
    public static function updateGlobalValue($key, $values, $html = false)
    {
        return Configuration::updateValue($key, $values, $html, 0, 0);
    }

    /**
     * Update configuration key and value into database (automatically insert if key does not exist)
     *
     * Values are inserted/updated directly using SQL, because using (Configuration) ObjectModel
     * may not insert values correctly (for example, HTML is escaped, when it should not be).
     * @TODO Fix saving HTML values in Configuration model
     *
     * @param string $key Key
     * @param mixed $values $values is an array if the configuration is multilingual, a single string else.
     * @param bool $html Specify if html is authorized in value
     * @param int $id_shop_group
     * @param int $id_shop
     * @return bool Update result
     */
    public static function updateValue($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(sprintf(Tools::displayError('[%s] is not a valid configuration key'), Tools::htmlentitiesUTF8($key)));
        }

        if ($id_shop === null || !Shop::isFeatureActive()) {
            $id_shop = Shop::getContextShopID(true);
        }
        if ($id_shop_group === null || !Shop::isFeatureActive()) {
            $id_shop_group = Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        if ($html) {
            foreach ($values as &$value) {
                $value = Tools::purifyHTML($value);
            }
            unset($value);
        }

        $result = true;
        foreach ($values as $lang => $value) {
            $stored_value = Configuration::get($key, $lang, $id_shop_group, $id_shop);
            // if there isn't a $stored_value, we must insert $value
            if ((!is_numeric($value) && $value === $stored_value) || (is_numeric($value) && $value == $stored_value && Configuration::hasKey($key, $lang))) {
                continue;
            }

            // If key already exists, update value
            if (Configuration::hasKey($key, $lang, $id_shop_group, $id_shop)) {
                if (!$lang) {
                    // Update config not linked to lang
                    $result &= Db::getInstance()->update(self::$definition['table'], array(
                        'value' => pSQL($value, $html),
                        'date_upd' => date('Y-m-d H:i:s'),
                    ), '`name` = \''.pSQL($key).'\''.Configuration::sqlRestriction($id_shop_group, $id_shop), 1, true);
                } else {
                    // Update multi lang
                    $sql = 'UPDATE `'._DB_PREFIX_.bqSQL(self::$definition['table']).'_lang` cl
                            SET cl.value = \''.pSQL($value, $html).'\',
                                cl.date_upd = NOW()
                            WHERE cl.id_lang = '.(int)$lang.'
                                AND cl.`'.bqSQL(self::$definition['primary']).'` = (
                                    SELECT c.`'.bqSQL(self::$definition['primary']).'`
                                    FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'` c
                                    WHERE c.name = \''.pSQL($key).'\''
                                        .Configuration::sqlRestriction($id_shop_group, $id_shop)
                                .')';
                    $result &= Db::getInstance()->execute($sql);
                }
            }
            // If key does not exists, create it
            else {
                if (!$configID = Configuration::getIdByName($key, $id_shop_group, $id_shop)) {
                    $now = date('Y-m-d H:i:s');
                    $data = array(
                        'id_shop_group' => $id_shop_group ? (int)$id_shop_group : null,
                        'id_shop'       => $id_shop ? (int)$id_shop : null,
                        'name'          => pSQL($key),
                        'value'         => $lang ? null : pSQL($value, $html),
                        'date_add'      => $now,
                        'date_upd'      => $now,
                    );
                    $result &= Db::getInstance()->insert(self::$definition['table'], $data, true);
                    $configID = Db::getInstance()->Insert_ID();
                }

                if ($lang) {
                    $result &= Db::getInstance()->insert(self::$definition['table'].'_lang', array(
                        self::$definition['primary'] => $configID,
                        'id_lang' => (int)$lang,
                        'value' => pSQL($value, $html),
                        'date_upd' => date('Y-m-d H:i:s'),
                    ));
                }
            }
        }

        Configuration::set($key, $values, $id_shop_group, $id_shop);

        return $result;
    }

    /**
     * Delete a configuration key in database (with or without language management)
     *
     * @param string $key Key to delete
     * @return bool Deletion result
     */
    public static function deleteByName($key)
    {
        if (!Validate::isConfigName($key)) {
            return false;
        }

        $result = Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'_lang`
        WHERE `'.bqSQL(self::$definition['primary']).'` IN (
            SELECT `'.bqSQL(self::$definition['primary']).'`
            FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'`
            WHERE `name` = "'.pSQL($key).'"
        )');

        $result2 = Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'`
        WHERE `name` = "'.pSQL($key).'"');

        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;

        return ($result && $result2);
    }

    /**
     * Delete configuration key from current context.
     *
     * @param string $key
     */
    public static function deleteFromContext($key)
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            return;
        }

        $id_shop = null;
        $id_shop_group = Shop::getContextShopGroupID(true);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $id_shop = Shop::getContextShopID(true);
        }

        $id = Configuration::getIdByName($key, $id_shop_group, $id_shop);
        Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'`
        WHERE `'.bqSQL(self::$definition['primary']).'` = '.(int)$id);
        Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.bqSQL(self::$definition['table']).'_lang`
        WHERE `'.bqSQL(self::$definition['primary']).'` = '.(int)$id);

        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;
    }

    /**
     * Check if configuration var is defined in given context
     *
     * @param string $key
     * @param int $id_lang
     * @param int $context
     */
    public static function hasContext($key, $id_lang, $context)
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            $id_shop = $id_shop_group = null;
        } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $id_shop_group = Shop::getContextShopGroupID(true);
            $id_shop = null;
        } else {
            $id_shop_group = Shop::getContextShopGroupID(true);
            $id_shop = Shop::getContextShopID(true);
        }

        if ($context == Shop::CONTEXT_SHOP && Configuration::hasKey($key, $id_lang, null, $id_shop)) {
            return true;
        } elseif ($context == Shop::CONTEXT_GROUP && Configuration::hasKey($key, $id_lang, $id_shop_group)) {
            return true;
        } elseif ($context == Shop::CONTEXT_ALL && Configuration::hasKey($key, $id_lang)) {
            return true;
        }
        return false;
    }

    public static function isOverridenByCurrentContext($key)
    {
        if (Configuration::isLangKey($key)) {
            $testContext = false;
            foreach (Language::getIDs(false) as $id_lang) {
                if ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, $id_lang, Shop::CONTEXT_SHOP))
                    || (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, $id_lang, Shop::CONTEXT_GROUP))) {
                    $testContext = true;
                }
            }
        } else {
            $testContext = ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, null, Shop::CONTEXT_SHOP))
                            || (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, null, Shop::CONTEXT_GROUP))) ? true : false;
        }

        return (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $testContext);
    }

    /**
     * Check if a key was loaded as multi lang
     *
     * @param string $key
     * @return bool
     */
    public static function isLangKey($key)
    {
        return (isset(self::$types[$key]) && self::$types[$key] == 'lang') ? true : false;
    }

    /**
     * Add SQL restriction on shops for configuration table
     *
     * @param int $id_shop_group
     * @param int $id_shop
     * @return string
     */
    protected static function sqlRestriction($id_shop_group, $id_shop)
    {
        if ($id_shop) {
            return ' AND id_shop = '.(int)$id_shop;
        } elseif ($id_shop_group) {
            return ' AND id_shop_group = '.(int)$id_shop_group.' AND (id_shop IS NULL OR id_shop = 0)';
        } else {
            return ' AND (id_shop_group IS NULL OR id_shop_group = 0) AND (id_shop IS NULL OR id_shop = 0)';
        }
    }

    /**
     * This method is override to allow TranslatedConfiguration entity
     *
     * @param $sql_join
     * @param $sql_filter
     * @param $sql_sort
     * @param $sql_limit
     * @return array
     */
    public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
    {
        $query = '
        SELECT DISTINCT main.`'.bqSQL($this->def['primary']).'`
        FROM `'._DB_PREFIX_.bqSQL($this->def['table']).'` main
        '.$sql_join.'
        WHERE id_configuration NOT IN (
            SELECT id_configuration
            FROM '._DB_PREFIX_.bqSQL($this->def['table']).'_lang
        ) '.$sql_filter.'
        '.($sql_sort != '' ? $sql_sort : '').'
        '.($sql_limit != '' ? $sql_limit : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
