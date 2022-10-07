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

namespace PrestaShopBundle\Security\OAuth2;

use Exception;
use League\OAuth2\Server\ResourceServer;
use PrestaShop\PrestaShop\Core\OAuth2\OAuth2Interface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OAuth2Client implements OAuth2Interface
{
    /**
     * @var ResourceServer
     */
    private $resourceServer;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    public function __construct(ResourceServer $resourceServer, UserProviderInterface $userProvider)
    {
        $this->resourceServer = $resourceServer;
        $this->userProvider = $userProvider;
    }

    public function isTokenValid(ServerRequestInterface $request): bool
    {
        try {
            $this->resourceServer->validateAuthenticatedRequest($request);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUser(ServerRequestInterface $request): ?UserInterface
    {
        $audience = $this->getAudience($request);
        if ($audience === null) {
            return null;
        }

        try {
            return $this->userProvider->loadUserByUsername($audience);
        } catch (UsernameNotFoundException $exception) {
            return null;
        }
    }

    private function getAudience(ServerRequestInterface $request): ?string
    {
        try {
            return $this->resourceServer->validateAuthenticatedRequest($request)->getAttribute('oauth_client_id');
        } catch (Exception $exception) {
            return null;
        }
    }
}
