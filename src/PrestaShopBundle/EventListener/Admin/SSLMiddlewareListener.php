<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Middleware that is triggered during `kernel.request` event on Symfony routing process, to redirect to HTTPS in some cases.
 *
 * If PS_SSL_ENABLED & REFERER is HTTPS
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

        // If It's Sf route and SSL enabled and forced, redirect to https
        $enabled = (1 === (int) $this->configuration->get('PS_SSL_ENABLED'));
        $serverParams = $event->getRequest()->server;
        $refererSsl = ($serverParams->has('HTTP_REFERER') && str_starts_with($serverParams->get('HTTP_REFERER'), 'https'));

        if ($enabled && $refererSsl) {
            $forwardedProto = ($serverParams->has('HTTP_X_FORWARDED_PROTO') && str_starts_with($serverParams->get('HTTP_X_FORWARDED_PROTO'), 'https'));

            if ($forwardedProto) {
                throw new RuntimeException("Infinite redirection detected, please fill in the 'PS_TRUSTED_PROXIES' environment variable or disable the PS_SSL_ENABLED configuration");
            }

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
