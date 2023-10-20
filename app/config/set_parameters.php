<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShopBundle\Install\Upgrade;

$parametersFilepath = __DIR__  . '/parameters.php';
$parameters = require $parametersFilepath;

if (!array_key_exists('parameters', $parameters)) {
    throw new \Exception('Missing "parameters" key in "parameters.php" configuration file');
}

if (!defined('_PS_IN_TEST_') && isset($_SERVER['argv'])) {
    $input = new \Symfony\Component\Console\Input\ArgvInput();
    $env = $input->getParameterOption(['--env', '-e'], getenv('SYMFONY_ENV') ?: 'dev');

    if ($env === 'test') {
        define('_PS_IN_TEST_', 1);
    }
}

if (isset($container) && $container instanceof \Symfony\Component\DependencyInjection\Container) {
    foreach ($parameters['parameters'] as $key => $value) {
        $container->setParameter($key, $value);
    }

    $driver = 'array';
    $cacheType = [
        'CacheMemcached' => ['memcached'],
        'CacheApc' => ['apcu'],
    ];
    $adapters = [
        'array' => 'cache.adapter.array',
        'memcached' => 'cache.adapter.memcached',
        'apcu' => 'cache.adapter.apcu'
    ];

    if (isset(
            $parameters['parameters']['ps_cache_enable'],
            $parameters['parameters']['ps_caching'],
            $cacheType[$parameters['parameters']['ps_caching']]
        )
        && true === $parameters['parameters']['ps_cache_enable']
    ) {
        foreach ($cacheType[$parameters['parameters']['ps_caching']] as $type) {
            if (extension_loaded($type)) {
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
