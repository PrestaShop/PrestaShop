<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Util\CacheClearLocker;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

require __DIR__ . '/../config/config.inc.php';
define('_PS_APP_ID_', AdminAPIKernel::APP_ID);

//small test to clear cache after upgrade
if (Configuration::get('PS_UPGRADE_CLEAR_CACHE')) {
    header('Cache-Control: max-age=0, must-revalidate');
    header('Expires: Mon, 06 Jun 1985 06:06:00 GMT+1');
    Configuration::updateValue('PS_UPGRADE_CLEAR_CACHE', 0);
}

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/
if (_PS_MODE_DEV_) {
    Debug::enable();
}
require_once __DIR__ . '/../autoload.php';

// Loads .env file from the root of project
$dotEnvFile = dirname(__FILE__, 2) . '/.env';
(new Dotenv())
    // DO NOT use putEnv
    ->usePutenv(false)
    ->loadEnv($dotEnvFile)
;

// Block the process until the cache clear is in progress, this must be done before the kernel is created so it doesn't
// try to use the old container
CacheClearLocker::waitUntilUnlocked(_PS_ENV_, _PS_APP_ID_);

$kernel = new AdminAPIKernel(_PS_ENV_, _PS_MODE_DEV_);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();

/*
 * Initialize legacy dispatcher request at the initial stage of the request. If we don't do it now,
 * the dispatcher could be created later by legacy classes. But, at that point, the request
 * could already be modified, for examply by move_uploaded_file. That would cause createFromGlobals
 * to crash.
 */
Dispatcher::setRequest($request);

Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);

$response = $kernel->handle($request, HttpKernelInterface::MAIN_REQUEST, true);
$response->send();
$kernel->terminate($request, $response);
