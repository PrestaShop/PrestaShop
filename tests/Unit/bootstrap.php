<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;

define('_PS_IN_TEST_', true);
define('_PS_ROOT_DIR_', dirname(__DIR__, 2));
const _PS_MODULE_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/modules/';
const _PS_ALL_THEMES_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/themes/';
require_once dirname(__DIR__, 2) . '/config/defines.inc.php';

// Define themes const
define('_THEME_NAME_', Theme::getDefaultTheme());
define('_PARENT_THEME_NAME_', '');
define('_PS_DEFAULT_THEME_NAME_', Theme::getDefaultTheme());

require_once _PS_CONFIG_DIR_ . 'autoload.php';

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 2) . '/vendor/autoload.php');
}

define('_COOKIE_KEY_', 'cookieKeyValue');
define('_NEW_COOKIE_KEY_', PhpEncryption::createNewRandomKey());

if (!defined('__PS_BASE_URI__')) {
    define('__PS_BASE_URI__', '');
}

// Load MockDb mock for unit tests to avoid database dependency
require_once __DIR__ . '/Classes/Db/MockDb.php';
