<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use Context;
use PrestaShopLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/** Sends the CSP headers on FrontKernel storefront responses (PS_FF_FRONT_CONTAINER_V2); the legacy path is covered in FrontController. */
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
        // Redirects and empty (204/304) responses carry no document to protect, and building a
        // policy for them would run a needless DB query per redirect.
        if ($response->isRedirection() || $response->isEmpty()) {
            return;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        // Skip explicitly non-document responses; an empty Content-Type is kept, since an HTML
        // response not yet through Response::prepare() must not ship without a policy. SVG and
        // XHTML can execute script, so they are covered alongside text/html.
        if ($contentType !== '' && !preg_match('#^(?:text/html|application/xhtml\+xml|image/svg\+xml)#i', $contentType)) {
            return;
        }

        $context = Context::getContext();
        if (null === $context || null === $context->shop || null === $context->link || null === $context->shop->theme) {
            return;
        }

        // A DB error or a throwing module must skip the header, never turn a storefront response into a 500.
        try {
            $reportUri = $context->link->getPageLink('cspreport', null);
            $themeContributions = $context->shop->theme->get('global_settings.csp', []);
            foreach ($this->headerBuilder->build((int) $context->shop->id, $reportUri, is_array($themeContributions) ? $themeContributions : []) as $name => $value) {
                $response->headers->set($name, $value);
            }
        } catch (Throwable $e) {
            try {
                PrestaShopLogger::addLog('CSP header not sent: ' . $e->getMessage(), 2, null, 'Csp');
            } catch (Throwable) {
                error_log('CSP header not sent: ' . $e->getMessage());
            }
        }
    }
}
