<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Security;

use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\OAuth2\AuthorisationServerInterface;
use PrestaShop\PrestaShop\Core\Security\OAuth2\TokenAuthenticator;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;

class TokenAuthenticatorTest extends TestCase
{
    protected $tokenAuthenticator;

    public function setUp(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $this->tokenAuthenticator = new TokenAuthenticator(
            [$this->createMock(AuthorisationServerInterface::class)],
            $translator,
            $this->createMock(ApiClientRepository::class),
            $this->createMock(LoggerInterface::class),
        );
        parent::setUp();
    }

    public function testOnAuthenticationFailure(): void
    {
        $response = $this->tokenAuthenticator->onAuthenticationFailure(Request::create('/'), new AuthenticationException());
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertTrue($response->headers->has('WWW-Authenticate'));
        $this->assertSame('Bearer', $response->headers->get('WWW-Authenticate'));
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->tokenAuthenticator->supports(Request::create('/')));
    }

    private function buildTestToken(): string
    {
        $key = InMemory::base64Encoded('hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB');

        $token = (new JwtFacade())->issue(
            new Sha256(),
            $key,
            static fn (
                Builder $builder,
                DateTimeImmutable $issuedAt
            ): Builder => $builder
                ->issuedBy('https://api.my-awesome-app.io')
                ->permittedFor('https://client-app.io')
                ->expiresAt($issuedAt->modify('+10 minutes'))
        );

        return $token->toString();
    }

    public function testAuthenticate(): void
    {
        $request = Request::create('/');
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('No Authorization header provided');
        $this->tokenAuthenticator->authenticate($request);

        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Bearer token missing');
        $request->headers->add(['Authorization' => 'toto']);
        $this->tokenAuthenticator->authenticate($request);

        $request->headers->add(['Authorization' => 'Bearer ' . $this->buildTestToken()]);
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');
        $this->tokenAuthenticator->authenticate($request);
    }
}
