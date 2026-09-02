<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * @deprecated This file will be removed in v10
 *
 * Not used anymore, will be removed in next major version
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (isset(Context::getContext()->controller)) {
    $controller = Context::getContext()->controller;
} else {
    $controller = new FrontController();
    $controller->init();
}
