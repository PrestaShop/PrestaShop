<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use Context;
use Link;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/**
 * Sends the CSP headers on Symfony-kernel responses for one surface. Registered twice: in the Front
 * app container for the storefront (the legacy path is covered by FrontController) and in the Admin
 * app container for the back office. The surface is injected as $cspContext.
 */
final class CspHeaderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CspHeaderBuilder $headerBuilder,
        private readonly CspContext $cspContext,
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

        $psContext = Context::getContext();
        if (null === $psContext || null === $psContext->shop || null === $psContext->link) {
            return;
        }

        $isFront = CspContext::FRONT === $this->cspContext;
        // The storefront policy is per shop and may carry theme contributions; the back office is a
        // single global surface (shop id 0) and has no theme contributions.
        if ($isFront && null === $psContext->shop->theme) {
            return;
        }

        // A DB error or a throwing module must skip the header, never turn a response into a 500.
        try {
            $shopId = $isFront ? (int) $psContext->shop->id : 0;
            $reportUri = $this->reportUri($psContext->link);
            $themeContributions = $isFront ? $psContext->shop->theme->get('global_settings.csp', []) : [];
            foreach ($this->headerBuilder->build($this->cspContext, $shopId, $reportUri, is_array($themeContributions) ? $themeContributions : []) as $name => $value) {
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

    /** The public collector lives on the storefront; a back-office report tags itself with context=admin. */
    private function reportUri(Link $link): string
    {
        $reportUri = $link->getPageLink('cspreport', null);

        if (CspContext::ADMIN === $this->cspContext) {
            $reportUri .= (str_contains($reportUri, '?') ? '&' : '?') . 'context=admin';
        }

        return $reportUri;
    }
}
