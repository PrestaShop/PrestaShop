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

// Load .env file from the root of project if present
(new Dotenv(false))->loadEnv(_PS_FRONT_DIR_ . '/.env');

// If we want to use new container access in front (Warning: Experimental feature from now!)
if (isset($_ENV['PS_CONTAINER_V2_FRONT']) && filter_var($_ENV['PS_CONTAINER_V2_FRONT'], \FILTER_VALIDATE_BOOL)) {
    define('_PS_CONTAINER_V2_FRONT_', true);

    // Activate Symfony's debug if we need it
    if (_PS_MODE_DEV_) {
        Debug::enable();
    }

    // Starting Kernel
    $kernel = new FrontKernel(_PS_ENV_, _PS_MODE_DEV_);
    $request = Request::createFromGlobals();

    // Try to handle request
    try {
        $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
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
