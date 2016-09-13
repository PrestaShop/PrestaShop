<?php
/*
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
*  @author Matthieu Deroubaix <matthieu@agence-malttt.fr>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * This class require PHP Redis (PECL) extension
 */

class CacheRedisCore extends Cache
{

	/**
	 * @var Redis
	 */
	protected $redis;
	protected $is_connected = false;

	public function __construct()
	{
       
        $this->is_connected = $this->connect();

        $this->keys = array();
        $this->localcache = array();

        return $this->is_connected;
            
    }

	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Connect to redis server
	 */
	public function connect()
	{
        
		if (class_exists('Redis') && extension_loaded('redis')){
			$this->redis = new Redis();
        } else {
			return false;
        }
       
        if (!defined('_PS_CACHE_REDIS_') || !_PS_CACHE_REDIS_ || !preg_match("/:/",_PS_CACHE_REDIS_)){
            define('_PS_CACHE_REDIS_', '127.0.0.1:6379');
        }
       
        $params = explode(':',_PS_CACHE_REDIS_);
       
        try {

            $result = $this->redis->connect($params[0], $params[1]);
            $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP); 

            return (bool)$result;

        } catch (Exception $e) {

            return false;

        }

	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		
        if (!$this->is_connected){
			return false;
        }

        $this->localcache[$key] = $value;
        
        if ((int)$ttl > 0){
		    return $this->redis->setex($key, (int)$ttl, $value);
        }else{
		    return $this->redis->set($key, $value);
        }

	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{

		if (!$this->is_connected){
            return false;
        }

        if (isset($this->localcache[$key])) {
            return $this->localcache[$key];
        }
        
        $value = $this->redis->get(_DB_NAME_.'.'.$key);
        $this->localcache[$key] = $value;
        
        return $value;

    }

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		return !$this->is_connected ? false : (bool)$this->redis->exists(_DB_NAME_.'.'.$key);
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		return !$this->is_connected ? false : (bool)$this->redis->delete(_DB_NAME_.'.'.$key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
        return $this->is_connected;
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{

		if (!$this->is_connected){
			return false;
        }

        // Case if redis is shared with other apps
        $keys = $this->redis->keys(_DB_NAME_.'.*');
        $this->redis->delete(implode(' ',$keys));
        
        return true;

	}

	/**
	 * Close connection to redis server
	 *
	 * @return bool
	 */
	protected function close()
	{ 
		return !$this->is_connected ? true : (bool)$this->redis->close();
	}

    public function get($key) 
    {
        return !$this->is_connected ? false : (bool)$this->_get($key);
    }

    public function set($key, $value, $ttl = 3600)
    { 
        return !$this->is_connected ? false : (bool)$this->_set($key,$value,$ttl);
    }

    public function exists($key) 
    {
        return !$this->is_connected ? false : (bool)$this->_exists($key);
    }

    public function delete($key)
    {
	   return !$this->is_connected ? false : (bool)$this->_delete($key);
    }

}
