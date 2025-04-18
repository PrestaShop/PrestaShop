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

namespace PrestaShop\PrestaShop\Core\Security\OAuth2;

use Symfony\Component\HttpFoundation\Request;

/**
 * To integrate an authorization server you must implement this interface, the methods are bridges that call the authorization server
 * to ensure the access token is valid, if so it can return a representation of the user using the JwtTokenUser DTO.
 */
interface AuthorisationServerInterface
{
    /**
     * For each request received, the resource server loops through all the available servers implementing this interface
     * and uses this method to detect which one matches with the provided access token. Your implementation of each authorization
     * server must be able to recognize an access token it created, usually relying on the issuer saved in the metadata included
     * in the JWT token (no convention is forced, each authorization server may store this info differently as long as it can
     * recognize itself).
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isTokenValid(Request $request): bool;

    /**
     * If the token is valid, the authorization server must return a representation of the user using the
     * JwtTokenUser DTO that contains:
     *  - userId: Usually, the Client ID
     *  - scopes: List of scopes authorized in the access token
     *  - issuer: An identifier for the authorization server that issued the token:
     *    - for external authorization servers: usually the address of the server
     *    - for our internal authorization server: null (it is the only allowed to use null as an issuer)
     *
     * @param Request $request
     *
     * @return JwtTokenUser|null
     */
    public function getJwtTokenUser(Request $request): ?JwtTokenUser;
}
