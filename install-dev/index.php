<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

require_once 'install_version.php';

if (
    !defined('PHP_VERSION_ID') // PHP_VERSION_ID is available since 5.2.7
    || PHP_VERSION_ID < _PS_INSTALL_MINIMUM_PHP_VERSION_ID_
    || PHP_VERSION_ID > _PS_INSTALL_MAXIMUM_PHP_VERSION_ID_
    || !extension_loaded('SimpleXML') /** @phpstan-ignore-line */
    || !extension_loaded('zip') /** @phpstan-ignore-line */
    || !is_writable(
        __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache'
    )
) {
    require_once dirname(__FILE__).'/missing_requirement.php';
    exit();
}
/** @phpstan-ignore-next-line */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'init.php';
require_once(__DIR__).DIRECTORY_SEPARATOR.'autoload.php';

define('_PS_APP_ID_', AdminKernel::APP_ID);
PrestaShop\PrestaShop\Core\Util\CacheClearLocker::waitUntilUnlocked(_PS_ENV_, _PS_APP_ID_);

try {
    if (_PS_MODE_DEV_) {
        Symfony\Component\ErrorHandler\Debug::enable();
    }

    require_once _PS_INSTALL_PATH_.'classes'.DIRECTORY_SEPARATOR.'controllerHttp.php';
    require_once _PS_INSTALL_PATH_.'classes'.DIRECTORY_SEPARATOR.'HttpConfigureInterface.php';
    InstallControllerHttp::execute();
} catch (PrestashopInstallerException $e) {
    $e->displayMessage();
}
