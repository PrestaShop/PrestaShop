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

class Tools extends ToolsCore
{
    public static function redirect($url, $base_uri = __PS_BASE_URI__, Link $link = null, $headers = null)
    {
        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $link) {
            if (strpos($url, $base_uri) === 0) {
                $url = substr($url, strlen($base_uri));
            }
            if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0) {
                $url = substr($url, strlen('index.php?controller='));
                if (Configuration::get('PS_REWRITING_SETTINGS')) {
                    $url = static::strReplaceFirst('&', '?', $url);
                }
            }

            $explode = explode('?', $url);
            // don't use ssl if url is home page
            // used when logout for example
            $use_ssl = !empty($url);
            $url = $link->getPageLink($explode[0], $use_ssl);
            if (isset($explode[1])) {
                $url .= '?'.$explode[1];
            }
        }

        // Send additional headers
        if ($headers) {
            if (!is_array($headers)) {
                $headers = array($headers);
            }

            foreach ($headers as $header) {
                header($header);
            }
        }

        Context::getContext()->controller->setRedirectAfter($url);
    }

    public static function getDefaultControllerClass()
    {
        if (isset(Context::getContext()->employee) && Validate::isLoadedObject(Context::getContext()->employee) && isset(Context::getContext()->employee->default_tab)) {
            $default_controller = Tab::getClassNameById((int)Context::getContext()->employee->default_tab);
        }
        if (empty($default_controller)) {
            $default_controller = 'AdminDashboard';
        }
        $controllers = Dispatcher::getControllers(array(_PS_ADMIN_DIR_.'/tabs/', _PS_ADMIN_CONTROLLER_DIR_, _PS_OVERRIDE_DIR_.'controllers/admin/'));
        if (!isset($controllers[strtolower($default_controller)])) {
            $default_controller = 'adminnotfound';
        }
        $controller_class = $controllers[strtolower($default_controller)];

        return $controller_class;
    }

    public static function redirectLink($url)
    {
        if (!preg_match('@^https?://@i', $url)) {
            if (strpos($url, __PS_BASE_URI__) !== false && strpos($url, __PS_BASE_URI__) == 0) {
                $url = substr($url, strlen(__PS_BASE_URI__));
            }
            $explode = explode('?', $url);
            $url = Context::getContext()->link->getPageLink($explode[0]);
            if (isset($explode[1])) {
                $url .= '?'.$explode[1];
            }
        }
    }

    public static function redirectAdmin($url)
    {
        if (!is_object(Context::getContext()->controller)) {
            try {
                $controller = Controller::getController(static::getDefaultControllerClass());
                $controller->setRedirectAfter($url);
                $controller->run();
                Context::getContext()->controller = $controller;
                die();
            } catch (PrestaShopException $e) {
                $e->displayMessage();
            }
        } else {
            Context::getContext()->controller->setRedirectAfter($url);
        }
    }
}
