<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
define('_PS_IN_TEST_', true);
define('_PS_ROOT_DIR_', dirname(__DIR__, 2));
const _PS_MODULE_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/modules/';
const _PS_ALL_THEMES_DIR_ = _PS_ROOT_DIR_ . '/tests/Resources/themes/';
require_once dirname(__DIR__, 2) . '/vendor/smarty/smarty/libs/functions.php';
require_once dirname(__DIR__, 2) . '/admin-dev/bootstrap.php';

/*
 * Following code makes tests run under phpstorm
 * Else we get error : Class 'PHPUnit_Util_Configuration' not found
 * @see https://stackoverflow.com/questions/33299149/phpstorm-8-and-phpunit-problems-with-runinseparateprocess
 */
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
    define('PHPUNIT_COMPOSER_INSTALL', dirname(__DIR__, 2) . '/vendor/autoload.php');
}
