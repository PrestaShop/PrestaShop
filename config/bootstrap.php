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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\ContainerBuilder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerAggregate;
use Symfony\Component\Yaml\Yaml;

$container_builder = new ContainerBuilder();
$legacyContainer = $container_builder->build();
ServiceLocator::setServiceContainerInstance($legacyContainer);

if (!file_exists(_PS_CACHE_DIR_)) {
    @mkdir(_PS_CACHE_DIR_);
    $warmer = new CacheWarmerAggregate([
        new PrestaShopBundle\Cache\LocalizationWarmer(_PS_VERSION_, 'en'), //@replace hard-coded Lang
    ]);
    $warmer->warmUp(_PS_CACHE_DIR_);
}

$configDirectory = __DIR__. '/../app/config';
$phpParametersFilepath = $configDirectory . '/parameters.php';
$yamlParametersFilepath = $configDirectory . '/parameters.yml';

$filesystem = new Filesystem();

$exportPhpConfigFile = function ($config, $destination) use ($filesystem) {
    try {
        $filesystem->dumpFile($destination, '<?php return '.var_export($config, true).';'."\n");
    } catch (IOException $e) {
        return false;
    }

    return true;
};

// Bootstrap an application with parameters.yml, which has been installed before PHP parameters file support
if (!file_exists($phpParametersFilepath) && file_exists($yamlParametersFilepath)) {
    $parameters = Yaml::parseFile($yamlParametersFilepath);
    if ($exportPhpConfigFile($parameters, $phpParametersFilepath)) {
        $filesystem->dumpFile($yamlParametersFilepath, 'parameters:' . "\n");
    }
}

$lastParametersModificationTime = (int)@filemtime($phpParametersFilepath);

if ($lastParametersModificationTime) {
    $cachedParameters = _PS_CACHE_DIR_. 'appParameters.php';

    $lastParametersCacheModificationTime = (int)@filemtime($cachedParameters);
    if (!$lastParametersCacheModificationTime || $lastParametersCacheModificationTime < $lastParametersModificationTime) {
        // When parameters file is available, update its cache if it is stale.
        if (file_exists($phpParametersFilepath)) {
            $config = require $phpParametersFilepath;
            $exportPhpConfigFile($config, $cachedParameters);
        } elseif (file_exists($yamlParametersFilepath)) {
            $config = Yaml::parseFile($yamlParametersFilepath);
            $exportPhpConfigFile($config, $cachedParameters);
        }
    }

    $config = require_once _PS_CACHE_DIR_ . 'appParameters.php';
    array_walk($config['parameters'], function (&$param) {
        $param = str_replace('%%', '%', $param);
    });

    $database_host = $config['parameters']['database_host'];

    if (!empty($config['parameters']['database_port'])) {
        $database_host .= ':'. $config['parameters']['database_port'];
    }

    define('_DB_SERVER_', $database_host);
    if (defined('_PS_IN_TEST_')) {
        define('_DB_NAME_', 'test_'.$config['parameters']['database_name']);
    } else {
        define('_DB_NAME_', $config['parameters']['database_name']);
    }

    define('_DB_USER_', $config['parameters']['database_user']);
    define('_DB_PASSWD_', $config['parameters']['database_password']);
    define('_DB_PREFIX_',  $config['parameters']['database_prefix']);
    define('_MYSQL_ENGINE_',  $config['parameters']['database_engine']);
    define('_PS_CACHING_SYSTEM_',  $config['parameters']['ps_caching']);

    if (!defined('PS_IN_UPGRADE') && !defined('_PS_IN_TEST_')) {
        define('_PS_CACHE_ENABLED_', $config['parameters']['ps_cache_enable']);
    } else {
        define('_PS_CACHE_ENABLED_', 0);
        $config['parameters']['ps_cache_enable'] = 0;
    }

    // Legacy cookie
    if (array_key_exists('cookie_key', $config['parameters'])) {
        define('_COOKIE_KEY_', $config['parameters']['cookie_key']);
    } else {
        // Define cookie key if missing to prevent failure in composer post-install script
        define('_COOKIE_KEY_', Tools::passwdGen(56));
    }

    if (array_key_exists('cookie_iv', $config['parameters'])) {
        define('_COOKIE_IV_', $config['parameters']['cookie_iv']);
    } else {
        // Define cookie IV if missing to prevent failure in composer post-install script
        define('_COOKIE_IV_', Tools::passwdGen(32));
    }

    // New cookie
    if (array_key_exists('new_cookie_key', $config['parameters'])) {
        define('_NEW_COOKIE_KEY_', $config['parameters']['new_cookie_key']);
    } else {
        // Define cookie key if missing to prevent failure in composer post-install script
        $key = PhpEncryption::createNewRandomKey();
        define('_NEW_COOKIE_KEY_', $key);
    }

    define('_PS_CREATION_DATE_', $config['parameters']['ps_creation_date']);

    if (isset($config['parameters']['_rijndael_key'], $config['parameters']['_rijndael_iv'])) {
        define('_RIJNDAEL_KEY_', $config['parameters']['_rijndael_key']);
        define('_RIJNDAEL_IV_', $config['parameters']['_rijndael_iv']);
    }
} elseif (file_exists(_PS_ROOT_DIR_.'/config/settings.inc.php')) {
    require_once _PS_ROOT_DIR_.'/config/settings.inc.php';
}
