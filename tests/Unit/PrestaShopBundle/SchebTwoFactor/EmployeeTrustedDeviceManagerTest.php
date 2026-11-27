<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\SchebTwoFactor;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\EmployeeTrustedDeviceManager;
use PrestaShopBundle\SchebTwoFactor\TrustedDeviceTokenFactory;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenFactory;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\Http\Authentication\AuthenticationRequiredHandlerInterface;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\Passport\Badge\TrustedDeviceBadge;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\Passport\Credentials\TwoFactorCodeCredentials;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\TwoFactorAuthenticator;
use Scheb\TwoFactorBundle\Security\Http\EventListener\TrustedDeviceListener;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\JwtTokenEncoder;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManager;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceTokenEncoder;
use Scheb\TwoFactorBundle\Security\TwoFactor\Trusted\TrustedDeviceTokenStorage;
use Scheb\TwoFactorBundle\Security\TwoFactor\TwoFactorFirewallConfig;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

final class EmployeeTrustedDeviceManagerTest extends TestCase
{
    public function testNativeCookieIsIssuedAndRevokedByVersion(): void
    {
        [$native, $cookies] = $this->nativeManager();
        $user = $this->user(1);
        $tokens = $this->authenticatedTokens($user);
        $version = 1;
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willReturnCallback(static function () use (&$version): int {
            return $version;
        });
        $manager = new EmployeeTrustedDeviceManager($native, $tokens, $repository);

        self::assertTrue($manager->canSetTrustedDevice($user, new Request(), 'main'));
        $manager->addTrustedDevice($user, 'main');
        $cookie = $cookies->getCookieValue();
        self::assertNotEmpty($cookie);

        [$reader] = $this->nativeManager($cookie);
        $manager = new EmployeeTrustedDeviceManager($reader, $tokens, $repository);
        self::assertTrue($manager->isTrustedDevice($user, 'main'));
        $version = 2;
        self::assertFalse($manager->isTrustedDevice($user, 'main'), 'A cached user must not keep obsolete trust.');
        self::assertFalse($manager->isTrustedDevice($this->user(2), 'main'));
    }

    public function testCookiesIssuedBeforeVersioningAreInvalid(): void
    {
        [$native, $cookies] = $this->nativeManager();
        $native->addTrustedDevice($this->user(0), 'main');
        [$reader] = $this->nativeManager($cookies->getCookieValue());
        self::assertFalse($reader->isTrustedDevice($this->user(1), 'main'));
    }

    public function testANewAuthenticationCanTrustTheDeviceAgainAfterRevocation(): void
    {
        [$native, $cookies] = $this->nativeManager();
        $user = $this->user(2);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willReturn(2);
        $manager = new EmployeeTrustedDeviceManager($native, $this->authenticatedTokens($user), $repository);
        self::assertTrue($manager->canSetTrustedDevice($user, new Request(), 'main'));
        $manager->addTrustedDevice($user, 'main');
        [$reader] = $this->nativeManager($cookies->getCookieValue());
        self::assertTrue($reader->isTrustedDevice($user, 'main'));
    }

    public function testRevocationAfterTheLastCheckCannotIssueTheNewVersion(): void
    {
        [$native, $cookies] = $this->nativeManager();
        $user = $this->user(1);
        $repository = $this->createMock(EmployeeRepository::class);
        $version = 1;
        $reads = 0;
        $repository->method('getTrustedDeviceVersion')->willReturnCallback(static function () use (&$version, &$reads): int {
            $observedVersion = $version;
            if (++$reads === 2) {
                // The reset commits immediately after addTrustedDevice's final read.
                $version = 2;
            }

            return $observedVersion;
        });
        $manager = new EmployeeTrustedDeviceManager($native, $this->authenticatedTokens($user), $repository);
        self::assertTrue($manager->canSetTrustedDevice($user, new Request(), 'main'));

        $manager->addTrustedDevice($user, 'main');
        self::assertSame(2, $version);
        [$reader] = $this->nativeManager($cookies->getCookieValue());
        self::assertTrue($reader->isTrustedDevice($user, 'main'));
        self::assertFalse($reader->isTrustedDevice($this->user(2), 'main'));
    }

    public function testRevocationBeforeTheFinalReadPreventsCookieIssuance(): void
    {
        [$native, $cookies] = $this->nativeManager();
        $user = $this->user(1);
        $version = 1;
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willReturnCallback(static function () use (&$version): int {
            return $version;
        });
        $manager = new EmployeeTrustedDeviceManager($native, $this->authenticatedTokens($user), $repository);
        self::assertTrue($manager->canSetTrustedDevice($user, new Request(), 'main'));
        $version = 2;
        $manager->addTrustedDevice($user, 'main');
        self::assertFalse($cookies->hasUpdatedCookie());
    }

    #[DataProvider('revocationsAtSuccess')]
    public function testNativeSuccessFlowKeepsTheOriginalTokenAvailable(bool $revoke): void
    {
        [$native, $cookies] = $this->nativeManager();
        $version = 1;
        $user = $this->createMock(Employee::class);
        $user->method('getId')->willReturn(42);
        $user->method('getUserIdentifier')->willReturn('employee@example.com');
        $user->method('getTrustedTokenVersion')->willReturnCallback(static function () use (&$version): int {
            return $version;
        });
        $authenticated = new UsernamePasswordToken($user, 'main', ['ROLE_EMPLOYEE']);
        $pending = (new TrustedDeviceTokenFactory(new TwoFactorTokenFactory()))->create($authenticated, 'main', ['email']);
        $tokens = new TokenStorage();
        $tokens->setToken($pending);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willReturnCallback(static function () use (&$version): int {
            return $version;
        });
        $manager = new EmployeeTrustedDeviceManager($native, $tokens, $repository);
        $events = new EventDispatcher();
        $events->addSubscriber(new TrustedDeviceListener($manager));
        $successHandler = $this->createMock(AuthenticationSuccessHandlerInterface::class);
        $successHandler->expects(self::once())->method('onAuthenticationSuccess')->willReturnCallback(
            static function (Request $request, TokenInterface $token) use ($authenticated, $tokens, $revoke, &$version): Response {
                self::assertSame($authenticated, $token);
                self::assertSame($token, $tokens->getToken(), 'Symfony must set the completed token before its success handler.');
                if ($revoke) {
                    // Model the managed employee being refreshed in PrestaShop's success handler.
                    $version = 2;
                }

                return new Response();
            }
        );
        $firewall = $this->createMock(TwoFactorFirewallConfig::class);
        $firewall->method('isMultiFactor')->willReturn(false);
        $authenticator = new TwoFactorAuthenticator(
            $firewall,
            $tokens,
            $successHandler,
            $this->createMock(AuthenticationFailureHandlerInterface::class),
            $this->createMock(AuthenticationRequiredHandlerInterface::class),
            $events
        );
        // Only the success pipeline is under test: the second factor is already validated.
        $credentials = new TwoFactorCodeCredentials($pending, '123456');
        $credentials->markResolved();
        $authentication = new AuthenticatorManager([], $tokens, $events, 'main');
        $authentication->authenticateUser($user, $authenticator, new Request(), [$credentials, new TrustedDeviceBadge()]);

        self::assertSame($authenticated, $tokens->getToken());
        self::assertSame(1, $authenticated->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE)['version']);
        self::assertSame(!$revoke, $cookies->hasUpdatedCookie());
        if (!$revoke) {
            [$reader] = $this->nativeManager($cookies->getCookieValue());
            self::assertTrue($reader->isTrustedDevice($user, 'main'));
        }
    }

    public static function revocationsAtSuccess(): iterable
    {
        yield 'unchanged employee' => [false];
        yield 'revocation and refresh before cookie issuance' => [true];
    }

    public function testRefreshAfterEligibilityCannotPromoteTheCookie(): void
    {
        $version = 1;
        $user = $this->createMock(Employee::class);
        $user->method('getId')->willReturn(42);
        $user->method('getUserIdentifier')->willReturn('employee@example.com');
        $user->method('getTrustedTokenVersion')->willReturnCallback(static function () use (&$version): int {
            return $version;
        });
        $inner = $this->createMock(TrustedDeviceManagerInterface::class);
        $inner->method('canSetTrustedDevice')->willReturn(true);
        $inner->expects(self::never())->method('addTrustedDevice');
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willReturn(1);
        $manager = new EmployeeTrustedDeviceManager($inner, $this->authenticatedTokens($user), $repository);
        self::assertTrue($manager->canSetTrustedDevice($user, new Request(), 'main'));
        $version = 2;
        $manager->addTrustedDevice($user, 'main');
    }

    #[DataProvider('differentAuthentications')]
    public function testRefreshCannotGrantTrustToAnotherAuthentication(int $version, int $id, string $email, string $firewall): void
    {
        $tokens = $this->authenticatedTokens($this->user(1));
        $refreshed = $this->user($version, $id, $email);
        $token = $tokens->getToken();
        self::assertNotNull($token);
        $token->setUser($refreshed);
        $inner = $this->createMock(TrustedDeviceManagerInterface::class);
        $inner->expects(self::never())->method('addTrustedDevice');
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->expects(self::never())->method('getTrustedDeviceVersion');
        $manager = new EmployeeTrustedDeviceManager($inner, $tokens, $repository);

        self::assertFalse($manager->canSetTrustedDevice($refreshed, new Request(), $firewall));
        $manager->addTrustedDevice($refreshed, $firewall);
    }

    public static function differentAuthentications(): iterable
    {
        yield 'revoked version' => [2, 42, 'employee@example.com', 'main'];
        yield 'another employee' => [1, 43, 'employee@example.com', 'main'];
        yield 'another email' => [1, 42, 'changed@example.com', 'main'];
        yield 'another firewall' => [1, 42, 'employee@example.com', 'admin-api'];
    }

    public function testMissingSnapshotDoesNotGrantTrustOrChangeTheToken(): void
    {
        $user = $this->user(1);
        $token = new UsernamePasswordToken($user, 'main', ['ROLE_EMPLOYEE']);
        $tokens = new TokenStorage();
        $tokens->setToken($token);
        $inner = $this->createMock(TrustedDeviceManagerInterface::class);
        $inner->expects(self::never())->method('addTrustedDevice');
        $repository = $this->createMock(EmployeeRepository::class);
        $manager = new EmployeeTrustedDeviceManager($inner, $tokens, $repository);
        self::assertFalse($manager->canSetTrustedDevice($user, new Request(), 'main'));
        $manager->addTrustedDevice($user, 'main');
        self::assertSame($token, $tokens->getToken());
    }

    public function testPendingAuthenticationCannotIssueTrust(): void
    {
        $user = $this->user(1);
        $authenticated = new UsernamePasswordToken($user, 'main', ['ROLE_EMPLOYEE']);
        $pending = (new TrustedDeviceTokenFactory(new TwoFactorTokenFactory()))->create($authenticated, 'main', ['email']);
        $tokens = new TokenStorage();
        $tokens->setToken($pending);
        $inner = $this->createMock(TrustedDeviceManagerInterface::class);
        $inner->expects(self::never())->method('addTrustedDevice');
        $repository = $this->createMock(EmployeeRepository::class);
        $manager = new EmployeeTrustedDeviceManager($inner, $tokens, $repository);
        self::assertFalse($manager->canSetTrustedDevice($user, new Request(), 'main'));
        $manager->addTrustedDevice($user, 'main');
    }

    public function testFactoryCannotReplaceTheInitialSnapshotAfterRefresh(): void
    {
        $authenticated = new UsernamePasswordToken($this->user(1), 'main', ['ROLE_EMPLOYEE']);
        $factory = new TrustedDeviceTokenFactory(new TwoFactorTokenFactory());
        $factory->create($authenticated, 'main', ['email']);
        $original = $authenticated->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE);
        $authenticated->setUser($this->user(2));
        $factory->create($authenticated, 'main', ['email']);
        self::assertSame($original, $authenticated->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE));
    }

    public function testDeletedEmployeeCannotValidateOrAcquireTrust(): void
    {
        $user = $this->user(1);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willThrowException(new EmployeeNotFoundException(new EmployeeId(42)));
        $inner = $this->createMock(TrustedDeviceManagerInterface::class);
        $inner->expects(self::never())->method('isTrustedDevice');
        $inner->expects(self::never())->method('canSetTrustedDevice');
        $manager = new EmployeeTrustedDeviceManager($inner, $this->authenticatedTokens($user), $repository);
        self::assertFalse($manager->isTrustedDevice($user, 'main'));
        self::assertFalse($manager->canSetTrustedDevice($user, new Request(), 'main'));
    }

    public function testNativeEligibilityDecisionIsPreserved(): void
    {
        $user = $this->user(1);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getTrustedDeviceVersion')->willReturn(1);
        $inner = $this->createMock(TrustedDeviceManagerInterface::class);
        $inner->expects(self::once())->method('canSetTrustedDevice')->willReturn(false);
        $manager = new EmployeeTrustedDeviceManager($inner, $this->authenticatedTokens($user), $repository);
        self::assertFalse($manager->canSetTrustedDevice($user, new Request(), 'main'));
    }

    public function testSnapshotSurvivesTheNativePendingTokenSerialization(): void
    {
        $user = new Employee();
        $user->__unserialize([
            'id' => 42,
            'email' => 'employee@example.com',
            'password' => 'hash',
            'profileId' => 1,
            'defaultLocale' => 'en',
        ]);
        $authenticated = new UsernamePasswordToken($user, 'main', ['ROLE_EMPLOYEE']);
        $pending = (new TrustedDeviceTokenFactory(new TwoFactorTokenFactory()))->create($authenticated, 'main', ['email']);
        $restored = unserialize(serialize($pending));
        self::assertInstanceOf(TwoFactorTokenInterface::class, $restored);

        self::assertSame(
            $authenticated->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE),
            $restored->getAuthenticatedToken()->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE)
        );
        self::assertSame(1, $restored->getAuthenticatedToken()->getAttribute(TrustedDeviceTokenFactory::AUTHENTICATION_ATTRIBUTE)['version']);
    }

    private function authenticatedTokens(Employee $user): TokenStorage
    {
        $authenticated = new UsernamePasswordToken($user, 'main', ['ROLE_EMPLOYEE']);
        $pending = (new TrustedDeviceTokenFactory(new TwoFactorTokenFactory()))->create($authenticated, 'main', ['email']);
        $tokens = new TokenStorage();
        $tokens->setToken($pending->getAuthenticatedToken());

        return $tokens;
    }

    private function user(int $version, int $id = 42, string $email = 'employee@example.com'): Employee
    {
        $user = $this->createMock(Employee::class);
        $user->method('getId')->willReturn($id);
        $user->method('getUserIdentifier')->willReturn($email);
        $user->method('getTrustedTokenVersion')->willReturn($version);

        return $user;
    }

    /**
     * @return array{TrustedDeviceManager, TrustedDeviceTokenStorage}
     */
    private function nativeManager(?string $cookie = null): array
    {
        $request = new Request([], [], [], null === $cookie ? [] : ['trusted_device' => $cookie]);
        $requests = new RequestStack();
        $requests->push($request);
        $configuration = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(str_repeat('test-key', 8)));
        $encoder = new TrustedDeviceTokenEncoder(new JwtTokenEncoder($configuration), 5184000);
        $storage = new TrustedDeviceTokenStorage($requests, $encoder, 'trusted_device');

        return [new TrustedDeviceManager($storage), $storage];
    }
}
