<?php

/*
 * This is the bootstrap file to setup required dependencies for Behat tests
 */

$rootDirectory = __DIR__ . '/../../../';

define('_PS_IN_TEST_', true);
require_once $rootDirectory . 'config/config.inc.php';
require_once $rootDirectory . 'app/AppKernel.php';

/*
$rootDirectory = __DIR__ . '/../../../';
define('_PS_IN_TEST_', true);
define('_PS_ROOT_DIR_', __DIR__ . '/..');
define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.'/tests-legacy/resources/modules/');
require_once dirname(__FILE__).'/../../../config/defines.inc.php';
require_once _PS_CONFIG_DIR_.'autoload.php';
require_once dirname(__FILE__).'/../../../config/bootstrap.php';
*/
