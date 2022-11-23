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

require_once __DIR__. '/install_version.php';

// Check PHP version
if ((!defined('PHP_VERSION_ID') || PHP_VERSION_ID < _PS_INSTALL_MINIMUM_PHP_VERSION_ID_) || (PHP_VERSION_ID > _PS_INSTALL_MAXIMUM_PHP_VERSION_ID_) ) {
    echo 'Your server is running PHP ' . PHP_VERSION . ', but PrestaShop requires a PHP version between PHP ' . _PS_INSTALL_MINIMUM_PHP_VERSION_ . ' and PHP ' . _PS_INSTALL_MAXIMUM_PHP_VERSION_ . '.';
    echo PHP_EOL;
    echo 'To install PrestaShop ' . _PS_INSTALL_VERSION_ . ' you need to change your server\'s PHP version.';
    echo PHP_EOL;
    die();
}

/* Redefine REQUEST_URI *//** @phpstan-ignore-next-line */
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
} catch (Throwable $e) {
    echo (string) $e;
}
exit(1);
