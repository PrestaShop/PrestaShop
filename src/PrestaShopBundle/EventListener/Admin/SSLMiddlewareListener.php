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

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Middleware that is triggered during `kernel.request` event on Symfony routing process, to redirect to HTTPS in some cases.
 *
 * If PS_SSL_ENABLED & (PS_SSL_ENABLED_EVERYWHERE | REFERER is HTTPS)
 * Then redirect to the equivalent URL to HTTPS.
 */
class SSLMiddlewareListener
{
    public function __construct(
        private readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * Registered as `kernel.request` event listener.
     *
     * If the condition needs a redirection to HTTPS, then the current process is interrupted, the headers are sent directly.
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // already SSL, do nothing more
        if ($event->getRequest()->isSecure()) {
            return;
        }

        //If It's Sf route and SSL enabled and forced, redirect to https
        $enabled = (1 === (int) $this->configuration->get('PS_SSL_ENABLED'));
        $forced = (1 === (int) $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE'));
        $serverParams = $event->getRequest()->server;
        $refererSsl = ($serverParams->has('HTTP_REFERER') && str_starts_with($serverParams->get('HTTP_REFERER'), 'https'));

        if ($enabled && ($forced || $refererSsl)) {
            $this->redirectToSsl($event);
        }
    }

    private function redirectToSsl(RequestEvent $event): void
    {
        $status = $event->getRequest()->isMethod('GET') ? 302 : 308;
        $redirect = str_replace('http://', 'https://', $event->getRequest()->getUri());
        $event->setResponse(new RedirectResponse($redirect, $status));
    }
}
