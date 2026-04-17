<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/*
 * This is the bootstrap file to setup required dependencies for Behat tests
 */

$rootDirectory = __DIR__ . '/../../../';

const _PS_IN_TEST_ = true;
define('_PS_ROOT_DIR_', dirname(__DIR__, 3));
const _PS_MODULE_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/modules/';
const _PS_ALL_THEMES_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/themes/';
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', __DIR__);
}
require_once $rootDirectory . 'config/config.inc.php';
require_once $rootDirectory . 'app/AppKernel.php';
