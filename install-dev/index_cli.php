<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'install_version.php';

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

define('_PS_APP_ID_', AdminKernel::APP_ID);
PrestaShop\PrestaShop\Core\Util\CacheClearLocker::waitUntilUnlocked(_PS_ENV_, _PS_APP_ID_);

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
