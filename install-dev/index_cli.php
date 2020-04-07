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

require_once 'install_version.php';

// Check PHP version
if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < _PS_INSTALL_MINIMUM_PHP_VERSION_ID_) {
    die('You need at least PHP '._PS_INSTALL_MINIMUM_PHP_VERSION_.' to install PrestaShop. Your current PHP version is '.PHP_VERSION);
}

/* Redefine REQUEST_URI */
$_SERVER['REQUEST_URI'] = '/install/index_cli.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'classes/datas.php';
/**
 * The autoload needs constant (__PS_BASE_URI__) declared in the init.php
 * to work properly.
 * And, this one can have a custom value depending on what the user specify in arguments.
 *
 * Using getAndCheckArgs is quite redundant because it's also used in controllerConsole,
 * but it prevent a duplicate logic and allows the program to retrieve the base_uri
 * value from the CLI.
 */
Datas::getInstance()->getAndCheckArgs($argv);

require_once dirname(__FILE__).'/init.php';
require_once(__DIR__).DIRECTORY_SEPARATOR.'autoload.php';

try {
    require_once _PS_INSTALL_PATH_.'classes/controllerConsole.php';
    InstallControllerConsole::execute($argc, $argv);
    echo '-- Installation successful! --'."\n";
    exit(0);
} catch (PrestashopInstallerException $e) {
    $e->displayMessage();
} catch (Throwable $t) {
    // Executed only in PHP 7, will not match in PHP 5.
    // Allows `Error` classes to be catched, without throwing an error on PHP 5.
    echo $t->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
exit(1);
