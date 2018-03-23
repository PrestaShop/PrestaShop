<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\EventListener;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Csrf\CsrfToken;
use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;

use Employee;
use Symfony\CS\Tokenizer\Token;
use Tools;

/**
 * Each Symfony url is automatically tokenized to avoid CSRF fails using XSS failures
 *
 * If token in url is not found or invalid, the user is redirected to a warning page
 */
class TokenizedUrlsListener
{
    private $tokenManager;
    private $router;
    private $username;
    private $employeeId;

    public function __construct(
        CsrfTokenManager $tokenManager,
        RouterInterface $router,
        $username,
        LegacyContext $legacyContext
    ) {
        $this->tokenManager = $tokenManager;
        $this->router = $router;
        $this->username = $username;
        $context = $legacyContext->getContext();

        if (!is_null($context)) {
            if ($context->employee instanceof Employee) {
                $this->employeeId = $context->employee->id;
            }
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (TokenInUrls::isEnabled()) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $request->get('_route');
        $uri = $request->getUri();

        /**
         * every route prefixed by '_' won't be secured
         */
        if (
            0 === strpos($route, '_') ||
            0 === strpos($route, 'api_')
        ) {
            return;
        }

        /**
         * every uri which contains 'token' should use the old validation system
         */
        if ($request->query->has('token')) {
            if (0 == strcasecmp(Tools::getAdminToken($this->employeeId), $request->query->get('token'))) {
                return;
            }
        }

        $token = urldecode($request->query->get('_token', false));

        if (false === $token || !$this->tokenManager->isTokenValid(new CsrfToken($this->username, $token))) {
            // remove token if any
            if (false !== strpos($uri, '_token=')) {
                $uri = substr($uri, 0, strpos($uri, '_token='));
            }


            $response = new RedirectResponse($this->router->generate('admin_security_compromised', array('uri' => urlencode($uri))));
            $event->setResponse($response);
        }
    }
}
