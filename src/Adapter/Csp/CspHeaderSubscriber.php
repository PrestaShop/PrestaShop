<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Sends the CSP headers on storefront responses served by the FrontKernel (the experimental
 * PS_FF_FRONT_CONTAINER_V2 path). The default legacy dispatch is covered by a delegate in
 * FrontController; both call the same context-free CspHeaderBuilder. Lives in the Adapter layer
 * because it reads the legacy Context to resolve the current shop and the report endpoint.
 */
final class CspHeaderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CspHeaderBuilder $headerBuilder,
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
        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && !str_contains($contentType, 'text/html')) {
            return;
        }

        $context = Context::getContext();
        if (null === $context || null === $context->shop || null === $context->link) {
            return;
        }

        $reportUri = $context->link->getPageLink('cspreport', null);
        foreach ($this->headerBuilder->build((int) $context->shop->id, $reportUri) as $name => $value) {
            $response->headers->set($name, $value);
        }
    }
}
