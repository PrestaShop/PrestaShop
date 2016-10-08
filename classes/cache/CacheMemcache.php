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
 * This class requires the PECL Memcache extension
 *
 */
class CacheMemcacheCore extends Cache
{
    /** @var \Memcache */
    protected $memcache;

    /** @var bool Connection status */
    protected $is_connected = false;

    /**
     * CacheMemcacheCore constructor
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * CacheMemcacheCore destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Connect to memcache server
     *
     * @return void
     */
    public function connect()
    {
        if (class_exists('Memcache') && extension_loaded('memcache')) {
            $this->memcache = new Memcache();
        } else {
            return;
        }

        $servers = self::getMemcachedServers();
        if (!$servers) {
            return;
        }
        foreach ($servers as $server) {
            $this->memcache->addServer($server['ip'], $server['port'], true, (int) $server['weight']);
        }

        $this->is_connected = true;
    }

    /**
     * @see Cache::_set()
     */
    protected function _set($key, $value, $ttl = 0)
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcache->set($key, $value, 0, $ttl);
    }

    /**
     * @see Cache::_get()
     */
    protected function _get($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcache->get($key);
    }

    /**
     * @see Cache::_exists()
     */
    protected function _exists($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        return ($this->memcache->get($key) !== false);
    }

    /**
     * @see Cache::_delete()
     */
    protected function _delete($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        return $this->memcache->delete($key);
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

        return $this->memcache->flush();
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
     *
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
            // Get keys (this code comes from Doctrine 2 project)
            $pattern = str_replace('\\*', '.*', preg_quote($key));
            $servers = $this->getMemcachedServers();
            if (is_array($servers) && count($servers) > 0 && method_exists('Memcache', 'getStats')) {
                $allSlabs = $this->memcache->getStats('slabs');
            }

            if (isset($allSlabs) && is_array($allSlabs)) {
                foreach ($allSlabs as $server => $slabs) {
                    if (is_array($slabs)) {
                        foreach (array_keys($slabs) as $i => $slabId) {
                            // $slab_id is not an int but a string, using the key instead ?

                            if (is_int($i)) {
                                $dump = $this->memcache->getStats('cachedump', (int) $i);
                                if ($dump) {
                                    foreach ($dump as $entries) {
                                        if ($entries) {
                                            foreach ($entries as $key => $data) {
                                                if (preg_match('#^'.$pattern.'$#', $key)) {
                                                    $this->_delete($key);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
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

        return $this->memcache->close();
    }

    /**
     * Add a memcache server
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
     * Delete a memcache server
     *
     * @param int $idServer
     *
     * @return bool Indicates whether the server was successfully deleted
     */
    public static function deleteServer($idServer)
    {
        return Db::getInstance()->delete(
            'memcached_servers',
            '`id_memcached_server` = '.(int) $idServer,
            0,
            false
        );
    }
}
