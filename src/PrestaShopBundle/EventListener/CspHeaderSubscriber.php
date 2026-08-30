<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sends a Content-Security-Policy-Report-Only header with a per-request nonce on
 * every main back-office request, as the first step of the Content Security Policy
 * rollout. The nonce is also stored in the request attributes (as `csp_nonce`) so
 * templates can adopt it incrementally before the header becomes enforcing.
 */
final class CspHeaderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $headerName = 'Content-Security-Policy-Report-Only'
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $nonce = base64_encode(random_bytes(16));
        $event->getRequest()->attributes->set('csp_nonce', $nonce);
        $event->getResponse()->headers->set(
            $this->headerName,
            sprintf(
                "default-src 'self'; script-src 'self' 'nonce-%s'; style-src 'self' 'unsafe-inline'; font-src 'self' data:; img-src 'self' data:",
                $nonce
            )
        );
    }
}
