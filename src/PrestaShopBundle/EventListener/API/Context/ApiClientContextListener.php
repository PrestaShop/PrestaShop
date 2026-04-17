<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API\Context;

use PrestaShop\PrestaShop\Core\Context\ApiClientContextBuilder;
use PrestaShop\PrestaShop\Core\Security\OAuth2\JwtTokenUser;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

/**
 * Listener dedicated to set up ApiClient context for the Back-Office/Admin application.
 */
class ApiClientContextListener
{
    public function __construct(
        private readonly ApiClientContextBuilder $accessContextBuilder,
        private readonly Security $security
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->security->getToken();
        if ($token) {
            $this->accessContextBuilder->setClientId($token->getUserIdentifier());
            if ($token->getUser() instanceof JwtTokenUser) {
                $this->accessContextBuilder->setExternalIssuer($token->getUser()->getExternalIssuer());
            }
        }
    }
}
