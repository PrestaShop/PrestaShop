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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MCachedCore extends Cache
{
	protected $_memcacheObj;
	protected $_isConnected = false;

	protected function __construct()
	{
		parent::__construct();
		return $this->connect();
	}

	public function connect()
	{
		$this->_memcacheObj = new Memcache();
		$servers = self::getMemcachedServers();
		if (!$servers)
			return false;
		foreach ($servers AS $server)
			$this->_memcacheObj->addServer($server['ip'], $server['port'], $server['weight']);

		$this->_isConnected = true;
		return $this->_setKeys();
	}

	public function set($key, $value, $expire = 0)
	{
		if (!$this->_isConnected)
			return false;
		if ($this->_memcacheObj->set($key, $value, 0, $expire))
		{
			$this->_keysCached[$key] = true;
			return $key;
		}
	}
	
	public function setNumRows($key, $value, $expire = 0)
	{
		return $this->set($key.'_nrows', $value, $expire);
	}
	
	public function getNumRows($key)
	{
		return $this->get($key.'_nrows');
	}

	public function get($key)
	{
		if (!isset($this->_keysCached[$key]))
			return false;
		return $this->_memcacheObj->get($key);
	}
	
	protected function _setKeys()
	{
		if (!$this->_isConnected)
			return false;
		$this->_keysCached = $this->_memcacheObj->get('keysCached');
		$this->_tablesCached = $this->_memcacheObj->get('tablesCached');
		
		return true;
	}
	
	public function setQuery($query, $result)
	{
		if (!$this->_isConnected)
			return false;
		if ($this->isBlacklist($query))
			return true;
		$md5_query = md5($query);
		if (isset($this->_keysCached[$md5_query]))
			return true;
		$key = $this->set($md5_query, $result);
		if(preg_match_all('/('._DB_PREFIX_.'[a-z_-]*)`?.*/i', $query, $res))
			foreach($res[1] AS $table)
				if(!isset($this->_tablesCached[$table][$key]))
					$this->_tablesCached[$table][$key] = true;	
	}
	
	public function delete($key, $timeout = 0)
	{
		if (!$this->_isConnected)
			return false;
		if (!empty($key) AND $this->_memcacheObj->delete($key, $timeout))
			unset($this->_keysCached[$key]);
	}

	public function deleteQuery($query)
	{
		if (!$this->_isConnected)
			return false;
		if (preg_match_all('/('._DB_PREFIX_.'[a-z_-]*)`?.*/i', $query, $res))
			foreach ($res[1] AS $table)
				if (isset($this->_tablesCached[$table]))
				{
					foreach ($this->_tablesCached[$table] AS $memcachedKey => $foo)
					{
						$this->delete($memcachedKey);
						$this->delete($memcachedKey.'_nrows');
					}
					unset($this->_tablesCached[$table]);
				}
	}

	protected function close()
	{
		if (!$this->_isConnected)
			return false;
		return $this->_memcacheObj->close();
	}

	public function flush()
	{
		if(!$this->_isConnected)
			return false;
		if ($this->_memcacheObj->flush())
			return $this->_setKeys();
		return false;
	}

	public function __destruct()
	{
		parent::__destruct();
		if (!$this->_isConnected)
			return false;
		$this->_memcacheObj->set('keysCached', $this->_keysCached, 0, 0);
		$this->_memcacheObj->set('tablesCached', $this->_tablesCached, 0, 0);
		$this->close();
	}

	public static function addServer($ip, $port, $weight)
	{
		return Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'memcached_servers (id_memcached_server, ip, port, weight) VALUES(\'\', \''.pSQL($ip).'\', '.(int)$port.', '.(int)$weight.')', false);
	}

	public static function getMemcachedServers()
	{
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'memcached_servers', true, false);
	}

	public static function deleteServer($id_server)
	{
		return Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'memcached_servers WHERE id_memcached_server='.(int)$id_server);
	}
}
