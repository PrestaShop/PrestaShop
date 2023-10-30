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

namespace PrestaShopBundle\Security\OAuth2\GrantType;

use DateInterval;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use PrestaShopBundle\Security\OAuth2\Entity\Client;

/**
 * The default class does not allow to modify the lifetime of a token.
 * This class allow to set a different lifetime for each token.
 */
class CustomClientCredentialsGrant extends ClientCredentialsGrant
{
    protected function issueAccessToken(DateInterval $accessTokenTTL, ClientEntityInterface $client, $userIdentifier, array $scopes = [])
    {
        /** @var Client $client */
        if ($client->getLifetime() !== null) {
            $accessTokenTTL = DateInterval::createFromDateString($client->getLifetime() . ' seconds');
        }

        return parent::issueAccessToken($accessTokenTTL, $client, $userIdentifier, $scopes);
    }
}
