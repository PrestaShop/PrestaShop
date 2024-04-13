<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

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
    ) {
    }

    public function onKernelRequest(KernelEvent $event)
    {
        $request = $event->getRequest();

        if (TokenInUrls::isDisabled()) {
            return;
        }

        if (!$event->isMainRequest() || !($event instanceof RequestEvent)) {
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
}
