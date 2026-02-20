<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Security\Admin\RequestAttributes;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Scheb\TwoFactorBundle\Security\Authorization\Voter\TwoFactorInProgressVoter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\AccessMapInterface;

/**
 * Each Symfony url is automatically tokenized to avoid CSRF fails using XSS failures.
 *
 * If token in url is not found or invalid, the user is redirected to a warning page
 */
class TokenizedUrlsListener
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly UserTokenManager $userTokenManager,
        private readonly AccessMapInterface $map,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMainRequest() || $this->isRequestAnonymous($request) || TokenInUrls::isDisabled()) {
            return;
        }

        $route = $request->get('_route');

        /*
         * every route prefixed by '_' won't be secured
         */
        if (str_starts_with($route, '_') || str_starts_with($route, 'api_')) {
            return;
        }

        if (!$this->userTokenManager->isTokenValid()) {
            // We don't use $request->getUri() because it adds an unwanted / on urls that include index.php
            $uri = $request->getRequestUri();
            // Remove _token/token if any
            $uri = UrlCleaner::cleanUrl($uri, ['_token', 'token']);
            $uri = $request->getSchemeAndHttpHost() . $uri;
            $response = new RedirectResponse($this->router->generate('admin_security_compromised', ['uri' => urlencode($uri)]));
            $event->setResponse($response);
        }
    }

    private function isRequestAnonymous(Request $request): bool
    {
        $publicLegacyRoute = $request->attributes->get(RequestAttributes::ANONYMOUS_CONTROLLER_ATTRIBUTE);
        if ($publicLegacyRoute === true) {
            return true;
        }

        [$attributes] = $this->map->getPatterns($request);
        if (!$attributes) {
            return false;
        }

        // Skip token validation for public routes
        if ([AuthenticatedVoter::PUBLIC_ACCESS] === $attributes) {
            return true;
        }

        // Skip token validation for 2FA routes - they use their own CSRF protection
        // and the user is in a transitional authentication state
        if ([TwoFactorInProgressVoter::IS_AUTHENTICATED_2FA_IN_PROGRESS] === $attributes) {
            return true;
        }

        return false;
    }
}
