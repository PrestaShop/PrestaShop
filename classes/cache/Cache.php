<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

abstract class CacheCore
{
    /**
     * Name of keys index
     */
    const KEYS_NAME = '__keys__';

    /**
     * Name of SQL cache index
     */
    const SQL_TABLES_NAME = 'tablesCached';

    /**
     * Store the number of time a query is fetched from the cache
     *
     * @var array
     */
    protected $queryCounter = array();

    /**
     * @var Cache
     */
    protected static $instance;

    /**
     * Max number of queries cached in memcached, for each SQL table
     */
    protected $maxCachedObjectsByTable = 10000;

    /**
     * If a cache set this variable to true, we need to adjust the size of the table cache object
     *
     * @var bool
     */
    protected $adjustTableCacheSize = false;

    /**
     * @var array List all keys of cached data and their associated ttl
     */
    protected $keys = array();

    /**
     * @var array Store list of tables and their associated keys for SQL cache
     */
    protected $sql_tables_cached = array();

    /**
     * @var array List of blacklisted tables for SQL cache, these tables won't be indexed
     */
    protected $blacklist = array(
        'cart',
        'cart_cart_rule',
        'cart_product',
        'connections',
        'connections_source',
        'connections_page',
        'customer',
        'customer_group',
        'customized_data',
        'guest',
        'pagenotfound',
        'page_viewed',
        'employee',
    );

    /**
     * @var array Store local cache
     */
    protected static $local = array();

    /**
     * Cache a data
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    abstract protected function _set($key, $value, $ttl = 0);

    /**
     * Retrieve a cached data by key
     *
     * @param string $key
     * @return mixed
     */
    abstract protected function _get($key);

    /**
     * Check if a data is cached by key
     *
     * @param string $key
     * @return bool
     */
    abstract protected function _exists($key);

    /**
     * Delete a data from the cache by key
     *
     * @param string $key
     * @return bool
     */
    abstract protected function _delete($key);

    /**
     * Delete multiple keys from the cache
     *
     * @param array $keyArray
     */
    protected function _deleteMulti($keyArray)
    {
        foreach ($keyArray as $key) {
            $this->_delete($key);
        }
    }

    /**
     * Write keys index
     */
    abstract protected function _writeKeys();

    /**
     * Clean all cached data
     *
     * @return bool
     */
    abstract public function flush();

    /**
     * @return mixed
     */
    public function getMaxCachedObjectsByTable()
    {
        return $this->maxCachedObjectsByTable;
    }

    /**
     * @param mixed $maxCachedObjectsByTable
     */
    public function setMaxCachedObjectsByTable($maxCachedObjectsByTable)
    {
        $this->maxCachedObjectsByTable = $maxCachedObjectsByTable;
    }

    /**
     * @return Cache
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $caching_system = _PS_CACHING_SYSTEM_;
            self::$instance = new $caching_system();
        }
        return self::$instance;
    }

    /**
     * Unit testing purpose only
     * @param $test_instance Cache
     */
    public static function setInstanceForTesting($test_instance)
    {
        self::$instance = $test_instance;
    }

    /**
     * @param bool $value
     */
    protected function setAdjustTableCacheSize($value)
    {
        $this->adjustTableCacheSize = (bool)$value;
    }

    /**
     * Unit testing purpose only
     */
    public static function deleteTestingInstance()
    {
        self::$instance = null;
    }

    /**
     * Store a data in cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        if ($this->_set($key, $value, $ttl)) {
            if ($ttl < 0) {
                $ttl = 0;
            }

            $this->keys[$key] = ($ttl == 0) ? 0 : time() + $ttl;
            $this->_writeKeys();
            return true;
        }

        return false;
    }

    /**
     * Retrieve a data from cache
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->keys[$key])) {
            return false;
        }

        return $this->_get($key);
    }

    /**
     * Check if a data is cached
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        if (!isset($this->keys[$key])) {
            return false;
        }

        return $this->_exists($key);
    }

    /**
     * Delete one or several data from cache (* joker can be used)
     * 	E.g.: delete('*'); delete('my_prefix_*'); delete('my_key_name');
     *
     * @param string $key
     * @return array List of deleted keys
     */
    public function delete($key)
    {
        // Get list of keys to delete
        $keys = array();
        if ($key == '*') {
            $keys = $this->keys;
        } elseif (strpos($key, '*') === false) {
            $keys = array($key);
        } else {
            $pattern = str_replace('\\*', '.*', preg_quote($key));
            foreach ($this->keys as $k => $ttl) {
                if (preg_match('#^'.$pattern.'$#', $k)) {
                    $keys[] = $k;
                }
            }
        }

        // Delete keys
        foreach ($keys as $key) {
            if (!isset($this->keys[$key])) {
                continue;
            }

            if ($this->_delete($key)) {
                unset($this->keys[$key]);
            }
        }

        $this->_writeKeys();
        return $keys;
    }

    /**
     * Increment the query counter for the given query
     *
     * @param string $query
     */
    public function incrementQueryCounter($query)
    {
        if (isset($this->queryCounter[$query])) {
            $this->queryCounter[$query]++;
        } else {
            $this->queryCounter[$query] = 1;
        }
    }

    /**
     * Store a query in cache
     *
     * @param string $query
     * @param array  $result
     *
     * @return void
     */
    public function setQuery($query, $result)
    {
        if ($this->isBlacklist($query)) {
            return;
        }

        if (empty($result) || $result === false) {
            $result = array();
        }

        // use the query counter to update the cache statistics
        $this->updateQueryCacheStatistics();

        $key = $this->updateTableToQueryMap($query);

        // Store query results in cache
        // no need to check the key existence before the set : if the query is already
        // in the cache, setQuery is not invoked
        $this->set($key, $result);
    }

    /**
     * Return the hash associated with a query, used to store data into the cache
     *
     * @param string $query
     *
     * @return string
     */
    public function getQueryHash($query)
    {
        return Tools::hashIV($query);
    }

    /**
     * Return the hash associated with a table name, used to store the "table to query hash" map
     *
     * @param string $table
     *
     * @return string
     */
    public function getTableMapCacheKey($table)
    {
        return Tools::hashIV(self::SQL_TABLES_NAME.'_'.$table);
    }

    /**
     * This function extract all the tables involded in a query, and in the each table map the query hash.
     *
     * @param string $query
     *
     * @return string
     */
    private function updateTableToQueryMap($query)
    {
        $key = $this->getQueryHash($query);

        // Get all table from the query and save them in cache
        if ($tables = $this->getTables($query)) {
            foreach ($tables as $table) {
                $this->addQueryKeyToTableMap($key, $table);
            }
        }

        return $key;
    }

    /**
     * Add the given query hash to the table to query key map
     *
     * @param string $key query hash
     * @param string $table table name
     */
    private function addQueryKeyToTableMap($key, $table)
    {
        // the name of the cache entry which cache the table map
        $cacheKey = $this->getTableMapCacheKey($table);

        if (!array_key_exists($table, $this->sql_tables_cached)) {
            $this->sql_tables_cached[$table] = $this->get($cacheKey);
            if (!is_array($this->sql_tables_cached[$table])) {
                $this->sql_tables_cached[$table] = array();
            }
        }


        if (!isset($this->sql_tables_cached[$table][$key])) {
            $this->sql_tables_cached[$table][$key] = 1;
            $this->set($cacheKey, $this->sql_tables_cached[$table]);
            // if the set fail because the object is too big, the adjustTableCacheSize flag is set
            if ($this->adjustTableCacheSize
                || count($this->sql_tables_cached[$table]) > $this->maxCachedObjectsByTable) {
                $this->adjustTableCacheSize($table);
                $this->set($cacheKey, $this->sql_tables_cached[$table]);
            }
        }
    }

    /**
     * Use the query counter to update the query cache statistics
     * So far its only called during a set operation to avoid overloading / slowing down the cache server
     */
    protected function updateQueryCacheStatistics()
    {
        $changedTables = array();

        foreach ($this->queryCounter as $query => $count) {
            $key = $this->getQueryHash($query);

            if ($tables = $this->getTables($query)) {
                foreach ($tables as $table) {
                    if (!array_key_exists($table, $this->sql_tables_cached)) {
                        $this->sql_tables_cached[$table] = $this->get($this->getTableMapCacheKey($table));
                        if (!is_array($this->sql_tables_cached[$table])) {
                            $this->sql_tables_cached[$table] = array();
                        }
                    }

                    if (isset($this->sql_tables_cached[$table][$key])) {
                        $this->sql_tables_cached[$table][$key] += $count;
                        $changedTables[$table] = true;
                    }
                }
            }
        }

        foreach (array_keys($changedTables) as $table) {
            $this->set($this->getTableMapCacheKey($table), $this->sql_tables_cached[$table]);
        }

        $this->queryCounter = array();
    }

    /**
     * Remove the first less used query results from the cache
     *
     * @param $table
     */
    protected function adjustTableCacheSize($table)
    {
        $invalidKeys = array();
        if (isset($this->sql_tables_cached[$table])) {
            // sort the array with the query with the lowest count first
            asort($this->sql_tables_cached[$table], SORT_NUMERIC);
            // reduce the size of the cache : delete the first entries (those with the lowest count)
            $tableBuffer = array_slice($this->sql_tables_cached[$table], 0, ceil($this->maxCachedObjectsByTable/3), true);
            foreach (array_keys($tableBuffer) as $fs_key) {
                $invalidKeys[] = $fs_key;
                $invalidKeys[] = $fs_key.'_nrows';
                unset($this->sql_tables_cached[$table][$fs_key]);
            }
            $this->_deleteMulti($invalidKeys);
        }

        $this->adjustTableCacheSize = false;
    }

    /**
     * Get the tables used in a SQL query
     *
     * @param string $string
     *
     * @return array|bool
     */
    public function getTables($string)
    {
        if (preg_match_all('/(?:from|join|update|into)\s+`?('._DB_PREFIX_.
            '[0-9a-z_-]+)(?:`?\s{0,},\s{0,}`?('._DB_PREFIX_.
            '[0-9a-z_-]+)`?)?(?:`|\s+|\Z)(?!\s*,)/Umsi', $string, $res)) {
            foreach ($res[2] as $table) {
                if ($table != '') {
                    $res[1][] = $table;
                }
            }
            return array_unique($res[1]);
        } else {
            return false;
        }
    }

    /**
     * Delete a query from cache
     *
     * @param string $query
     */
    public function deleteQuery($query)
    {
        if ($this->isBlacklist($query)) {
            return;
        }

        $invalidKeys = array();
        if ($tables = $this->getTables($query)) {
            foreach ($tables as $table) {
                $cacheKey = $this->getTableMapCacheKey($table);

                if (!array_key_exists($table, $this->sql_tables_cached)) {
                    $this->sql_tables_cached[$table] = $this->get($cacheKey);
                    if (!is_array($this->sql_tables_cached[$table])) {
                        $this->sql_tables_cached[$table] = array();
                    }
                }

                if (!empty($this->sql_tables_cached[$table])) {
                    foreach (array_keys($this->sql_tables_cached[$table]) as $fs_key) {
                        $invalidKeys[] = $fs_key;
                        $invalidKeys[] = $fs_key.'_nrows';
                    }
                    unset($this->sql_tables_cached[$table]);
                    $this->_deleteMulti($invalidKeys);
                    $this->_delete($cacheKey);
                }
            }
        }
    }

    /**
     * Check if a query contain blacklisted tables
     *
     * @param string $query
     * @return bool
     */
    protected function isBlacklist($query)
    {
        foreach ($this->blacklist as $find) {
            if (false !== strpos($query, _DB_PREFIX_.$find)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public static function store($key, $value)
    {
        // PHP is not efficient at storing array
        // Better delete the whole cache if there are
        // more than 1000 elements in the array
        if (count(Cache::$local) > 1000) {
            Cache::$local = array();
        }
        Cache::$local[$key] = $value;
    }

    public static function clear()
    {
        Cache::$local = array();
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function retrieve($key)
    {
        return isset(Cache::$local[$key]) ? Cache::$local[$key] : null;
    }

    /**
     * @return array
     */
    public static function retrieveAll()
    {
        return Cache::$local;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function isStored($key)
    {
        return isset(Cache::$local[$key]);
    }

    /**
     * @param string $key
     */
    public static function clean($key)
    {
        if (strpos($key, '*') !== false) {
            $regexp = str_replace('\\*', '.*', preg_quote($key, '#'));
            foreach (array_keys(Cache::$local) as $key) {
                if (preg_match('#^'.$regexp.'$#', $key)) {
                    unset(Cache::$local[$key]);
                }
            }
        } else {
            unset(Cache::$local[$key]);
        }
    }
}
