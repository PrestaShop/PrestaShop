<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShopLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/**
 * Adds the static security headers to Symfony-kernel responses. Registered in both the Front and Admin
 * app containers; the default legacy storefront dispatch is covered by a delegate in FrontController.
 */
final class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SecurityHeadersProvider $provider,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'onKernelResponse'];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        // Static headers must never turn a page into a 500; a broken configuration read is swallowed.
        try {
            foreach ($this->provider->getHeaders($event->getRequest()->isSecure()) as $name => $value) {
                $response->headers->set($name, $value);
            }
        } catch (Throwable $e) {
            try {
                PrestaShopLogger::addLog('Security headers not sent: ' . $e->getMessage(), 2, null, 'SecurityHeaders');
            } catch (Throwable) {
                error_log('Security headers not sent: ' . $e->getMessage());
            }
        }
    }
}
