<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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
        if (\ToolsCore::usingSecureMode()) {
            return;
        }

        $enabled = (1 == \Configuration::get('PS_SSL_ENABLED'));
        $forced = (1 == \Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $serverParams = $event->getRequest()->server;
        $refererSsl = ($serverParams->has('HTTP_REFERER') && strpos($serverParams->get('HTTP_REFERER'), 'https') === 0);

        if ($enabled && ($forced || $refererSsl)) {
            $this->redirectToSsl($event);
        }
    }

    private function redirectToSsl(GetResponseEvent $event)
    {
        $event->stopPropagation();
        $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header("HTTP/1.1 302 Found");
        header("Location: $redirect");
        exit();
    }
}
