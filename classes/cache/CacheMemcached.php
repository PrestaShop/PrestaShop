<?php
/*
* 2015 Raphaël Droz
*/
/**
 * This class require PECL Memcached extension
 *
 */
class CacheMemcachedCore extends Cache
{
	/**
	 * @var Memcached
	 */
	protected $memcached;

	/**
	 * @var bool Connection status
	 */
	protected $is_connected = false;

	public function __construct()
	{
		$this->connect();
		if($this->is_connected)
		{
			$this->memcached->setOption(Memcached::OPT_PREFIX_KEY, _DB_PREFIX_);
			if($this->memcached->getOption(Memcached::HAVE_IGBINARY))
				$this->memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
			$this->keys = array_flip($this->memcached->getAllKeys());
		}

	}

	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Connect to memcached server
	 */
	public function connect()
	{
		if (class_exists('Memcached') && extension_loaded('memcached'))
			$this->memcached = new Memcached();
		else
			return;
		
		$servers = self::getMemcachedServers();
		if (!$servers)
			return;
		foreach ($servers as $server)
			$this->memcached->addServer($server['ip'], $server['port'], (int) $server['weight']);

		$this->is_connected = in_array('255.255.255', $this->memcached->getVersion(), TRUE) === FALSE;
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcached->set($key, $value, $ttl);
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcached->get($key);
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
		return $this->memcached->delete($key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys() { }

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		if (!$this->is_connected)
			return false;
		return $this->memcached->flush();
	}

	/**
	 * Close connection to memcached server
	 *
	 * @return bool
	 */
	protected function close()
	{
		return $this->memcached->quit();
	}

	/**
	 * Add a memcached server
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
	 * Delete a memcached server
	 *
	 * @param int $id_server
	 */
	public static function deleteServer($id_server)
	{
		return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'memcached_servers WHERE id_memcached_server='.(int)$id_server);
	}
}
