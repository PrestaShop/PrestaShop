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
	 * @var Cache
	 */
	protected static $instance;

	/**
	 * @var array List all keys of cached data and their associated ttl
	 */
	protected $keys = array();

	/**
	 * @var array Store list of tables and their associated keys for SQL cache (warning: this var must not be initialized here !)
	 */
	protected $sql_tables_cached;

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
	 * @return Cache
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			$caching_system = _PS_CACHING_SYSTEM_;
			self::$instance = new $caching_system();

		}
		return self::$instance;
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
		if ($this->_set($key, $value))
		{
			if ($ttl < 0)
				$ttl = 0;

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
		if (!isset($this->keys[$key]))
			return false;

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
		if (!isset($this->keys[$key]))
			return false;

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
		if ($key == '*')
			$keys = $this->keys;
		else if (strpos($key, '*') === false)
			$keys = array($key);
		else
		{
			$pattern = str_replace('\\*', '.*', preg_quote($key));
			foreach ($this->keys as $k => $ttl)
				if (preg_match('#^'.$pattern.'$#', $k))
					$keys[] = $k;
		}

		// Delete keys
		foreach ($keys as $key)
		{
			if (!isset($this->keys[$key]))
				continue;

			if ($this->_delete($key))
				unset($this->keys[$key]);
		}

		$this->_writeKeys();
		return $keys;
	}

	/**
	 * Store a query in cache
	 *
	 * @param string $query
	 * @param array $result
	 */
	public function setQuery($query, $result)
	{
		if ($this->isBlacklist($query))
			return true;

		if (is_null($this->sql_tables_cached))
		{
			$this->sql_tables_cached = $this->get(_COOKIE_IV_.self::SQL_TABLES_NAME);
			if (!is_array($this->sql_tables_cached))
				$this->sql_tables_cached = array();
		}

		// Store query results in cache if this query is not already cached
		$key = md5(_COOKIE_IV_.$query);
		if ($this->exists($key))
			return true;
		$this->set($key, $result);

		// Get all table from the query and save them in cache
		if ($tables = $this->getTables($query))
			foreach ($tables as $table)
				if (!isset($this->sql_tables_cached[$table][$key]))
					$this->sql_tables_cached[$table][$key] = true;
		$this->set(_COOKIE_IV_.self::SQL_TABLES_NAME, $this->sql_tables_cached);
	}

	protected function getTables($string)
	{
		if (preg_match_all('/(?:from|join|update|into)\s+`?('._DB_PREFIX_.'[a-z_-]+)`?(?:,\s{0,}`?('._DB_PREFIX_.'[a-z_-]+)`?)?\s.*/Umsi', $string, $res))
			return array_merge($res[1], $res[2]);
		else
			return false;
	}
	
	/**
	 * Delete a query from cache
	 *
	 * @param string $query
	 */
	public function deleteQuery($query)
	{
		if (is_null($this->sql_tables_cached))
		{
			$this->sql_tables_cached = $this->get(_COOKIE_IV_.self::SQL_TABLES_NAME);
			if (!is_array($this->sql_tables_cached))
				$this->sql_tables_cached = array();
		}

		if ($tables = $this->getTables($query))
			foreach ($tables as $table)
				if (isset($this->sql_tables_cached[$table]))
				{
					foreach (array_keys($this->sql_tables_cached[$table]) as $fs_key)
					{
						$this->delete($fs_key);
						$this->delete($fs_key.'_nrows');
					}
					unset($this->sql_tables_cached[$table]);
				}
		$this->set(_COOKIE_IV_.self::SQL_TABLES_NAME, $this->sql_tables_cached);
	}

	/**
	 * Check if a query contain blacklisted tables
	 *
	 * @param string $query
	 * @return bool
	 */
	protected function isBlacklist($query)
	{
		foreach ($this->blacklist as $find)
			if (strpos($query, $find))
				return true;
		return false;
	}

	public static function store($key, $value)
	{
		Cache::$local[$key] = $value;
	}

	public static function retrieve($key)
	{
		return isset(Cache::$local[$key]) ? Cache::$local[$key] : null;
	}

	public static function retrieveAll()
	{
		return Cache::$local;
	}

	public static function isStored($key)
	{
		return isset(Cache::$local[$key]);
	}

	public static function clean($key)
	{
		if (strpos($key, '*') !== false)
		{
			$regexp = str_replace('\\*', '.*', preg_quote($key, '#'));
			foreach (array_keys(Cache::$local) as $key)
				if (preg_match('#^'.$regexp.'$#', $key))
					unset(Cache::$local[$key]);
		}
		else
			unset(Cache::$local[$key]);
	}

}
