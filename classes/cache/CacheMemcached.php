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

/**
 * This class require libmemcached and PECL Memcached extension
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

        // Get keys
        $this->keys = array();
        $servers = self::getMemcachedServers();

        if(is_array($servers) && count($servers) > 0 && method_exists('Memcached', 'getAllKeys')){

            $cle=$this->memcached->getAllKeys();
            if (is_array($cle)) {
                foreach ($cle as $value) {
                    $this->keys[$value] = $this->memcached->get($value);
                }
            }
        }
    }

    public function __destruct()
    {
        $this->quit();
    }

    /**
     * Connect to memcached server
     */
    public function connect()
    {
        if (class_exists('Memcached') && extension_loaded('memcached'))
            $this->memcached = new Memcached('story_pool');
        else
            return false;

        $servers = self::getMemcachedServers();
        if (!$servers)
            return false;

        foreach ($servers as &$key) {
            unset($key['id_memcached_server']);
        }
        $this->memcached->addServers($servers);
        $this->is_connected = true;
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
    protected function _writeKeys()
    {
    }

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
     * Quit connection to memcached server
     *
     * @return bool
     */
    protected function quit()
    {
        if (!$this->is_connected)
            return false;
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
