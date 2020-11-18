<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
ob_start();

require_once 'install_version.php';

// Set execution time and time_limit to infinite if available
@set_time_limit(0);
@ini_set('max_execution_time', '0');

// setting the memory limit to 256M only if current is lower
$current_memory_limit = psinstall_get_memory_limit();
if ($current_memory_limit > 0 && $current_memory_limit < psinstall_get_octets('256M')) {
    ini_set('memory_limit', '256M');
}

// redefine REQUEST_URI if empty (on some webservers...)
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '') {
    if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
    } else {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    }
}

if ($tmp = strpos($_SERVER['REQUEST_URI'], '?')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
}
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

// we check if theses constants are defined
// in order to use init.php in upgrade.php script
if (!defined('__PS_BASE_URI__')) {
    if (PHP_SAPI !== 'cli') {
        define(
            '__PS_BASE_URI__',
            substr(
                $_SERVER['REQUEST_URI'],
                0,
                -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/'))
                - strlen(
                    substr(
                        dirname($_SERVER['REQUEST_URI']),
                        strrpos(dirname($_SERVER['REQUEST_URI']), '/') + 1
                    )
                )
            )
        );
    } else {
        define('__PS_BASE_URI__', '/' . trim(Datas::getInstance()->base_uri, '/') . '/');
    }
}

if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', realpath(dirname(__FILE__).'/..'));
}

/* in dev mode - check if composer was executed */
if ((!is_dir(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.'vendor') ||
    !file_exists(_PS_CORE_DIR_.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php'))) {
    die('Error : please install <a href="https://getcomposer.org/">composer</a>. Then run "php composer.phar install"');
}

require_once _PS_CORE_DIR_.'/config/defines.inc.php';
require_once _PS_CORE_DIR_.'/config/autoload.php';

if (file_exists(_PS_CORE_DIR_.'/app/config/parameters.php')) {
    require_once _PS_CORE_DIR_.'/config/bootstrap.php';

    if (defined('_PS_IN_TEST_') && _PS_IN_TEST_) {
        $env = 'test';
    } else {
        $env = _PS_MODE_DEV_ ? 'dev' : 'prod';
    }
    global $kernel;
    $kernel = new AppKernel($env, _PS_MODE_DEV_);
    $kernel->loadClassCache();
    $kernel->boot();
}

if (!defined('_THEME_NAME_')) {
    // @see app/config.yml _PS_THEME_NAME default value is "classic".
    if (getenv('PS_THEME_NAME') !== false) {
        define('_THEME_NAME_', getenv('PS_THEME_NAME'));
    } else {
        /**
         * @deprecated since 1.7.5.x to be removed in 1.8.x
         * Rely on "PS_THEME_NAME" environment variable value
         */
        $themes = glob(dirname(__DIR__).'/themes/*/config/theme.yml', GLOB_NOSORT);
        usort($themes, function ($a, $b) {
            return strcmp($b, $a);
        });

        define('_THEME_NAME_', basename(substr($themes[0], 0, -strlen('/config/theme.yml'))));
    }
}

require_once _PS_CORE_DIR_.'/config/defines_uri.inc.php';

// Generate common constants
define('PS_INSTALLATION_IN_PROGRESS', true);
define('_PS_INSTALL_PATH_', dirname(__FILE__).'/');
define('_PS_INSTALL_DATA_PATH_', _PS_INSTALL_PATH_.'data/');
define('_PS_INSTALL_CONTROLLERS_PATH_', _PS_INSTALL_PATH_.'controllers/');
define('_PS_INSTALL_MODELS_PATH_', _PS_INSTALL_PATH_.'models/');
define('_PS_INSTALL_LANGS_PATH_', _PS_INSTALL_PATH_.'langs/');
define('_PS_INSTALL_FIXTURES_PATH_', _PS_INSTALL_PATH_.'fixtures/');

// PrestaShop autoload is used to load some helpful classes like Tools.
// Add classes used by installer bellow.

require_once _PS_CORE_DIR_.'/config/alias.php';
require_once _PS_INSTALL_PATH_.'classes/exception.php';
require_once _PS_INSTALL_PATH_.'classes/session.php';

@set_time_limit(0);
// Work around lack of validation for timezone
// standards conformance, mandatory in PHP 7
if (!in_array(@ini_get('date.timezone'), timezone_identifiers_list())) {
    @date_default_timezone_set('UTC');
    ini_set('date.timezone', 'UTC');
}


function psinstall_get_octets($option)
{
    if (preg_match('/[0-9]+k/i', $option)) {
        return 1024 * (int) $option;
    }

    if (preg_match('/[0-9]+m/i', $option)) {
        return 1024 * 1024 * (int) $option;
    }

    if (preg_match('/[0-9]+g/i', $option)) {
        return 1024 * 1024 * 1024 * (int) $option;
    }

    return $option;
}

function psinstall_get_memory_limit()
{
    $memory_limit = @ini_get('memory_limit');

    if (preg_match('/[0-9]+k/i', $memory_limit)) {
        return 1024 * (int) $memory_limit;
    }

    if (preg_match('/[0-9]+m/i', $memory_limit)) {
        return 1024 * 1024 * (int) $memory_limit;
    }

    if (preg_match('/[0-9]+g/i', $memory_limit)) {
        return 1024 * 1024 * 1024 * (int) $memory_limit;
    }

    return $memory_limit;
}
