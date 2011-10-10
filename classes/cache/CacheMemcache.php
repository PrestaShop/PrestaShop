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
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CacheMemcacheCore extends Cache
{
	/**
	 * @var Memcache
	 */
	protected $memcache;

	/**
	 * @var bool Connection status
	 */
	protected $is_connected = false;

	public function __construct()
	{
		$this->connect();
		$this->keys = $this->memcache->get(self::KEYS_NAME);
	}

	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Connect to memcache server
	 */
	public function connect()
	{
		$this->memcache = new Memcache();
		$servers = self::getMemcachedServers();
		if (!$servers)
			return false;
		foreach ($servers AS $server)
			$this->memcache->addServer($server['ip'], $server['port'], $server['weight']);

		$this->is_connected = true;
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->set($key, $value, 0, $ttl);
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->get($key);
	}

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		if (!$this->is_connected)
			return false;
		return isset($this->keys[$key]);
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->delete($key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
		if (!$this->is_connected)
			return false;
		$this->memcache->set(self::KEYS_NAME, $this->keys, 0, 0);
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		if(!$this->is_connected)
			return false;
		if ($this->memcache->flush())
			return $this->_setKeys();
		return false;
	}

	/**
	 * Close connection to memcache server
	 *
	 * @return bool
	 */
	protected function close()
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->close();
	}

	/**
	 * Add a memcache server
	 *
	 * @param string $ip
	 * @param int $port
	 * @param int $weight
	 */
	public static function addServer($ip, $port, $weight)
	{
		return Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'memcached_servers (ip, port, weight) VALUES(\''.pSQL($ip).'\', '.(int)$port.', '.(int)$weight.')', false);
	}

	/**
	 * Get list of memcached servers
	 *
	 * @return array
	 */
	public static function getMemcachedServers()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'memcached_servers', true, false);
	}

	/**
	 * Delete a memcache server
	 *
	 * @param int $id_server
	 */
	public static function deleteServer($id_server)
	{
		return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'memcached_servers WHERE id_memcached_server='.(int)$id_server);
	}
}
