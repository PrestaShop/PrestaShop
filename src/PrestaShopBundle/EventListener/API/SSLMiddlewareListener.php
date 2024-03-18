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

namespace PrestaShopBundle\EventListener\API;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Middleware that is triggered during `kernel.request` event on Symfony routing process, to trigger error response when
 * proper environment does not meet the requirements.
 *
 * For APi requests we force HTTPs protocol with TLSv1.2+
 */
class SSLMiddlewareListener
{
    private const AVAILABLE_SECURE_PROTOCOLS = ['tls/1.2', 'tls/1.3'];

    public function __construct(
        private readonly ConfigurationInterface $configuration
    ) {
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
        // If constant that forces the check is disabled ignore the protection
        if ($this->configuration->get('_PS_API_FORCE_TLS_VERSION_') === false) {
            return;
        }

        // If protocol is not even HTTPs specify it should be used
        if (!$event->getRequest()->isSecure()) {
            $this->useSecureProtocol($event);
        } elseif (!$this->isTLSVersionAccepted($event->getRequest())) {
            // HTTPs is not enough the proper TLS should also be used, if not it should be upgraded
            $this->upgradeProtocol($event);
        }
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

    private function useSecureProtocol(RequestEvent $event): void
    {
        $redirect = str_replace('http://', 'https://', $event->getRequest()->getUri());
        $event->setResponse(
            new JsonResponse(
                'Use HTTPS protocol',
                Response::HTTP_UNAUTHORIZED,
                ['Location' => $redirect]
            )
        );
    }
}
