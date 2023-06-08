<?php

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
     * @return TagAwareAdapter|null
     */
    public static function getInstance()
    {
        static $instance = null;
        if (empty($instance)) {
            $dsn = (defined('_PS_CACHE_DSN_') ? _PS_CACHE_DSN_ : 'filesystem');
            $type = substr($dsn, 0, strpos($dsn, ':'));
            switch ($type) {
                case 'memcached':
                    $adapter = new MemcachedAdapter(MemcachedAdapter::createConnection($dsn), 'init', 3600);
                    break;
                case 'redis':
                    $adapter = new RedisAdapter(RedisAdapter::createConnection($dsn), 'init', 3600);
                    break;
                default:
                    $adapter = new FilesystemAdapter('', 3600, _PS_CACHE_DIR_ . 'init');
            }
            $instance = new TagAwareAdapter($adapter);
        }

        return $instance;
    }
}
