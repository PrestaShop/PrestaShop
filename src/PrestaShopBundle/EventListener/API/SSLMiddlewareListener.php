<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Middleware that is triggered during `kernel.request` event on Symfony routing process, to trigger error response when
 * proper environment does not meet the requirements.
 *
 * For APi requests we force HTTPs protocol with TLSv1.2+
 */
class SSLMiddlewareListener
{
    private const AVAILABLE_SECURE_PROTOCOLS = ['tls/1.2', 'tls/1.3'];

    private const NOT_PROTECTED_ROUTES = [
        '/^api_doc$/',
        '/^_wdt$/',
        '/^_profiler.*$/',
        '/^_preview_error$/',
    ];

    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly bool $isDebug,
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
        // Only main request are protected, this way sub requests like partial fragments work correctly
        if (!$event->isMainRequest()) {
            return;
        }

        // Some routes don't need to be protected by HTTPs (Swagger doc, profiler and debugging routes, ...)
        $route = $event->getRequest()->attributes->get('_route');
        if (!empty($route)) {
            foreach (self::NOT_PROTECTED_ROUTES as $routePattern) {
                if (preg_match($routePattern, $route)) {
                    return;
                }
            }
        }

        // You can disable the forced secured protocol via a configuration but ONLY in dev mode, prod mode is strictly protected
        if ($this->isDebug && (bool) $this->configuration->get('PS_ADMIN_API_FORCE_DEBUG_SECURED') === false) {
            return;
        }

        // If protocol is not even HTTPs specify it should be used
        if (!$event->getRequest()->isSecure()) {
            $this->useSecureProtocol($event);
        } elseif (!$this->isTLSVersionAccepted($event->getRequest())) {
            // HTTPs is not enough the proper TLS should also be used, if not it should be upgraded
            $this->upgradeProtocol($event);
        }
    }

    private function isTLSVersionAccepted(Request $request): bool
    {
        // Probably using another webserver than Apache
        // Or the .htaccess is not take in account or has been modified
        if ($request->server->get('SSL_PROTOCOL') === null) {
            return $request->isSecure();
        }

        $protocol = explode('v', $request->server->get('SSL_PROTOCOL'));

        return count($protocol) === 2
            && $protocol[0] === 'TLS'
            && preg_match('/^(1(\.0)?(\.1)?$).*$/', $protocol[1]) === 0
        ;
    }

    private function upgradeProtocol(RequestEvent $event): void
    {
        $event->setResponse(
            new JsonResponse(
                'TLSv1.2 or higher is required.',
                Response::HTTP_UPGRADE_REQUIRED,
                ['Upgrade' => implode(', ', self::AVAILABLE_SECURE_PROTOCOLS)]
            )
        );
    }

    private function useSecureProtocol(RequestEvent $event): void
    {
        $redirect = str_replace('http://', 'https://', $event->getRequest()->getUri());
        $event->setResponse(
            new JsonResponse(
                'Use HTTPS protocol',
                Response::HTTP_UNAUTHORIZED,
                ['Location' => $redirect]
            )
        );
    }
}
