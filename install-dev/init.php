<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use Symfony\Component\Dotenv\Dotenv;

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
    define('_PS_CORE_DIR_', realpath(dirname(__FILE__) . '/..'));
}

/* in dev mode - check if composer was executed */
if ((!is_dir(_PS_CORE_DIR_ . DIRECTORY_SEPARATOR . 'vendor') ||
    !file_exists(_PS_CORE_DIR_ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'))) {
    die('Init Install Error : please install <a href="https://getcomposer.org/">composer</a>. Then run "php composer.phar install"');
}

require_once _PS_CORE_DIR_ . '/config/defines.inc.php';
require_once _PS_CORE_DIR_ . '/config/autoload.php';

// Loads .env file from the root of project
$dotEnvFile = dirname(__FILE__, 2) . '/.env';
(new Dotenv())
    // DO NOT use putEnv
    ->usePutenv(false)
    ->loadEnv($dotEnvFile)
;

if (file_exists(_PS_CORE_DIR_ . '/app/config/parameters.php')) {
    require_once _PS_CORE_DIR_ . '/config/bootstrap.php';
}

if (!defined('_THEME_NAME_')) {
    define('_THEME_NAME_', Theme::getDefaultTheme());
}

require_once _PS_CORE_DIR_ . '/config/defines_uri.inc.php';

// Delay kernel instantiation after all the configuration const have been defined, or they won't be usable in the service definitions,
// especially all the URLs constants
if (file_exists(_PS_CORE_DIR_ . '/app/config/parameters.php')) {
    require_once _PS_CORE_DIR_ . '/app/AdminKernel.php';

    global $kernel;
    try {
        $kernel = new AdminKernel(_PS_ENV_, _PS_MODE_DEV_);
        $kernel->boot();
    } catch (DBALException $e) {
        /*
         * Doctrine couldn't be loaded because database settings point to a
         * non existence database
         */
        if (strpos($e->getMessage(), 'You can circumvent this by setting a \'server_version\' configuration value') === false) {
            throw $e;
        }
    } catch (\Symfony\Component\DependencyInjection\Exception\RuntimeException $e) {
        if (strpos($e->getMessage(), 'You have requested a non-existent parameter') === 0) {
            die(sprintf('Error: %s', $e->getMessage())
                . PHP_EOL . 'A missing parameter was detected, which may be caused by old configuration files.'
                . PHP_EOL . 'Try clearing the /var/cache directory and deleting parameters.php and parameters.yml in the /app/config/ directory.'
            );
        }
        throw $e;
    }
}

// Generate common constants
define('PS_INSTALLATION_IN_PROGRESS', true);
define('PS_INSTALLATION_LOCK_FILE', _PS_CORE_DIR_ . '/var/.install.prestashop');
define('_PS_INSTALL_PATH_', dirname(__FILE__) . '/');
define('_PS_INSTALL_DATA_PATH_', _PS_INSTALL_PATH_ . 'data/');
define('_PS_INSTALL_CONTROLLERS_PATH_', _PS_INSTALL_PATH_ . 'controllers/');
define('_PS_INSTALL_MODELS_PATH_', _PS_INSTALL_PATH_ . 'models/');
define('_PS_INSTALL_LANGS_PATH_', _PS_INSTALL_PATH_ . 'langs/');
define('_PS_INSTALL_FIXTURES_PATH_', _PS_INSTALL_PATH_ . 'fixtures/');

// PrestaShop autoload is used to load some helpful classes like Tools.
// Add classes used by installer bellow.

require_once _PS_CORE_DIR_ . '/config/alias.php';
require_once _PS_INSTALL_PATH_ . 'classes/exception.php';
require_once _PS_INSTALL_PATH_ . 'classes/session.php';

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
