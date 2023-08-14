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

use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;

ob_start();
$timerStart = microtime(true);

//	$_GET['tab'] = $_GET['controller'];
//	$_POST['tab'] = $_POST['controller'];
//	$_REQUEST['tab'] = $_REQUEST['controller'];
try {
    $context = Context::getContext();
    if (isset($_GET['logout'])) {
        $context->employee->logout();
    }

    if (!isset($context->employee) || !$context->employee->isLoggedBack()) {
        Tools::redirectAdmin('index.php?controller=AdminLogin&redirect='.$_SERVER['REQUEST_URI']);
    }

    $iso = $context->language->iso_code;

    /* Server Params */
    $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
    $protocol_content = (isset($useSSL) && $useSSL && Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
    $link = new Link($protocol_link, $protocol_content);
    $context->link = $link;
    if (!defined('_PS_BASE_URL_')) {
        define('_PS_BASE_URL_', Tools::getShopDomain(true));
    }
    if (!defined('_PS_BASE_URL_SSL_')) {
        define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
    }

    $path = dirname(__FILE__).'/themes/';
    // if the current employee theme is not valid (check layout.tpl presence),
    // reset to default theme
    if (empty($context->employee->bo_theme) ||
        !file_exists($path.$context->employee->bo_theme.'/template/layout.tpl')) {
        // default admin theme is "default".
        $context->employee->bo_theme = '';
        if (file_exists($path.'default/template/layout.tpl')) {
            $context->employee->bo_theme = 'default';
        } else {
            // if default theme doesn't exists, try to find one, otherwise throw exception
            foreach (scandir($path, SCANDIR_SORT_NONE) as $theme) {
                if ($theme[0] != '.' && file_exists($path.$theme.'/template/layout.tpl')) {
                    $context->employee->bo_theme = $theme;

                    break;
                }
            }
            // if no theme is found, admin can't work.
            if (empty($context->employee->bo_theme)) {
                throw new PrestaShopException('Unable to load theme for employee, and no valid theme found');
            }
        }
        $context->employee->update();
    }

    // Change shop context ?
    if (Shop::isFeatureActive() && Tools::getValue('setShopContext') !== false) {
        $context->cookie->shopContext = Tools::getValue('setShopContext');
        Tools::redirectAdmin(UrlCleaner::cleanUrl($_SERVER['REQUEST_URI'], ['setShopContext']));
    }

    $context->currency = Currency::getDefaultCurrency();

    if ($context->employee->isLoggedBack()) {
        $shop_id = '';
        Shop::setContext(Shop::CONTEXT_ALL);
        if ($context->cookie->shopContext) {
            $split = explode('-', $context->cookie->shopContext);
            if (count($split) == 2) {
                if ($split[0] == 'g') {
                    if ($context->employee->hasAuthOnShopGroup((int) $split[1])) {
                        Shop::setContext(Shop::CONTEXT_GROUP, (int) $split[1]);
                    } else {
                        $shop_id = $context->employee->getDefaultShopID();
                        Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                    }
                } elseif ($context->employee->hasAuthOnShop((int) $split[1])) {
                    $shop_id = $split[1];
                    Shop::setContext(Shop::CONTEXT_SHOP, (int) $shop_id);
                } else {
                    $shop_id = $context->employee->getDefaultShopID();
                    Shop::setContext(Shop::CONTEXT_SHOP, $shop_id);
                }
            }
        }

        // Replace existing shop if necessary
        if (!$shop_id) {
            $context->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
        } elseif ($context->shop->id != $shop_id) {
            $context->shop = new Shop($shop_id);
        }
    }
} catch (PrestaShopException $e) {
    $e->displayMessage();
}
