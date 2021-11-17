<?php
declare(strict_types = 1);

function requireFileIfItExists(string $filepath) : bool {
    if (file_exists($filepath)) {
        require_once $filepath;
        return true;
    }
    return false;
}

define('_PS_ROOT_DIR_', __DIR__. '/../../../');

// Add module composer autoloader
require_once _PS_ROOT_DIR_ . 'vendor/autoload.php';

// Add PrestaShop composer autoload
define('_PS_ADMIN_DIR_', _PS_ROOT_DIR_ . '/admin-dev/');
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_);

requireFileIfItExists(_PS_ROOT_DIR_ . '/tools/smarty/Smarty.class.php');
require_once _PS_ROOT_DIR_ . '/config/defines.inc.php';
require_once _PS_ROOT_DIR_ . '/config/autoload.php';
require_once _PS_ROOT_DIR_ . '/config/bootstrap.php';
require_once _PS_ROOT_DIR_ . '/install-dev/classes/exception.php';
require_once _PS_ROOT_DIR_ . '/install-dev/classes/session.php';
require_once _PS_ROOT_DIR_ . '/var/SymfonyRequirements.php';

// Make sure loader php-parser is coming from php stan composer

// 1- Use module vendors
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['vendor/nikic/php-parser/lib/PhpParser']);
$loader->register(true);
// 2- Use with Docker container
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['/composer/vendor/nikic/php-parser/lib/PhpParser']);
$loader->register(true);
// 3- Use with PHPStan phar
$loader = new \Composer\Autoload\ClassLoader();
// Contains the vendor in phar, like "phar://phpstan.phar/vendor"
$loader->setPsr4('PhpParser\\', ['phar://' . dirname($_SERVER['PATH_TRANSLATED']) . '/../phpstan/phpstan-shim/phpstan.phar/vendor/nikic/php-parser/lib/PhpParser/']);
$loader->register(true);
// 4- Use phpstan phar with sym link
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['phar://' . realpath($_SERVER['PATH_TRANSLATED']) . '/vendor/nikic/php-parser/lib/PhpParser/']);
$loader->register(true);

// We must declare these constant in this bootstrap script.
// Ignoring the error pattern with this value will throw another error if not found
// during the checks.
$constantsToDefine = [
    '_LEGACY_PS_CREATION_DATE_' => 'string',
    '_LEGACY_PS_CACHE_ENABLED_' => 'bool',
    '_LEGACY_PS_CACHING_SYSTEM_' => 'string',
    '_LEGACY_COOKIE_KEY_' => 'string',
    '_LEGACY_COOKIE_IV_' => 'string',
    '_LEGACY_DB_PREFIX_' => 'string',
    '_LEGACY_DB_SERVER_' => 'string',
    '_LEGACY_DB_USER_' => 'string',
    '_LEGACY_DB_PASSWD_' => 'string',
    '_LEGACY_DB_NAME_' => 'string',
    '_LEGACY_MYSQL_ENGINE_' => 'string',
    '_DB_SERVER_' => 'string',
    '_DB_NAME_' => 'string',
    '_DB_USER_' => 'string',
    '_DB_PASSWD_' => 'string',
    '_MYSQL_ENGINE_' => 'string',
    '_COOKIE_KEY_' => 'string',
    '_COOKIE_IV_' => 'string',
    '_DB_PREFIX_' => 'string',
    '_PS_SSL_PORT_' => 'int',
    '_THEME_NAME_' => 'string',
    '_THEME_COL_DIR_' => 'string',
    '_PARENT_THEME_NAME_' => 'string',
    '__PS_BASE_URI__' => 'string',
    '_PS_API_URL_' => 'string',
    '_PS_INSTALL_PATH_' => 'string',
    '_PS_INSTALL_DATA_PATH_' => 'string',
    '_PS_INSTALL_LANGS_PATH_' => 'string',
    '_PS_INSTALL_FIXTURES_PATH_' => 'string',
    '_PS_INSTALL_VERSION_' => 'string',
    '_PS_INSTALLER_PHP_UPGRADE_DIR_' => 'string',
    '_PS_INSTALLER_SQL_UPGRADE_DIR_' => 'string',
    '_PS_PRICE_DISPLAY_PRECISION_' => 'int',
    '_PS_PRICE_COMPUTE_PRECISION_' => 'string',
    '_PS_OS_CHEQUE_' => 'int',
    '_PS_OS_PAYMENT_' => 'int',
    '_PS_OS_PREPARATION_' => 'int',
    '_PS_OS_SHIPPING_' => 'int',
    '_PS_OS_DELIVERED_' => 'int',
    '_PS_OS_CANCELED_' => 'int',
    '_PS_OS_REFUND_' => 'int',
    '_PS_OS_ERROR_' => 'int',
    '_PS_OS_OUTOFSTOCK_' => 'int',
    '_PS_OS_OUTOFSTOCK_PAID_' => 'int',
    '_PS_OS_OUTOFSTOCK_UNPAID_' => 'int',
    '_PS_OS_BANKWIRE_' => 'int',
    '_PS_OS_PAYPAL_' => 'int',
    '_PS_OS_WS_PAYMENT_' => 'int',
    '_PS_OS_COD_VALIDATION_' => 'int',
    '_PS_THEME_DIR_' => 'string',
    '_PS_BASE_URL_' => 'string',
    '_MODULE_DIR_' => 'string',
    '_THEME_PROD_DIR_' => 'string',
    '_THEME_PROD_PIC_DIR_' => 'string',
    '_NEW_COOKIE_KEY_' => 'string',
    '_MAIL_DIR_' => 'string',
];

foreach ($constantsToDefine as $key => $value) {
    if (!defined($key)) {
        switch ($value) {
            case 'bool':
                define($key, false);
                break;
            case 'int':
                define($key, 1);
                break;
            case 'string':
            default:
                define($key, 'DUMMY_VALUE');
                break;
        }
    }
}

/*
 * At this time if _PS_VERSION_ is still undefined, this is likely because
 * - we are on a old PrestaShop (below 1.7.0.0),
 * - the shop hasn't been installed yet.
 *
 * In that case, the constant can be set from another one in the installation folder.
 */
if (!defined('_PS_VERSION_')) {
    $legacyInstallationFileDefiningConstant = [
        '/install-dev/install_version.php',
        '/install/install_version.php',
    ];
    foreach ($legacyInstallationFileDefiningConstant as $file) {
        if (requireFileIfItExists(_PS_ROOT_DIR_ . $file)) {
            define('_PS_VERSION_', _PS_INSTALL_VERSION_);
            break;
        }
    }
}
