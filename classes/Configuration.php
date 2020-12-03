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

/**
 * Class ConfigurationCore.
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
    public static $definition = [
        'table' => 'configuration',
        'primary' => 'id_configuration',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 254],
            'id_shop_group' => ['type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'],
            'value' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /** @var array Configuration cache (kept for backward compat) */
    protected static $_cache = null;

    /** @var array Configuration cache with optimised key order */
    protected static $_new_cache_shop = null;
    protected static $_new_cache_group = null;
    protected static $_new_cache_global = null;
    protected static $_initialized = false;

    /** @var array Vars types */
    protected static $types = [];

    protected $webserviceParameters = [
        'fields' => [
            'value' => [],
        ],
    ];

    /**
     * @see ObjectModel::getFieldsLang()
     *
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
     * Return ID a configuration key.
     *
     * @param string $key
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return int Configuration key ID
     */
    public static function getIdByName($key, $idShopGroup = null, $idShop = null)
    {
        if ($idShop === null) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        $sql = 'SELECT `' . bqSQL(self::$definition['primary']) . '`
                FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
                WHERE name = \'' . pSQL($key) . '\'
                ' . Configuration::sqlRestriction($idShopGroup, $idShop);

        return (int) Db::getInstance()->getValue($sql);
    }

    /**
     * Is the configuration loaded.
     *
     * @return bool `true` if configuration is loaded
     */
    public static function configurationIsLoaded()
    {
        return self::$_initialized;
    }

    /**
     * WARNING: For testing only. Do NOT rely on this method, it may be removed at any time.
     *
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
     * Load all configuration data.
     */
    public static function loadConfiguration()
    {
        $sql = 'SELECT c.`name`, cl.`id_lang`, IF(cl.`id_lang` IS NULL, c.`value`, cl.`value`) AS value, c.id_shop_group, c.id_shop
               FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '` c
               LEFT JOIN `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang` cl ON (c.`' . bqSQL(
               self::$definition['primary']
            ) . '` = cl.`' . bqSQL(self::$definition['primary']) . '`)';
        $db = Db::getInstance();
        $results = $db->executeS($sql);
        if ($results) {
            foreach ($results as $row) {
                $lang = ($row['id_lang']) ? $row['id_lang'] : 0;
                self::$types[$row['name']] = (bool) $lang;

                if (!isset(self::$_cache[self::$definition['table']][$lang])) {
                    self::$_cache[self::$definition['table']][$lang] = [
                        'global' => [],
                        'group' => [],
                        'shop' => [],
                    ];
                }

                if ($row['value'] === null) {
                    $row['value'] = '';
                }

                if ($row['id_shop']) {
                    self::$_cache[self::$definition['table']][$lang]['shop'][$row['id_shop']][$row['name']] = $row['value'];
                    self::$_new_cache_shop[$row['name']][$lang][$row['id_shop']] = $row['value'];
                } elseif ($row['id_shop_group']) {
                    self::$_cache[self::$definition['table']][$lang]['group'][$row['id_shop_group']][$row['name']] = $row['value'];
                    self::$_new_cache_group[$row['name']][$lang][$row['id_shop_group']] = $row['value'];
                } else {
                    self::$_cache[self::$definition['table']][$lang]['global'][$row['name']] = $row['value'];
                    self::$_new_cache_global[$row['name']][$lang] = $row['value'];
                }
            }
            self::$_initialized = true;
        }
    }

    /**
     * Get a single configuration value (in one language only).
     *
     * @param string $key Key wanted
     * @param int $idLang Language ID
     *
     * @return string Value
     */
    public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        if (defined('_PS_DO_NOT_LOAD_CONFIGURATION_') && _PS_DO_NOT_LOAD_CONFIGURATION_) {
            return false;
        }

        // Init the cache on demand
        if (!self::$_initialized) {
            Configuration::loadConfiguration();
        }
        $idLang = (int) $idLang;

        if (!self::isLangKey($key)) {
            $idLang = 0;
        }

        if (self::$_new_cache_shop === null) {
            $idShop = 0;
        } else {
            if ($idShop === null || !Shop::isFeatureActive()) {
                $idShop = Shop::getContextShopID(true);
            }
        }

        if (self::$_new_cache_group === null) {
            $idShopGroup = 0;
        } else {
            if ($idShopGroup === null || !Shop::isFeatureActive()) {
                $idShopGroup = Shop::getContextShopGroupID(true);
            }
        }

        if ($idShop && Configuration::hasKey($key, $idLang, null, $idShop)) {
            return self::$_new_cache_shop[$key][$idLang][$idShop];
        } elseif ($idShopGroup && Configuration::hasKey($key, $idLang, $idShopGroup)) {
            return self::$_new_cache_group[$key][$idLang][$idShopGroup];
        } elseif (Configuration::hasKey($key, $idLang)) {
            return self::$_new_cache_global[$key][$idLang];
        }

        return $default;
    }

    /**
     * Get global value.
     *
     * @param string $key Configuration key
     * @param int|null $idLang Language ID
     *
     * @return string
     */
    public static function getGlobalValue($key, $idLang = null)
    {
        return Configuration::get($key, $idLang, 0, 0);
    }

    /**
     * Get a single configuration value (in multiple languages).
     *
     * @param string $key Configuration Key
     * @param int $idShopGroup Shop Group ID
     * @param int $idShop Shop ID
     *
     * @return array Values in multiple languages
     */
    public static function getInt($key, $idShopGroup = null, $idShop = null)
    {
        $resultsArray = [];
        foreach (Language::getIDs() as $idLang) {
            $resultsArray[$idLang] = Configuration::get($key, $idLang, $idShopGroup, $idShop);
        }

        return $resultsArray;
    }

    /**
     * Get a single configuration value for all shops.
     *
     * @param string $key Key wanted
     * @param int $idLang
     *
     * @return array Values for all shops
     */
    public static function getMultiShopValues($key, $idLang = null)
    {
        $shops = Shop::getShops(false, null, true);
        $resultsArray = [];
        foreach ($shops as $idShop) {
            $resultsArray[$idShop] = Configuration::get($key, $idLang, null, $idShop);
        }

        return $resultsArray;
    }

    /**
     * Get several configuration values (in one language only).
     *
     * @throws PrestaShopException
     *
     * @param array $keys Keys wanted
     * @param int $idLang Language ID
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return array Values
     */
    public static function getMultiple($keys, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        if (!is_array($keys)) {
            throw new PrestaShopException('keys var is not an array');
        }

        $idLang = (int) $idLang;
        if ($idShop === null) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        $results = [];
        foreach ($keys as $key) {
            $results[$key] = Configuration::get($key, $idLang, $idShopGroup, $idShop);
        }

        return $results;
    }

    /**
     * Check if key exists in configuration.
     *
     * @param string $key
     * @param int $idLang
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool
     */
    public static function hasKey($key, $idLang = null, $idShopGroup = null, $idShop = null)
    {
        if (!is_int($key) && !is_string($key)) {
            return false;
        }

        $idLang = (int) $idLang;

        if ($idShop) {
            return isset(self::$_new_cache_shop[$key][$idLang][$idShop]);
        } elseif ($idShopGroup) {
            return isset(self::$_new_cache_group[$key][$idLang][$idShopGroup]);
        }

        return isset(self::$_new_cache_global[$key][$idLang]);
    }

    /**
     * Set TEMPORARY a single configuration value (in one language only).
     *
     * @param string $key Configuration key
     * @param mixed $values `$values` is an array if the configuration is multilingual, a single string else
     * @param int $idShopGroup
     * @param int $idShop
     */
    public static function set($key, $values, $idShopGroup = null, $idShop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(Tools::displayError(Context::getContext()->getTranslator()->trans('[%s] is not a valid configuration key', [Tools::htmlentitiesUTF8($key)], 'Admin.Notifications.Error')));
        }

        if ($idShop === null) {
            $idShop = (int) Shop::getContextShopID(true);
        }
        if ($idShopGroup === null) {
            $idShopGroup = (int) Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $lang => $value) {
            if ($idShop) {
                self::$_new_cache_shop[$key][$lang][$idShop] = $value;
                self::$_cache[self::$definition['table']][$lang]['shop'][$idShop][$key] = $value;
            } elseif ($idShopGroup) {
                self::$_new_cache_group[$key][$lang][$idShopGroup] = $value;
                self::$_cache[self::$definition['table']][$lang]['group'][$idShopGroup][$key] = $value;
            } else {
                self::$_new_cache_global[$key][$lang] = $value;
                self::$_cache[self::$definition['table']][$lang]['global'][$key] = $value;
            }
        }
    }

    /**
     * Update configuration key for global context only.
     *
     * @param string $key
     * @param mixed $values
     * @param bool $html
     *
     * @return bool
     */
    public static function updateGlobalValue($key, $values, $html = false)
    {
        return Configuration::updateValue($key, $values, $html, 0, 0);
    }

    /**
     * Update configuration key and value into database (automatically insert if key does not exist).
     *
     * Values are inserted/updated directly using SQL, because using (Configuration) ObjectModel
     * may not insert values correctly (for example, HTML is escaped, when it should not be).
     *
     * @TODO Fix saving HTML values in Configuration model
     *
     * @param string $key Configuration key
     * @param mixed $values $values is an array if the configuration is multilingual, a single string else
     * @param bool $html Specify if html is authorized in value
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return bool Update result
     */
    public static function updateValue($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        if (!Validate::isConfigName($key)) {
            die(Tools::displayError(Context::getContext()->getTranslator()->trans('[%s] is not a valid configuration key', [Tools::htmlentitiesUTF8($key)], 'Admin.Notifications.Error')));
        }

        if ($idShop === null || !Shop::isFeatureActive()) {
            $idShop = Shop::getContextShopID(true);
        }
        if ($idShopGroup === null || !Shop::isFeatureActive()) {
            $idShopGroup = Shop::getContextShopGroupID(true);
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        if ($html) {
            foreach ($values as &$value) {
                $value = Tools::purifyHTML($value);
            }
            unset($value);
        }

        $result = true;
        foreach ($values as $lang => $value) {
            $storedValue = Configuration::get($key, $lang, $idShopGroup, $idShop);
            // if there isn't a $stored_value, we must insert $value
            if ((!is_numeric($value) && $value === $storedValue) || (is_numeric($value) && $value == $storedValue && Configuration::hasKey($key, $lang))) {
                continue;
            }

            // If key already exists, update value
            if (Configuration::hasKey($key, $lang, $idShopGroup, $idShop)) {
                if (!$lang) {
                    // Update config not linked to lang
                    $result &= Db::getInstance()->update(self::$definition['table'], [
                        'value' => pSQL($value, $html),
                        'date_upd' => date('Y-m-d H:i:s'),
                    ], '`name` = \'' . pSQL($key) . '\'' . Configuration::sqlRestriction($idShopGroup, $idShop), 1, true);
                } else {
                    // Update multi lang
                    $sql = 'UPDATE `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang` cl
                            SET cl.value = \'' . pSQL($value, $html) . '\',
                                cl.date_upd = NOW()
                            WHERE cl.id_lang = ' . (int) $lang . '
                                AND cl.`' . bqSQL(self::$definition['primary']) . '` = (
                                    SELECT c.`' . bqSQL(self::$definition['primary']) . '`
                                    FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '` c
                                    WHERE c.name = \'' . pSQL($key) . '\''
                                        . Configuration::sqlRestriction($idShopGroup, $idShop)
                                . ')';
                    $result &= Db::getInstance()->execute($sql);
                }
            } else {
                // If key does not exists, create it
                if (!$configID = Configuration::getIdByName($key, $idShopGroup, $idShop)) {
                    $now = date('Y-m-d H:i:s');
                    $data = [
                        'id_shop_group' => $idShopGroup ? (int) $idShopGroup : null,
                        'id_shop' => $idShop ? (int) $idShop : null,
                        'name' => pSQL($key),
                        'value' => $lang ? null : pSQL($value, $html),
                        'date_add' => $now,
                        'date_upd' => $now,
                    ];
                    $result &= Db::getInstance()->insert(self::$definition['table'], $data, true);
                    $configID = Db::getInstance()->Insert_ID();
                }

                if ($lang) {
                    $table = self::$definition['table'] . '_lang';
                    $selectConfiguration = strtr(
                        'SELECT 1 FROM {{ table }} WHERE id_lang = {{ lang }} ' .
                        'AND `{{ primary_key_column }}` = {{ config_id }}',
                        [
                            '{{ table }}' => _DB_PREFIX_ . $table,
                            '{{ lang }}' => (int) $lang,
                            '{{ primary_key_column }}' => self::$definition['primary'],
                            '{{ config_id }}' => $configID,
                        ]
                    );
                    $results = Db::getInstance()->getRow($selectConfiguration);
                    $configurationExists = is_array($results) && count($results) > 0;
                    $now = date('Y-m-d H:i:s');
                    $sanitizedValue = pSQL($value, $html);

                    if ($configurationExists) {
                        $condition = strtr(
                            '`{{ primary_key_column }}` = {{ config_id }} AND ' .
                            'date_upd = "{{ update_date }}" AND ' .
                            'value = "{{ value }}"',
                            [
                                '{{ primary_key_column }}' => self::$definition['primary'],
                                '{{ config_id }}' => $configID,
                                '{{ update_date }}' => $now,
                                '{{ value }}' => $sanitizedValue,
                            ]
                        );
                        $result &= Db::getInstance()->update($table, [
                            'value' => $sanitizedValue,
                            'date_upd' => date('Y-m-d H:i:s'),
                        ], $condition, 1, true);
                    } else {
                        $result &= Db::getInstance()->insert($table, [
                            self::$definition['primary'] => $configID,
                            'id_lang' => (int) $lang,
                            'value' => $sanitizedValue,
                            'date_upd' => $now,
                        ]);
                    }
                }
            }
        }

        Configuration::set($key, $values, $idShopGroup, $idShop);

        return $result;
    }

    /**
     * Delete a configuration key in database (with or without language management).
     *
     * @param string $key Key to delete
     *
     * @return bool Deletion result
     */
    public static function deleteByName($key)
    {
        if (!Validate::isConfigName($key)) {
            return false;
        }

        $result = Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang`
        WHERE `' . bqSQL(self::$definition['primary']) . '` IN (
            SELECT `' . bqSQL(self::$definition['primary']) . '`
            FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
            WHERE `name` = "' . pSQL($key) . '"
        )');

        $result2 = Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
        WHERE `name` = "' . pSQL($key) . '"');

        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;

        return $result && $result2;
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

        $idShop = null;
        $idShopGroup = Shop::getContextShopGroupID(true);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $idShop = Shop::getContextShopID(true);
        }

        $id = Configuration::getIdByName($key, $idShopGroup, $idShop);
        Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '`
        WHERE `' . bqSQL(self::$definition['primary']) . '` = ' . (int) $id);
        Db::getInstance()->execute('
        DELETE FROM `' . _DB_PREFIX_ . bqSQL(self::$definition['table']) . '_lang`
        WHERE `' . bqSQL(self::$definition['primary']) . '` = ' . (int) $id);

        self::$_cache = null;
        self::$_new_cache_shop = null;
        self::$_new_cache_group = null;
        self::$_new_cache_global = null;
        self::$_initialized = false;
    }

    /**
     * Check if configuration var is defined in given context.
     *
     * @param string $key
     * @param int $idLang
     * @param int $context
     */
    public static function hasContext($key, $idLang, $context)
    {
        if (Shop::getContext() == Shop::CONTEXT_ALL) {
            $idShop = $idShopGroup = null;
        } elseif (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $idShopGroup = Shop::getContextShopGroupID(true);
            $idShop = null;
        } else {
            $idShopGroup = Shop::getContextShopGroupID(true);
            $idShop = Shop::getContextShopID(true);
        }

        if ($context == Shop::CONTEXT_SHOP && Configuration::hasKey($key, $idLang, null, $idShop)) {
            return true;
        } elseif ($context == Shop::CONTEXT_GROUP && Configuration::hasKey($key, $idLang, $idShopGroup)) {
            return true;
        } elseif ($context == Shop::CONTEXT_ALL && Configuration::hasKey($key, $idLang)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isOverridenByCurrentContext($key)
    {
        if (Configuration::isLangKey($key)) {
            $testContext = false;
            foreach (Language::getIDs(false) as $idLang) {
                if ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, $idLang, Shop::CONTEXT_SHOP))
                    || (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, $idLang, Shop::CONTEXT_GROUP))) {
                    $testContext = true;
                }
            }
        } else {
            $testContext = ((Shop::getContext() == Shop::CONTEXT_SHOP && Configuration::hasContext($key, null, Shop::CONTEXT_SHOP))
                            || (Shop::getContext() == Shop::CONTEXT_GROUP && Configuration::hasContext($key, null, Shop::CONTEXT_GROUP))) ? true : false;
        }

        return Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL && $testContext;
    }

    /**
     * Check if a key was loaded as multi lang.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function isLangKey($key)
    {
        return isset(self::$types[$key]) && self::$types[$key];
    }

    /**
     * @return bool
     */
    public static function isCatalogMode()
    {
        if (is_a(Context::getContext()->controller, 'FrontController')) {
            $isCatalogMode =
                Configuration::get('PS_CATALOG_MODE') ||
                !Configuration::showPrices() ||
                (Context::getContext()->controller->getRestrictedCountry() == Country::GEOLOC_CATALOG_MODE);
        } else {
            $isCatalogMode =
                Configuration::get('PS_CATALOG_MODE') ||
                !Configuration::showPrices();
        }

        return $isCatalogMode;
    }

    /**
     * @return bool
     */
    public static function showPrices()
    {
        return Group::isFeatureActive() ? (bool) Group::getCurrent()->show_prices : true;
    }

    /**
     * Add SQL restriction on shops for configuration table.
     *
     * @param int $idShopGroup
     * @param int $idShop
     *
     * @return string
     */
    protected static function sqlRestriction($idShopGroup, $idShop)
    {
        if ($idShop) {
            return ' AND id_shop = ' . (int) $idShop;
        } elseif ($idShopGroup) {
            return ' AND id_shop_group = ' . (int) $idShopGroup . ' AND (id_shop IS NULL OR id_shop = 0)';
        } else {
            return ' AND (id_shop_group IS NULL OR id_shop_group = 0) AND (id_shop IS NULL OR id_shop = 0)';
        }
    }

    /**
     * This method is override to allow TranslatedConfiguration entity.
     *
     * @param string $sqlJoin
     * @param string $sqlFilter
     * @param string $sqlSort
     * @param string $sqlLimit
     *
     * @return array
     */
    public function getWebserviceObjectList($sqlJoin, $sqlFilter, $sqlSort, $sqlLimit)
    {
        $query = '
        SELECT DISTINCT main.`' . bqSQL($this->def['primary']) . '`
        FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '` main
        ' . $sqlJoin . '
        WHERE id_configuration NOT IN (
            SELECT id_configuration
            FROM `' . _DB_PREFIX_ . bqSQL($this->def['table']) . '_lang`
        ) ' . $sqlFilter . '
        ' . ($sqlSort != '' ? $sqlSort : '') . '
        ' . ($sqlLimit != '' ? $sqlLimit : '');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
