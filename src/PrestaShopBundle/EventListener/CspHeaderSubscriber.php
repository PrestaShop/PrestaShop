<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sends a Content-Security-Policy-Report-Only header with a per-request nonce on
 * every main back-office response, as the first step of the Content Security Policy
 * rollout.
 *
 * The nonce is generated on kernel.request and stored in the main request
 * attributes (as `csp_nonce`) so it is already available while templates render
 * (see CspNonceExtension) — long before the response is built.
 */
final class CspHeaderSubscriber implements EventSubscriberInterface
{
    public const NONCE_ATTRIBUTE = 'csp_nonce';

    public function __construct(
        private readonly string $headerName = 'Content-Security-Policy-Report-Only'
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Early max priority: the nonce must exist before controllers/views render.
            KernelEvents::REQUEST => ['onKernelRequest', 256],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$event->getRequest()->attributes->has(self::NONCE_ATTRIBUTE)) {
            $event->getRequest()->attributes->set(
                self::NONCE_ATTRIBUTE,
                base64_encode(random_bytes(16))
            );
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $nonce = (string) $event->getRequest()->attributes->get(self::NONCE_ATTRIBUTE, '');
        if ($nonce === '') {
            return;
        }

        $event->getResponse()->headers->set(
            $this->headerName,
            sprintf(
                "default-src 'self'; script-src 'self' 'nonce-%s'; style-src 'self' 'unsafe-inline'; font-src 'self' data:; img-src 'self' data:",
                $nonce
            )
        );
    }
}
