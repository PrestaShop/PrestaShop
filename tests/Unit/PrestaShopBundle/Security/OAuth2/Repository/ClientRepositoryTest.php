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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Security\OAuth2\Repository\ClientRepository;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\User;

class ClientRepositoryTest extends TestCase
{
    private $clientRepository;

    public function setUp(): void
    {
        $userProvider = new InMemoryUserProvider(['myclientid' => ['password' => 'myclientsecret']]);
        $this->clientRepository = new ClientRepository(
            $userProvider,
            new UserPasswordEncoder(new EncoderFactory([
                User::class => ['algorithm' => 'plaintext', 'ignore_case' => false],
            ]))
        );
        parent::setUp();
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testGetClientEntity(string $username, ?string $clientSecret, bool $exists): void
    {
        $client = $this->clientRepository->getClientEntity($username);
        $this->assertSame($exists, $client instanceof ClientEntityInterface);
    }

    /**
     * @dataProvider userDataProvider
     */
    public function testValidateClient(string $username, ?string $clientSecret, bool $exists, bool $valid): void
    {
        $response = $this->clientRepository->validateClient($username, $clientSecret, 'client_credentials');
        $this->assertSame($valid, $response);
    }

    /**
     * @dataProvider grantTypeProvider
     */
    public function testValidateClientWrongGrant(?string $grantType, bool $valid): void
    {
        $response = $this->clientRepository->validateClient('myclientid', 'myclientsecret', $grantType);
        $this->assertSame($valid, $response);
    }

    public function userDataProvider(): iterable
    {
        yield ['myclientid', null, true, false];
        yield ['myclientid', 'myclientsecret', true, true];
        yield ['notexistingclientid', null, false, false];
        yield ['notexistingclientid', 'myclientsecret', false, false];
    }

    public function grantTypeProvider(): iterable
    {
        yield ['client_credentials', true];
        yield ['refresh_token', false];
        yield ['authorization_code', false];
        yield ['password', false];
        yield ['implicit', false];
        yield ['nonexisting_grant', false];
    }
}
