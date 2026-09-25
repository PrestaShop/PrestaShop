<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopBundle\Routing\Converter\LegacyUrlConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Converts any legacy url into a migrated Symfony url (if it exists) and redirect to it.
 */
class LegacyUrlListener
{
    /**
     * @var LegacyUrlConverter
     */
    private $converter;

    /**
     * @param LegacyUrlConverter $converter
     */
    public function __construct(LegacyUrlConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        try {
            $convertedUrl = $this->converter->convertByRequest($event->getRequest());
        } catch (CoreException) {
            $this->redirectToCanonicalLegacyUrl($event);

            return;
        }

        $event->setResponse(new RedirectResponse($convertedUrl, Response::HTTP_PERMANENTLY_REDIRECT));
    }

    /**
     * A page that has not been migrated builds its links relative to `index.php`, so they only
     * resolve while the URL is `index.php` itself. Reached as `index.php/` the browser reads the
     * trailing slash as a directory and resolves every one of those links one level deeper, which
     * is where the second `index.php` in the reported 404 comes from. Both URLs serve the same
     * page - `/` matches admin_homepage, which falls back to the legacy controller - so the fix is
     * to send the request to the form whose links work rather than to let the page emit broken ones.
     */
    private function redirectToCanonicalLegacyUrl(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Without a legacy controller this is the back office homepage, which is a route of its own
        // and must keep working at that URL.
        if (!$request->query->has('controller')) {
            return;
        }

        // Only a URL that carries the front controller in its path can hold the spurious slash.
        // Reached as a directory instead - which is how the back office is normally served - the
        // trailing slash is the directory's own and the web server answers the stripped form with a
        // redirect back to it, so removing it here would loop until the browser gives up.
        $baseUrl = $request->getBaseUrl();
        if (!str_ends_with($baseUrl, '/' . basename($request->getScriptName()))) {
            return;
        }

        // getPathInfo() answers "/" for index.php and for index.php/ alike, so it cannot tell the
        // two apart and using it here redirects the canonical URL to itself. The raw path can:
        // only the uncanonical form carries the trailing slash after the script name.
        $path = parse_url($request->getRequestUri(), PHP_URL_PATH);
        if ($baseUrl . '/' !== $path) {
            return;
        }

        $queryString = $request->getQueryString();
        $canonicalUrl = $baseUrl . (null !== $queryString ? '?' . $queryString : '');

        // 308 rather than 301 so a form posted to the uncanonical URL keeps its method and body.
        $event->setResponse(new RedirectResponse($canonicalUrl, Response::HTTP_PERMANENTLY_REDIRECT));
    }
}
