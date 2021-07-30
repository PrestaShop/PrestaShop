<?php

abstract class Cache extends CacheCore
{
    public $table_expiry_dates = [];
    protected $blacklist = [
        'ps_cart',
        'ps_cart_cart_rule',
        'ps_cart_product',
        'ps_connections',
        'ps_customer',
        'ps_customer_group',
        'ps_guest',
        'ps_referrer',
        'ps_smarty_lazy_cache',
        'ps_statssearch',
    ];

    /**
     * Save the query result. Hash the query to get the key.
     *
     * @param string $query
     * @param array $result
     * @param null $row_id
     *
     * @return bool
     */
    public function setQuery($query, $result, $row_id = null)
    {
        $tables = $this->getTables($query);
        if ($this->isBlacklist($tables)) {
            return false;
        }
        if (class_exists('Profiling', false)) {
            Profiling::queryStat('memcached', 'cmd_set', $tables);
        }
        $key = $this->getQueryHash($query) . (isset($row_id) ? "_$row_id" : '');
        $this->set($key, $result);
    }

    /**
     * @param $query
     * @param $key
     * @param null $row_id
     *
     * @return bool|mixed
     */
    public function getQuery($query, $key, $row_id = null)
    {
        $tables = $this->getTables($query);
        if ($this->isBlacklist($tables)) {
            return false;
        }
        if (isset($row_id)) {
            $key .= "_$row_id";
        }
        $data = $this->_get($key);
        if ($data === false) {
            if (class_exists('Profiling', false)) {
                Profiling::queryStat('memcached', 'get_miss', $tables);
            }

            return false;
        }
        if (!is_array($data) || count($data) != 1) {
            if (class_exists('Profiling', false)) {
                Profiling::queryStat('memcached', 'get_fail', $tables);
            }

            return false;
        }
        if ($this->isOutdated($query, key($data))) {
            if (class_exists('Profiling', false)) {
                Profiling::queryStat('memcached', 'get_fail', $tables);
            }

            return false;
        }
        if (class_exists('Profiling', false)) {
            Profiling::queryStat('memcached', 'get_ok', $tables);
        }

        return current($data);
    }

    /**
     * We invalidate all queries using these tables.
     *
     * @param string $query
     */
    public function deleteQuery($query)
    {
        $tables = $this->getTables($query);
        if (is_array($tables)) {
            foreach ($tables as $table) {
                if (in_array($table, $this->blacklist)) {
                    continue;
                }
                $this->expireTable($table);
            }
        }
    }

    /**
     * Optimized version. Do not store query results if any blacklisted table is mentioned
     *
     * @param string[] $tables
     *
     * @return bool
     */
    protected function isBlacklist($tables)
    {
        if (empty($tables)) {
            return false;
        }

        return count(array_intersect($this->blacklist, $tables)) > 0;
    }

    protected function isOutdated($query, $timestamp)
    {
        $tables = $this->getTables($query);
        if (empty($tables)) {
            return false;
        }
        foreach ($tables as $table) {
            if (in_array($table, $this->blacklist)) {
                continue;
            }
            if (isset($this->table_expiry_dates[$table])) {
                $expiry = $this->table_expiry_dates[$table];
            }
            if (!empty($expiry) && $timestamp < $expiry) {
                return true;
            }
        }

        return false;
    }

    public function expireTable($table)
    {
        if (class_exists('Profiling', false)) {
            Profiling::queryStat('memcached', 'cmd_purge', [$table]);
        }
        $this->table_expiry_dates[$table] = time() + 1;
    }
}
