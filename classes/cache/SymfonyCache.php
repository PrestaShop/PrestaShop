<?php

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class SymfonyCacheCore
{
    /**
     * Choose caching mechanism by defining _PS_CACHE_DSN_ in defines_custom.inc.php.
     * Use a DSN as defined here https://symfony.com/doc/4.4/components/cache/adapters/memcached_adapter.html#configure-the-connection
     * and here https://symfony.com/doc/4.4/components/cache/adapters/redis_adapter.html#configure-the-connection
     *
     * If more than one Prestashop instance uses the same cache mechanism, the configuration must differ:
     * - for memcached, the DSN must include a key prefix (e.g. `memcached://localhost?prefix_key=my_key`)
     * - for redis, the DSN must contain a dbindex (e.g. `redis://my.server.com:6379/20`)
     *
     * @return TagAwareAdapter|null
     */
    public static function getInstance()
    {
        static $instance = null;
        if (empty($instance)) {
            $dsn = (defined('_PS_CACHE_DSN_') ? _PS_CACHE_DSN_ : 'filesystem');
            $ttl = (defined('_PS_CACHE_TTL_') ? _PS_CACHE_TL_ : 3600);
            $prefix_key = (defined('_PS_CACHE_PREFIX_') ? _PS_CACHE_PREFIX_ : '');
            $type = substr($dsn, 0, strpos($dsn, ':'));
            switch ($type) {
                case 'memcached':
                    $adapter = new MemcachedAdapter(MemcachedAdapter::createConnection($dsn), 'conf', $ttl);
                    break;
                case 'redis':
                case 'rediss':
                    $adapter = new RedisAdapter(RedisAdapter::createConnection($dsn), 'conf', $ttl);
                    break;
                default:
                    $adapter = new FilesystemAdapter('', $ttl, _PS_CACHE_DIR_ . 'conf');
            }
            $instance = new TagAwareAdapter(
                new ChainAdapter([
                    new ArrayAdapter($ttl, false),
                    $adapter,
                ]));
        }

        return $instance;
    }
}
