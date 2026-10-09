<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;
use Tools;

/**
 * Adds the static security headers to Symfony-kernel responses. Registered in both the Front and Admin
 * app containers; the default legacy storefront dispatch is covered by a delegate in FrontController.
 * The back-office registration reads the all-shops value ($allShopsScope); the storefront reads the
 * shop being served (current context).
 */
final class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SecurityHeadersProvider $provider,
        private readonly bool $allShopsScope = false,
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

        // The back office is a single global surface; the storefront reads the shop being served.
        $shopConstraint = $this->allShopsScope ? ShopConstraint::allShops() : null;

        // HTTPS detection matches the legacy storefront path (Tools::usingSecureMode): it honours
        // X-Forwarded-Proto/Port, so HSTS is still sent behind a TLS-terminating proxy (Cloudflare,
        // Varnish, a load balancer) without requiring PS_TRUSTED_PROXIES, whereas Request::isSecure()
        // would report the back office as plain HTTP there and drop HSTS.
        // Static headers must never turn a page into a 500; a broken configuration read is swallowed.
        try {
            foreach ($this->provider->getHeaders(Tools::usingSecureMode(), $shopConstraint) as $name => $value) {
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
