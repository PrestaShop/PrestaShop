<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\EventListener;

use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * Each Symfony url is automatically tokenized to avoid CSRF fails using XSS failures
 *
 * If token in url is not found or invalid, the user is redirected to a warning page
 */
class TokenizedUrlsListener
{
    const TOKEN_CONTEXT = 'PRESTASHOP';

    private $tokenManager;
    private $router;

    public function __construct(CsrfTokenManager $tokenManager, RouterInterface $router)
    {
        $this->tokenManager = $tokenManager;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $event->getRequest()->get('_route');

        /**
         * every route prefixed by '_' won't be secured
         */
        if (0 === strpos($route, '_')) {
            return;
        }

        $token = urldecode($request->query->get('_token', false));

        if (false === $token || !$this->tokenManager->isTokenValid(new CsrfToken(self::TOKEN_CONTEXT, $token))) {
            $response = new RedirectResponse($this->router->generate('compromised_access'));
            $event->setResponse($response);
        }
    }
}
