<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Core\Util\CacheClearLocker;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

// Set front dir constant to use after
if (!defined('_PS_FRONT_DIR_')) {
    define('_PS_FRONT_DIR_', dirname(__FILE__));
}

// Include some configurations & composer autoload
require_once _PS_FRONT_DIR_ . '/config/config.inc.php';
require_once _PS_FRONT_DIR_ . '/vendor/autoload.php';
define('_PS_APP_ID_', FrontKernel::APP_ID);

// Load .env file from the root of project if present
(new Dotenv(false))->loadEnv(_PS_FRONT_DIR_ . '/.env');

// If we want to use new container access in front (Warning: Experimental feature from now!)
if (isset($_ENV['PS_FF_FRONT_CONTAINER_V2']) && filter_var($_ENV['PS_FF_FRONT_CONTAINER_V2'], \FILTER_VALIDATE_BOOL)) {
    // Activate Symfony's debug if we need it
    if (_PS_MODE_DEV_) {
        Debug::enable();
    }

    // Block the process until the cache clear is in progress, this must be done before the kernel is created so it doesn't
    // try to use the old container
    CacheClearLocker::waitUntilUnlocked(_PS_ENV_, _PS_APP_ID_);

    // Starting Kernel
    $kernel = new FrontKernel(_PS_ENV_, _PS_MODE_DEV_);
    $request = Request::createFromGlobals();

    /*
     * Initialize legacy dispatcher request at the initial stage of the request. If we don't do it now,
     * the dispatcher could be created later by legacy classes. But, at that point, the request
     * could already be modified, for examply by move_uploaded_file. That would cause createFromGlobals
     * to crash.
     */
    Dispatcher::setRequest($request);

    // Try to handle request
    try {
        $response = $kernel->handle($request, HttpKernelInterface::MAIN_REQUEST, false);
        $response->send();
        define('FRONT_LEGACY_CONTEXT', false);
        $kernel->terminate($request, $response);
    } catch (NotFoundHttpException|Exception $exception) {
        // correct Apache charset (except if it's too late)
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }
    }
}

// Prepare and trigger LEGACY front dispatcher
define('FRONT_LEGACY_CONTEXT', true);
Dispatcher::getInstance()->dispatch();
