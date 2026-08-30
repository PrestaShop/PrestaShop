<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

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
class CspHeaderSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $headerName;

    /**
     * @param string $headerName
     */
    public function __construct($headerName = 'Content-Security-Policy-Report-Only')
    {
        $this->headerName = $headerName;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $nonce = base64_encode(random_bytes(16));
        $event->getRequest()->attributes->set('csp_nonce', $nonce);
        $event->getResponse()->headers->set(
            $this->headerName,
            sprintf(
                "default-src 'self'; script-src 'self' 'nonce-%s'; style-src 'self' 'unsafe-inline'",
                $nonce
            )
        );
    }
}
