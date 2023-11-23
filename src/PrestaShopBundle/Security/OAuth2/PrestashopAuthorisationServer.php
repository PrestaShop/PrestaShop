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

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer as LeagueResourceServer;
use PrestaShop\PrestaShop\Core\Security\OAuth2\AuthorisationServerInterface;
use PrestaShopBundle\Security\OAuth2\Entity\JwtTokenUser;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class responsible for validating token
 * thanks to the PrestaShop's Authorization Server
 *
 * @experimental
 */
class PrestashopAuthorisationServer implements AuthorisationServerInterface
{
    /**
     * @var LeagueResourceServer
     */
    private $leagueResourceServer;

    public function __construct(LeagueResourceServer $resourceServer)
    {
        $this->leagueResourceServer = $resourceServer;
    }

    public function isTokenValid(ServerRequestInterface $request): bool
    {
        try {
            $this->leagueResourceServer->validateAuthenticatedRequest($request);
        } catch (OAuthServerException) {
            return false;
        }

        return true;
    }

    public function getUser(ServerRequestInterface $request): ?UserInterface
    {
        try {
            $validatedResquest = $this->leagueResourceServer->validateAuthenticatedRequest($request);
        } catch (OAuthServerException) {
            return null;
        }

        return new JwtTokenUser(
            $validatedResquest->getAttribute('oauth_client_id'),
            $validatedResquest->getAttribute('oauth_scopes') ?? []
        );
    }
}
