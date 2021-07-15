<?php

class CacheMemcached extends CacheMemcachedCore
{
    public function __construct()
    {
        $this->connect();
        if ($this->is_connected) {
            if (php_sapi_name() === 'cli') {
                $key = 'maisonb';
            } else {
                $pos = strpos($_SERVER['SERVER_ADMIN'], '@') + 1;
                $len = min(7, strpos($_SERVER['SERVER_ADMIN'], '.', $pos) - $pos);
                $key = substr($_SERVER['SERVER_ADMIN'], $pos, $len);
            }
            $this->memcached->setOption(Memcached::OPT_PREFIX_KEY, 'ps' . $key);
            if ($this->memcached->getOption(Memcached::HAVE_IGBINARY)) {
                $this->memcached->setOption(Memcached::OPT_SERIALIZER, Memcached::SERIALIZER_IGBINARY);
            }
        }
        $tables = Db::getInstance()->executeS('select * from ps_memcached', true, false);
        foreach ($tables as $table) {
            if (empty($table['expiry'])) {
                $this->blacklist[] = $table['table_name'];
            } else {
                $this->table_expiry_dates[$table['table_name']] = $table['expiry'];
            }
        }
    }

    public function __destruct()
    {
        $this->saveExpiry();
        parent::__destruct();
    }

    /**
     * @param $key
     * @param $value
     * @param float|int $ttl
     *
     * @return bool
     */
    protected function _set($key, $value, $ttl = 3600 * 24)
    {
        if (!$this->is_connected) {
            return false;
        }
        if (class_exists('Profiling', false)) {
            Profiling::beforeQuery();
            $result = $this->memcached->set($key, $value, $ttl);
            Profiling::afterQuery('memcached');
        } else {
            $result = $this->memcached->set($key, $value, $ttl);
        }

        return $result;
    }

    /**
     * @see Cache::_get()
     */
    protected function _get($key)
    {
        if (!$this->is_connected) {
            return false;
        }
        if (class_exists('Profiling', false)) {
            Profiling::beforeQuery();
            $result = $this->memcached->get($key);
            Profiling::afterQuery('memcached');
        } else {
            $result = $this->memcached->get($key);
        }

        return $result;
    }

    /**
     * @see Cache::_exists()
     */
    protected function _exists($key)
    {
        return $this->_get($key) !== false;
    }

    /**
     * @see Cache::_delete()
     */
    protected function _delete($key)
    {
        if (!$this->is_connected) {
            return false;
        }
        if (class_exists('Profiling', false)) {
            Profiling::beforeQuery();
            $result = $this->memcached->delete($key);
            Profiling::afterQuery('memcached');
        } else {
            $result = $this->memcached->delete($key);
        }

        return $result;
    }

    /**
     * Always save the timestamp with the data
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        $value = [time() => $value];

        return $this->_set($key, $value, $ttl);
    }

    /**
     * Simply get the key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $data = $this->_get($key);
        if ($data === false) {
            return false;
        }
        if (!is_array($data) || count($data) != 1) {
            return false;
        }

        return current($data);
    }

    public function saveExpiry()
    {
        static $saved = false;
        if (!$saved && count($this->table_expiry_dates)) {
            $dsn = 'mysql:dbname=' . _DB_NAME_ . ';host=' . _DB_SERVER_;
            $dbo = new PDO($dsn, _DB_USER_, _DB_PASSWD_, [PDO::ATTR_TIMEOUT => 5, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true]);
            $dbo->exec("SET NAMES 'utf8'");
            $inserts = [];
            foreach ($this->table_expiry_dates as $table => $date) {
                $inserts[] = '(' . $dbo->quote($table) . ', ' . (int) $date . ')';
            }
            $query = 'insert into ps_memcached (table_name, expiry) values ' . implode(', ', $inserts) . ' on duplicate key update expiry = greatest(expiry, values(expiry));';
            $dbo->exec($query);
        }
        $saved = true;
    }
}
