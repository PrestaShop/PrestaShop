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

declare(strict_types=1);

namespace PrestaShopBundle\Security;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\EventListener\ExternalApiTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Middleware that is triggered during `kernel.request` event on Symfony routing process, to redirect to HTTPS in some cases.
 *
 * If PS_SSL_ENABLED & (PS_SSL_ENABLED_EVERYWHERE | REFERER is HTTPS)
 * Then redirect to the equivalent URL to HTTPS.
 * If the request is an API call, we always redirect to HTTPS and to TLSv1.2+ if we detect a previous version
 */
class SslMiddleware
{
    use ExternalApiTrait;

    private const AVAILABLE_SECURE_PROTOCOLS = ['tls/1.2', 'tls/1.3'];

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Registered as `kernel.request` event listener.
     *
     * If the condition needs a redirection to HTTPS, then the current process is interrupted, the headers are sent directly.
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // already SSL, do nothing more
        if ($this->isSSLrequirementsMet($event->getRequest())) {
            return;
        }

        //If It's an API call and not using https, redirect to https
        if ($this->isExternalApiRequest($event->getRequest()) && !$event->getRequest()->isSecure()) {
            $this->redirectToSsl($event);

            return;
        }

        //If It's an API call and not using TLS 1.2+, display error message
        if ($this->isExternalApiRequest($event->getRequest())) {
            $this->upgradeProtocol($event);

            return;
        }

        //If It's Sf route and SSL enabled and forced, redirect to https
        $enabled = (1 === (int) $this->configuration->get('PS_SSL_ENABLED'));
        $forced = (1 === (int) $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE'));
        $serverParams = $event->getRequest()->server;
        $refererSsl = ($serverParams->has('HTTP_REFERER') && str_starts_with($serverParams->get('HTTP_REFERER'), 'https'));

        if ($enabled && ($forced || $refererSsl)) {
            $this->redirectToSsl($event);
        }
    }

    private function redirectToSsl(RequestEvent $event): void
    {
        $status = $event->getRequest()->isMethod('GET') ? 302 : 308;
        $redirect = str_replace('http://', 'https://', $event->getRequest()->getUri());
        $event->setResponse(new RedirectResponse($redirect, $status));
    }

    private function isSSLrequirementsMet(Request $request): bool
    {
        if ($this->configuration->get('_PS_API_FORCE_TLS_VERSION_') === false) {
            return true;
        }
        if ($this->isExternalApiRequest($request)) {
            return $this->isTLSVersionAccepted($request);
        }

        return $request->isSecure();
    }

    private function isTLSVersionAccepted(Request $request): bool
    {
        // Probably using another webserver than Apache
        // Or the .htaccess is not take in account or has been modified
        if ($request->server->get('SSL_PROTOCOL') === null) {
            return $request->isSecure();
        }

        $protocol = explode('v', $request->server->get('SSL_PROTOCOL'));

        return count($protocol) === 2
            && $protocol[0] === 'TLS'
            && preg_match('/^(1(\.0)?(\.1)?$).*$/', $protocol[1]) === 0
        ;
    }

    private function upgradeProtocol(RequestEvent $event): void
    {
        $event->setResponse(
            new JsonResponse(
                'TLSv1.2 or higher is required.',
                Response::HTTP_UPGRADE_REQUIRED,
                ['Upgrade' => implode(', ', self::AVAILABLE_SECURE_PROTOCOLS)]
            )
        );
    }
}
