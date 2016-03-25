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
 * This class require a redis server and the Redis PECL extension
 *
 */
class CacheRedis extends Cache
{
    /**
     * @var RedisClient|RedisArray
     */
    protected $redis;

    /**
     * @var RedisParams
     */
    protected $_params = array();
    protected $_servers = array();

    /**
     * @var bool Connection status
     */
    public $is_connected = false;

    /**
     * CachePhpRedis constructor.
     */
    public function __construct()
    {
        if (!extension_loaded('redis')) {
            throw new PrestaShopException('Redis cache has been enabled, but the Redis extension is not available');
        }
        $this->connect();

        if ($this->is_connected) {
            $this->keys = @$this->redis->get(_COOKIE_IV_);
            if (!is_array($this->keys)) {
                $this->keys = array();
            }
        }
    }

    /**
     * CachePhpRedis destructor.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Connect to redis server
     *
     * @return void
     */
    public function connect()
    {
        $this->is_connected = false;
        $this->_servers = self::getRedisServers();

        if (!$this->_servers) {
            return;
        } else {
            if (count($this->_servers) > 1) {
                // Multiple servers, set up redis array
                $hosts = array();
                foreach ($this->_servers as $server) {
                    $hosts[] = $server['ip'].':'.$server['port'];
                }
                $this->redis = new RedisArray($hosts, array('pconnect' => true));
                foreach ($this->_servers as $server) {
                    $instance = $this->redis->_instance($server['ip'].':'.$server['port']);
                    if (!empty($server['auth'])) {
                        if (is_object($instance)) {
                            if ($instance->auth($server['auth'])) {
                                // We're connected as soon as authentication is successful
                                $this->is_connected = true;
                            }
                        }
                    } else {
                        $ping = array_values($this->redis->ping());
                        if(!empty($ping) && $ping[0] === '+PONG') {
                            // We're connected if a connection without +AUTH receives a +PONG
                            $this->is_connected = true;
                        }
                    }
                }
                if (!empty($this->_servers[0]['auth'])) {
                    if (!($this->redis->auth($this->_servers[0]['auth']))) {
                        return;
                    }
                }
            } elseif (count($this->_servers) === 1) {
                $this->redis = new Redis();
                if ($this->redis->pconnect($this->_servers[0]['ip'], $this->_servers[0]['port'])) {
                    $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                    if (!empty($this->_servers[0]['auth'])) {
                        if (!($this->redis->auth($this->_servers[0]['auth']))) {
                            return;
                        }
                    }
                    $this->redis->select($this->_servers[0]['db']);
                    try {
                        $this->redis->select($this->_servers[0]['db']);
                    } catch (Exception $e) {
                        $this->is_connected = false;
                    }
                }
            }
        }
    }

    /**
     * @see Cache::_set()
     *
     * @param string $key Cache key
     * @param mixed $value Value
     * @param int $ttl Time to live in cache
     * @return bool Whether the value could be stored
     */
    protected function _set($key, $value, $ttl = 0)
    {
        // TODO: add TTL support
        if (!$this->is_connected) {
            return false;
        }

        if ((count($this->_servers) > 1)) {
            $this->redis->set($key, $value);
            if (!empty($ret) && is_array($ret) && $ret = array_values($ret)) {
                return $ret[0];
            }
            return false;
        }

        return $this->redis->set($key, $value);
    }

    /**
     * @see Cache::_get()
     *
     * @return bool Value
     */
    protected function _get($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        if ((count($this->_servers) > 1)) {
            $ret = $this->redis->get($key);
            if (!empty($ret) && is_array($ret) && $ret = array_values($ret)) {
                return $ret[0];
            }
            return false;
        }

        return $this->redis->get($key);
    }

    /**
     * @see Cache::_exists()
     *
     * @return bool Whether key exists
     */
    protected function _exists($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        if ((count($this->_servers) > 1)) {
            $ret = $this->redis->exists($key);
            if (!empty($ret) && is_array($ret) && $ret = array_values($ret)) {
                return $ret[0];
            }
            return false;
        }

        return $this->redis->exists($key);
    }

    /**
     * @see Cache::_delete()
     *
     * @return bool Whether key could be deleted
     */
    protected function _delete($key)
    {
        if (!$this->is_connected) {
            return false;
        }

        if ((count($this->_servers) > 1)) {
            $ret = array_values($this->redis->del($key));
            if (!empty($ret) && is_array($ret) && $ret = array_values($ret)) {
                return $ret[0];
            }
            return false;
        }

        return $this->redis->del($key);
    }

    /**
     * @see Cache::_writeKeys()
     *
     * @return bool Whether keys could be written
     */
    protected function _writeKeys()
    {
        if (!$this->is_connected) {
            return false;
        }
        $this->redis->set(_COOKIE_IV_, $this->keys);

        return true;
    }

    /**
     * @see Cache::flush()
     *
     * @return bool
     */
    public function flush()
    {
        if (!$this->is_connected) {
            return false;
        }

        return (bool)$this->redis->flushDB();
    }

    /**
     * Close connection to redis server
     *
     * @return bool Whether closed successfully
     */
    protected function close()
    {
        if (!$this->is_connected) {
            return false;
        }

        // Don't close the connection, needs to be persistent across PHP-sessions
        return true;
    }

    /**
     * Enable pipelining
     * Requires exec() in order to send data to redis
     * because data is now being 'piped' over 1 connection
     *
     * @return bool Whether pipeline could be enabled
     */
    public function pipeline()
    {
        // Cannot enter multi mode if we are using a redis array
        if (count($this->_servers()) > 1) {
            return false;
        }
        return $this->redis->multi(Redis::PIPELINE);
    }

    /**
     * Executes the query
     *
     * Do not use this when not in multi-mode
     *
     * @return bool Whether exec succeeded
     */
    public function exec()
    {
        return $this->redis->exec();
    }

    /**
     * Add a redis server
     *
     * @param string $ip IP address or hostname
     * @param int $port Port number
     * @param string $auth Authentication key
     * @param int $db Redis database ID
     * @return bool Whether the server was successfully added
     * @throws PrestaShopDatabaseException
     */
    public static function addServer($ip, $port, $auth, $db)
    {
        $sql = new DbQuery();
        $sql->select('count(*)');
        $sql->from('redis_servers');
        $sql->where('`ip` = \''.pSQL($ip).'\'');
        $sql->where('`port` = '.(int)$port);
        $sql->where('`auth` = \''.pSQL($auth).'\'');
        $sql->where('`db` = '.(int)$db);
        if (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql, false)) {
            $context = Context::getContext();
            $context->controller->errors[] =
                Tools::displayError('Redis server has already been added');
            return false;
        }

        return Db::getInstance()->insert(
            'redis_servers',
            array(
                'ip' => pSQL($ip),
                'port' => (int)$port,
                'auth' => pSQL($auth),
                'db' => (int)$db
            ),
            false,
            false
        );
    }

    /**
     * Get list of redis server information
     *
     * @return array
     */
    public static function getRedisServers()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('redis_servers');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
    }

    /**
     * Delete a redis server
     *
     * @param int $id_server Server ID
     * @return bool Whether the server was successfully deleted
     */
    public static function deleteServer($id_server)
    {
        return Db::getInstance()->delete(
            'redis_servers',
            '`id_redis_server` = '.(int)$id_server,
            0,
            false
        );
    }
}
