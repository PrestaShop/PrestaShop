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

namespace PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use PrestaShopBundle\Security\OAuth2\Entity\Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Repository class responsible for managing PrestaShop's Authorization Server Client entity
 *
 * @experimental
 */
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserProviderInterface $userProvider, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getClientEntity($clientIdentifier): ?Client
    {
        $user = $this->getUser($clientIdentifier);

        if ($user === null) {
            return null;
        }

        $client = new Client();
        $client->setIdentifier($user->getUsername());

        return $client;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        if ($grantType !== 'client_credentials' || $clientSecret === null) {
            return false;
        }
        $client = $this->getUser($clientIdentifier);

        return $client !== null && $this->passwordEncoder->isPasswordValid($client, $clientSecret);
    }

    private function getUser($clientIdentifier): ?UserInterface
    {
        try {
            return $this->userProvider->loadUserByUsername($clientIdentifier);
        } catch (UsernameNotFoundException $exception) {
            return null;
        }
    }
}
