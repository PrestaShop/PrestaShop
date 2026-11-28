<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\EventListener\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorRateLimiter;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\EventListener\Admin\TwoFactorCheckRateLimitListener;
use RuntimeException;
use Scheb\TwoFactorBundle\Security\Authentication\Exception\InvalidTwoFactorCodeException;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\Passport\Credentials\TwoFactorCodeCredentials;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Lock\Exception\LockExpiredException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\Storage\StorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\EventListener\CsrfProtectionListener;

final class TwoFactorCheckRateLimitListenerTest extends TestCase
{
    private EmployeeTwoFactorRateLimiter $limiter;

    private TwoFactorCheckRateLimitListener $listener;

    private LockFactory $lockFactory;

    private RequestStack $requests;

    private Request $request;

    protected function setUp(): void
    {
        $this->lockFactory = new LockFactory(new InMemoryStore());
        $this->limiter = $this->limiter($this->lockFactory);
        $this->requests = new RequestStack();
        $this->request = Request::create('/two-factor');
        $this->requests->push($this->request);
        $this->listener = new TwoFactorCheckRateLimitListener($this->limiter, $this->requests);
    }

    public function testRepeatedSuccessfulLoginsNeverExhaustTheBudget(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $event = $this->verification();
            $this->listener->checkPassport($event);
            $this->resolve($event);
            $this->listener->onVerificationSuccess($event);
            $this->listener->onLoginSuccess(new LoginSuccessEvent(
                $event->getAuthenticator(),
                $event->getPassport(),
                new UsernamePasswordToken($event->getPassport()->getUser(), 'main', [Employee::ROLE_EMPLOYEE]),
                $this->request,
                new Response(),
                'main'
            ));
            $this->assertLockReleased();
        }
    }

    public function testSuccessPreservesFourFailuresAndTheFifthFailureClosesTheBudget(): void
    {
        for ($i = 0; $i < 4; ++$i) {
            $this->failVerification(new InvalidTwoFactorCodeException());
        }
        $event = $this->verification();
        $this->listener->checkPassport($event);
        $this->resolve($event);
        $this->listener->onVerificationSuccess($event);
        $this->failVerification(new InvalidTwoFactorCodeException());
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->listener->checkPassport($this->verification());
    }

    public function testLoginAndCqrsShareFailuresIndependentlyOfIpAndProvider(): void
    {
        $this->request->server->set('REMOTE_ADDR', '192.0.2.1');
        $lock = $this->limiter->startVerification(42);
        try {
            $this->limiter->recordFailure(42, $lock);
        } finally {
            $lock->release();
        }
        for ($i = 0; $i < 4; ++$i) {
            $this->request->server->set('REMOTE_ADDR', '192.0.2.' . ($i + 2));
            $this->failVerification(new InvalidTwoFactorCodeException());
        }
        $other = $this->limiter->startVerification(43);
        $other->release();
        $this->expectException(EmployeeTwoFactorException::class);
        $this->limiter->startVerification(42);
    }

    public function testNativeCsrfFailureDoesNotAcquireALockOrConsumeQuota(): void
    {
        $csrf = $this->createMock(CsrfTokenManagerInterface::class);
        $csrf->method('isTokenValid')->willReturn(false);
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new CsrfProtectionListener($csrf));
        $dispatcher->addSubscriber($this->listener);
        try {
            $dispatcher->dispatch($this->verification());
            self::fail('The native CSRF listener must reject the request.');
        } catch (InvalidCsrfTokenException) {
            $this->assertLockReleased();
        }
        for ($i = 0; $i < 5; ++$i) {
            $this->failVerification(new InvalidTwoFactorCodeException());
        }
        $this->expectException(EmployeeTwoFactorException::class);
        $this->limiter->startVerification(42);
    }

    #[DataProvider('nonProofFailures')]
    public function testNonProofFailuresReleaseWithoutCharging(AuthenticationException $exception): void
    {
        for ($i = 0; $i < 6; ++$i) {
            $this->failVerification($exception);
        }
        $guard = $this->limiter->startVerification(42);
        $guard->release();
    }

    public static function nonProofFailures(): iterable
    {
        yield 'disabled employee' => [new BadCredentialsException('Hidden account status', 0, new DisabledException())];
        yield 'CSRF' => [new InvalidCsrfTokenException()];
        yield 'preparation error' => [new AuthenticationException('Provider not prepared')];
    }

    public function testACodeAlreadyConsumedByAnotherRequestCountsAsAFailure(): void
    {
        $this->failVerification(new BadCredentialsException('The backup code is no longer valid.'));
        for ($i = 0; $i < 4; ++$i) {
            $this->failVerification(new InvalidTwoFactorCodeException());
        }
        $this->expectException(EmployeeTwoFactorException::class);
        $this->limiter->startVerification(42);
    }

    public function testFailureAfterResolvedCredentialsDoesNotChargeTheProof(): void
    {
        for ($i = 0; $i < 6; ++$i) {
            $event = $this->verification();
            $this->listener->checkPassport($event);
            $this->resolve($event);
            $this->loginFailure($event, new BadCredentialsException('Another badge failed'));
            $this->assertLockReleased();
        }
    }

    public function testTechnicalExceptionsReleaseEvenWhenTheFailureHandlerDidNotFinish(): void
    {
        for ($i = 0; $i < 6; ++$i) {
            $this->listener->checkPassport($this->verification());
            $request = $this->request;
            $this->listener->onKernelException(new ExceptionEvent(
                $this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST, new RuntimeException('Database or failure handler error')
            ));
            $this->assertLockReleased();
            // Cleanup is idempotent even when finish_request follows kernel.exception.
            $this->listener->onFinishRequest(new FinishRequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST));
        }
    }

    public function testFinishRequestReleasesAnUnfinishedVerification(): void
    {
        $this->listener->checkPassport($this->verification());
        $this->listener->onFinishRequest(new FinishRequestEvent(
            $this->createMock(HttpKernelInterface::class), $this->request, HttpKernelInterface::MAIN_REQUEST
        ));
        $this->assertLockReleased();
    }

    public function testBudgetReadErrorsReleaseBeforeThePassportIsRegistered(): void
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->method('fetch')->willThrowException(new RuntimeException('Cache unavailable'));
        $listener = new TwoFactorCheckRateLimitListener($this->limiter($this->lockFactory, $storage), $this->requests);
        try {
            $listener->checkPassport($this->verification());
            self::fail('The budget read error must propagate.');
        } catch (RuntimeException $e) {
            self::assertSame('Cache unavailable', $e->getMessage());
            $this->assertLockReleased();
        }
    }

    public function testFinishingASubRequestDoesNotReleaseTheParentVerification(): void
    {
        $this->listener->checkPassport($this->verification());
        $this->listener->onFinishRequest(new FinishRequestEvent(
            $this->createMock(HttpKernelInterface::class), Request::create('/fragment'), HttpKernelInterface::SUB_REQUEST
        ));
        $probe = $this->lockFactory->createLock('employee_2fa_verification:42');
        try {
            self::assertFalse($probe->acquire());
        } finally {
            $probe->release();
            $this->listener->onFinishRequest(new FinishRequestEvent(
                $this->createMock(HttpKernelInterface::class), $this->request, HttpKernelInterface::MAIN_REQUEST
            ));
        }
        $this->assertLockReleased();
    }

    public function testFailureCounterStorageErrorsStillReleaseTheVerificationLock(): void
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->method('fetch')->willReturn(null);
        $storage->method('save')->willThrowException(new RuntimeException('Cache unavailable'));
        $this->listener = new TwoFactorCheckRateLimitListener($this->limiter($this->lockFactory, $storage), $this->requests);
        try {
            $this->failVerification(new InvalidTwoFactorCodeException());
            self::fail('The storage error must propagate.');
        } catch (RuntimeException $e) {
            self::assertSame('Cache unavailable', $e->getMessage());
            $this->assertLockReleased();
        }
    }

    public function testExpiredLeaseFailsBeforeAuthenticationCanSucceedAndReleasesTheLock(): void
    {
        $lock = $this->createMock(LockInterface::class);
        $lock->method('acquire')->with(true)->willReturn(true);
        $lock->method('isExpired')->willReturnOnConsecutiveCalls(false, true);
        $lock->method('isAcquired')->willReturn(true);
        $lock->expects(self::once())->method('release');
        $factory = $this->createMock(LockFactory::class);
        $factory->expects(self::once())->method('createLock')->with('employee_2fa_verification:42', 300.0)->willReturn($lock);
        $listener = new TwoFactorCheckRateLimitListener($this->limiter($factory), $this->requests);
        $event = $this->verification();
        $listener->checkPassport($event);
        $this->resolve($event);
        $this->expectException(LockExpiredException::class);
        $listener->onVerificationSuccess($event);
    }

    private function limiter(LockFactory $factory, ?StorageInterface $storage = null): EmployeeTwoFactorRateLimiter
    {
        return new EmployeeTwoFactorRateLimiter(
            new RateLimiterFactory(['id' => 'check', 'policy' => 'fixed_window', 'limit' => 5, 'interval' => '15 minutes'], $storage ?? new InMemoryStorage()),
            new RateLimiterFactory(['id' => 'send', 'policy' => 'fixed_window', 'limit' => 3, 'interval' => '15 minutes'], new InMemoryStorage()),
            $factory
        );
    }

    private function verification(int $employeeId = 42): CheckPassportEvent
    {
        $employee = $this->createPartialMock(Employee::class, ['getId']);
        $employee->method('getId')->willReturn($employeeId);
        $token = $this->createMock(TwoFactorTokenInterface::class);
        $passport = new Passport(
            new UserBadge('employee', static fn (): Employee => $employee),
            new TwoFactorCodeCredentials($token, '123456'),
            [new CsrfTokenBadge('two-factor', 'invalid')]
        );

        return new CheckPassportEvent($this->createMock(AuthenticatorInterface::class), $passport);
    }

    private function resolve(CheckPassportEvent $event): void
    {
        $credentials = $event->getPassport()->getBadge(TwoFactorCodeCredentials::class);
        self::assertInstanceOf(TwoFactorCodeCredentials::class, $credentials);
        $credentials->markResolved();
    }

    private function loginFailure(CheckPassportEvent $event, AuthenticationException $exception): void
    {
        $this->listener->onLoginFailure(new LoginFailureEvent(
            $exception, $event->getAuthenticator(), $this->request, new Response(), 'main', $event->getPassport()
        ));
    }

    private function failVerification(AuthenticationException $exception): void
    {
        $event = $this->verification();
        $this->listener->checkPassport($event);
        $this->loginFailure($event, $exception);
        // A repeated event must not count the same failure twice.
        $this->loginFailure($event, $exception);
        $this->assertLockReleased();
    }

    private function assertLockReleased(): void
    {
        $probe = $this->lockFactory->createLock('employee_2fa_verification:42');
        try {
            self::assertTrue($probe->acquire());
        } finally {
            $probe->release();
        }
    }
}
