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

namespace PrestaShop\PrestaShop\Adapter\Security;

use Configuration;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Tools;

/**
 * Middleware that is triggered during kernel.request event on Symfony routing process, to redirect to HTTPS in some cases.
 *
 * If PS_SSL_ENABLED & (PS_SSL_ENABLED_EVERYWHERE | REFERER is HTTPS)
 * Then redirect to the equivalent URL to HTTPS.
 * Warning : will lost post data,
 */
class SslMiddleware
{
    /**
     * Registered as kernel.request event listener.
     *
     * If the condition needs a redirection to HTTPS, then the current process is interrupted, the headers are sent directly.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // already SSL, do nothing more
        if (Tools::usingSecureMode()) {
            return;
        }

        $enabled = (1 == Configuration::get('PS_SSL_ENABLED'));
        $forced = (1 == Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $serverParams = $event->getRequest()->server;
        $refererSsl = ($serverParams->has('HTTP_REFERER') && strpos($serverParams->get('HTTP_REFERER'), 'https') === 0);

        if ($enabled && ($forced || $refererSsl)) {
            $this->redirectToSsl($event);
        }
    }

    private function redirectToSsl(GetResponseEvent $event)
    {
        $event->stopPropagation();
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 302 Found');
        header("Location: $redirect");
        exit();
    }
}
