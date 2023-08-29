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
use PrestaShopBundle\Api\Api;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', __DIR__);
}

if (!defined('PS_ADMIN_DIR')) {
    define('PS_ADMIN_DIR', _PS_ADMIN_DIR_);
}

require _PS_ADMIN_DIR_ . '/../config/config.inc.php';

//small test to clear cache after upgrade
if (Configuration::get('PS_UPGRADE_CLEAR_CACHE')) {
    header('Cache-Control: max-age=0, must-revalidate');
    header('Expires: Mon, 06 Jun 1985 06:06:00 GMT+1');
    Configuration::updateValue('PS_UPGRADE_CLEAR_CACHE', 0);
}

// For retrocompatibility with "tab" parameter
if (!isset($_GET['controller']) && isset($_GET['tab'])) {
    $_GET['controller'] = strtolower($_GET['tab']);
}
if (!isset($_POST['controller']) && isset($_POST['tab'])) {
    $_POST['controller'] = strtolower($_POST['tab']);
}
if (!isset($_REQUEST['controller']) && isset($_REQUEST['tab'])) {
    $_REQUEST['controller'] = strtolower($_REQUEST['tab']);
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

$kernel = new AdminKernel(_PS_ENV_, _PS_MODE_DEV_);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_ALL);

$catch = str_contains($request->getRequestUri(), Api::API_BASE_PATH);

try {
    $response = $kernel->handle($request, HttpKernelInterface::MAIN_REQUEST, $catch);
    $response->send();
    $kernel->terminate($request, $response);
    /*
     * @todo during the refacto for getLegacyLayout, this behaviour should be changed, when no route is found`
     *       we should fallback to a common LegacyFallbackController symfony controller, that doesn't end the request
     *       it is responsible for calling the dispatcher and display the legacy controller content inside the new
     *       Symfony layout
     */
} catch (NotFoundHttpException $exception) {
    /** @var RequestStack $requestStack */
    $requestStack = $kernel->getContainer()->get('request_stack');
    // We force pushing the request in the stack again because when kernel detected the exception it popped it out,
    // but we need the request to be accessible, especially to access the session that stores CSRF value for the user
    $requestStack->push($request);

    define('ADMIN_LEGACY_CONTEXT', true);
    // correct Apache charset (except if it's too late)
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }

    // Prepare and trigger LEGACY admin dispatcher
    Dispatcher::getInstance()->dispatch();
    $requestStack->pop();
}
