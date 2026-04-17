<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Security\OAuth2\Repository\ClientRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class ClientRepositoryTest extends TestCase
{
    private $clientRepository;

    public function setUp(): void
    {
        $userProvider = new InMemoryUserProvider(['myclientid' => ['password' => 'myclientsecret']]);
        $this->clientRepository = new ClientRepository(
            $userProvider,
            new UserPasswordHasher(new PasswordHasherFactory([
                InMemoryUser::class => ['algorithm' => 'plaintext', 'ignore_case' => false],
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
