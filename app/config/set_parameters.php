<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
$parametersFilepath = __DIR__ . '/parameters.php';
if (file_exists($parametersFilepath)) {
    $parameters = require $parametersFilepath;
} else {
    // When no parameters file is present (before install) we define null values for mandatory parameters to avoid breaking the CI
    $parameters = [
        'parameters' => [
            'secret' => 'secret',
            'locale' => 'en',
            'database_host' => '',
            'database_port' => null,
            'database_name' => '',
            'database_user' => '',
            'database_password' => '',
            'database_prefix' => 'ps_',
            'api_private_key' => null,
            'api_public_key' => null,
            'cookie_key' => '',
            'new_cookie_key' => null,
            'ps_cache_enable' => null,
            'ps_caching' => null,
        ],
    ];
}

if (!array_key_exists('parameters', $parameters)) {
    throw new Exception('Missing "parameters" key in "parameters.php" configuration file');
}

if (!defined('_PS_IN_TEST_') && isset($_SERVER['argv'])) {
    $input = new Symfony\Component\Console\Input\ArgvInput();
    $env = $input->getParameterOption(['--env', '-e'], getenv('SYMFONY_ENV') ?: 'dev');

    if ($env === 'test') {
        define('_PS_IN_TEST_', 1);
    }
}

if (isset($container) && $container instanceof Symfony\Component\DependencyInjection\Container) {
    foreach ($parameters['parameters'] as $key => $value) {
        $container->setParameter($key, $value);
    }

    $driver = 'array';
    // WHY: the adapter, not the extension, decides whether a driver is usable - Symfony also
    // requires memcached >= 3.1.6 and apc.enabled=1, and its adapters throw from their
    // constructor when that is not met. Picking a driver on extension_loaded() alone makes
    // every Symfony page fail to boot, including the one the merchant would need to turn the
    // caching system back off.
    $cacheType = [
        'CacheMemcached' => ['memcached' => Symfony\Component\Cache\Adapter\MemcachedAdapter::class],
        'CacheApc' => ['apcu' => Symfony\Component\Cache\Adapter\ApcuAdapter::class],
    ];
    $adapters = [
        'array' => 'cache.adapter.array',
        'memcached' => 'cache.adapter.memcached',
        'apcu' => 'cache.adapter.apcu',
    ];

    if (isset(
        $parameters['parameters']['ps_cache_enable'],
        $parameters['parameters']['ps_caching'],
        $cacheType[$parameters['parameters']['ps_caching']]
    )
        && true === $parameters['parameters']['ps_cache_enable']
    ) {
        foreach ($cacheType[$parameters['parameters']['ps_caching']] as $type => $adapterClass) {
            if ($adapterClass::isSupported()) {
                $driver = $type;
                break;
            }
        }
    }
    $container->setParameter('cache.driver', $driver);
    $container->setParameter('cache.adapter', $adapters[$driver]);

    // Parameter used only in dev and test env
    $envParameter = getenv('DISABLE_DEBUG_TOOLBAR');
    if (!isset($parameters['parameters']['use_debug_toolbar']) || false !== $envParameter) {
        $container->setParameter('use_debug_toolbar', !$envParameter);
    }
}
