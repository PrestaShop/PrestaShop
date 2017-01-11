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

    /**
     * CacheMemcachedCore constructor
     */
    public function __construct()
    {
        $this->connect();
        if ($this->is_connected) {
            $this->memcached->setOption(Memcached::OPT_PREFIX_KEY, _DB_PREFIX_);
            if ($this->memcached->getOption(Memcached::HAVE_IGBINARY)) {
                $this->memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
            }
        }
    }

    /**
     * CacheMemcachedCore destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Connect to memcached server
     */
    public function connect()
    {
        if (class_exists('Memcached') && extension_loaded('memcached')) {
            $this->memcached = new Memcached();
        } else {
            return;
        }
        
        $servers = self::getMemcachedServers();
        if (!$servers) {
            return;
        }
        foreach ($servers as $server) {
            $this->memcached->addServer($server['ip'], $server['port'], (int) $server['weight']);
        }

        $this->is_connected =  in_array('255.255.255', $this->memcached->getVersion(), true) === false;
    }

    /**
     * @see Cache::_set()
     */
    protected function _set($key, $value, $ttl = 0)
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->set($key, $value, $ttl);
    }

    /**
     * @see Cache::_get()
     */
    protected function _get($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->get($key);
    }

    /**
     * @see Cache::_exists()
     */
    protected function _exists($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        return ($this->memcached->get($key) !== false);
    }

    /**
     * @see Cache::_delete()
     */
    protected function _delete($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->delete($key);
    }

    /**
     * @see Cache::_writeKeys()
     */
    protected function _writeKeys()
    {
        if (!$this->is_connected) {
            return false;
        }

        return true;
    }

    /**
     * @see Cache::flush()
     */
    public function flush()
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->flush();
    }

    /**
     * Store a data in cache
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        return $this->_set($key, $value, $ttl);
    }

    /**
     * Retrieve a data from cache
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->_get($key);
    }

    /**
     * Check if a data is cached
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return $this->_exists($key);
    }

    /**
     * Delete one or several data from cache (* joker can be used, but avoid it !)
     * E.g.: delete('*'); delete('my_prefix_*'); delete('my_key_name');
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        if ($key == '*') {
            $this->flush();
        } elseif (strpos($key, '*') === false) {
            $this->_delete($key);
        } else {
            $pattern = str_replace('\\*', '.*', preg_quote($key));
            $keys = $this->memcached->getAllKeys();
            foreach ($keys as $key => $data) {
                if (preg_match('#^'.$pattern.'$#', $key)) {
                    $this->_delete($key);
                }
            }
        }

        return true;
    }

    /**
     * Close connection to memcache server
     *
     * @return bool
     */
    protected function close()
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcached->quit();
    }

    /**
     * Add a memcached server
     *
     * @param string $ip
     * @param int    $port
     * @param int    $weight
     *
     * @return bool Indicates whether the server was successfully added
     */
    public static function addServer($ip, $port, $weight)
    {
        return Db::getInstance()->insert(
            'memcached_servers',
            array(
                'ip' => pSQL($ip),
                'port' => (int) $port,
                'weight' => (int) $weight,
            ),
            false,
            false
        );
    }

    /**
     * Get list of memcached servers
     *
     * @return array
     */
    public static function getMemcachedServers()
    {
        $sql = new DbQuery();
        $sql->select('ms.*');
        $sql->from('memcached_servers', 'ms');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
    }

    /**
     * Delete a memcached server
     *
     * @param int $idServer
     *
     * @return bool Indicates whether the server was successfully deleted
     */
    public static function deleteServer($idServer)
    {
        return Db::getInstance()->delete(
            'memcached_servers',
            '`id_memcached_server` = '.(int) $idServer
        );
    }
}
