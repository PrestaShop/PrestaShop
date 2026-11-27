<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Employee\TwoFactor;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmailOtpPreparation;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeEmailOtpManager;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorRateLimiter;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeEmailOtp;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\EmployeeEmailCodeGenerator;
use PrestaShopBundle\SchebTwoFactor\EmployeeEmailFormRenderer;
use PrestaShopBundle\SchebTwoFactor\EmployeeEmailTwoFactorProvider;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\PreparationRecorderInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

final class EmployeeEmailOtpManagerTest extends TestCase
{
    private EmployeeRepository&MockObject $repository;

    private AuthCodeMailerInterface&MockObject $mailer;

    private EmployeeEmailOtpManager $manager;

    private EmployeeTwoFactorRateLimiter $limiter;

    private Employee $employee;

    private MockClock $clock;

    /**
     * @var array<string, mixed>
     */
    private array $state;

    protected function setUp(): void
    {
        $this->state = [
            'id_employee' => 42,
            'email' => 'current@example.com',
            'active' => 1,
            'two_factor_enabled' => 1,
            'two_factor_email_enabled' => 1,
            'two_factor_trusted_version' => 1,
            'two_factor_email_auth_code' => '123456',
            'two_factor_email_auth_code_issued_at' => '2026-11-27 12:00:00.000000',
            'two_factor_email_auth_code_context' => EmployeeEmailOtp::LOGIN,
            'two_factor_email_auth_code_version' => 1,
        ];
        $this->clock = new MockClock('2026-11-27 12:00:00 UTC');
        $this->repository = $this->createMock(EmployeeRepository::class);
        $this->repository->method('getEmailOtpState')->willReturnCallback(fn (): array => $this->state);
        $this->mailer = $this->createMock(AuthCodeMailerInterface::class);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->with('PS_BACKOFFICE_2FA')->willReturn(true);
        $this->limiter = new EmployeeTwoFactorRateLimiter(
            new RateLimiterFactory(['id' => 'check', 'policy' => 'fixed_window', 'limit' => 5, 'interval' => '15 minutes'], new InMemoryStorage()),
            new RateLimiterFactory(['id' => 'send', 'policy' => 'fixed_window', 'limit' => 3, 'interval' => '15 minutes'], new InMemoryStorage()),
            new LockFactory(new InMemoryStore())
        );
        $this->manager = new EmployeeEmailOtpManager($this->repository, $this->limiter, $this->mailer, $configuration, $this->clock);
        $this->employee = $this->createPartialMock(Employee::class, ['getId']);
        $this->employee->method('getId')->willReturn(42);
        $this->employee->setEmail('obsolete@example.com');
        $this->employee->setEmailAuthCode('obsolete');
    }

    public function testAutomaticPreparationReusesAValidChallengeWithoutSendingOrWriting(): void
    {
        $this->mailer->expects(self::never())->method('sendAuthCode');
        $this->repository->expects(self::never())->method('replaceEmailOtp');
        for ($i = 0; $i < 5; ++$i) {
            self::assertSame(EmailOtpPreparation::AVAILABLE, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN)->status);
        }
        self::assertTrue($this->limiter->consumeSend(42)->isAccepted());
    }

    public function testExplicitResendPreservesTheCodeAndItsOriginalExpiry(): void
    {
        $this->repository->expects(self::never())->method('replaceEmailOtp');
        $this->mailer->expects(self::once())->method('sendAuthCode')->with(self::callback(
            fn (Employee $recipient): bool => $recipient !== $this->employee
                && '123456' === $recipient->getEmailAuthCode()
                && 'current@example.com' === $recipient->getEmail()
        ));
        self::assertSame(EmailOtpPreparation::SENT, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN, true)->status);
        self::assertSame('obsolete', $this->employee->getEmailAuthCode());
        $this->clock->sleep(600);
        self::assertFalse($this->manager->hasValidChallenge(42, EmployeeEmailOtp::LOGIN));
    }

    public function testExpiredCodeIsReplacedByASixDigitCodeBeforeSending(): void
    {
        $this->clock->sleep(600);
        $written = false;
        $this->repository->expects(self::once())->method('replaceEmailOtp')->with(self::callback(
            function (EmployeeEmailOtp $challenge) use (&$written): bool {
                $written = true;

                return 1 === preg_match('/^[0-9]{6}$/D', $challenge->code)
                    && '123456' !== $challenge->code
                    && $challenge->issuedAt == $this->clock->now()
                    && EmployeeEmailOtp::LOGIN === $challenge->context
                    && 1 === $challenge->version;
            }
        ), $this->state)->willReturn(true);
        $this->mailer->expects(self::once())->method('sendAuthCode')->willReturnCallback(
            static function () use (&$written): void { self::assertTrue($written); }
        );
        self::assertSame(EmailOtpPreparation::SENT, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN)->status);
    }

    public function testOnlyThreeExplicitSendsAreAllowed(): void
    {
        $this->mailer->expects(self::exactly(3))->method('sendAuthCode');
        for ($i = 0; $i < 3; ++$i) {
            self::assertSame(EmailOtpPreparation::SENT, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN, true)->status);
        }
        $outcome = $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN, true);
        self::assertSame(EmailOtpPreparation::THROTTLED, $outcome->status);
        self::assertNotNull($outcome->retryAt);
    }

    public function testAutomaticAndBackupRegenerationSendsShareTheSameQuota(): void
    {
        for ($i = 0; $i < 3; ++$i) {
            $this->limiter->consumeSend(42);
        }
        $this->mailer->expects(self::never())->method('sendAuthCode');
        $this->repository->expects(self::never())->method('replaceEmailOtp');
        self::assertSame(EmailOtpPreparation::THROTTLED, $this->manager->prepare($this->employee, EmployeeEmailOtp::BACKUP_REGENERATION)->status);
        $this->state['two_factor_email_auth_code'] = null;
        self::assertSame(EmailOtpPreparation::THROTTLED, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN)->status);
    }

    public function testGlobalDisableRejectsIssuingAndConsumingCodes(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturn(false);
        $manager = new EmployeeEmailOtpManager($this->repository, $this->limiter, $this->mailer, $configuration, $this->clock);
        self::assertFalse($manager->consumeLogin(42, '123456'));
        $this->mailer->expects(self::never())->method('sendAuthCode');
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::DISABLED);
        $manager->prepare($this->employee, EmployeeEmailOtp::LOGIN);
    }

    public function testLostConditionalWriteNeverSendsTheLosingCode(): void
    {
        $this->state['two_factor_email_auth_code'] = null;
        $this->repository->expects(self::once())->method('replaceEmailOtp')->willReturn(false);
        $this->mailer->expects(self::never())->method('sendAuthCode');
        self::assertSame(EmailOtpPreparation::CHANGED, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN)->status);
    }

    public function testMailFailureDoesNotRestoreOldStateOrReportSuccess(): void
    {
        $this->mailer->method('sendAuthCode')->willThrowException(new EmployeeTwoFactorException('Delivery failed', EmployeeTwoFactorException::EMAIL_DELIVERY_FAILED));
        $this->repository->expects(self::never())->method('replaceEmailOtp');
        self::assertSame(EmailOtpPreparation::FAILED, $this->manager->prepare($this->employee, EmployeeEmailOtp::LOGIN, true)->status);
    }

    #[DataProvider('invalidChallenges')]
    public function testInvalidChallengesCannotReachTheAtomicConsumer(array $changes, string $code): void
    {
        $this->state = array_replace($this->state, $changes);
        $this->repository->expects(self::never())->method('consumeEmailOtp');
        self::assertFalse($this->manager->consumeLogin(42, $code));
    }

    public static function invalidChallenges(): iterable
    {
        yield 'expired at boundary' => [['two_factor_email_auth_code_issued_at' => '2026-11-27 11:50:00.000000'], '123456'];
        yield 'no issuance time' => [['two_factor_email_auth_code_issued_at' => null], '123456'];
        yield 'future issuance' => [['two_factor_email_auth_code_issued_at' => '2026-11-27 12:00:01.000000'], '123456'];
        yield 'wrong context' => [['two_factor_email_auth_code_context' => EmployeeEmailOtp::BACKUP_REGENERATION], '123456'];
        yield 'revoked security version' => [['two_factor_trusted_version' => 2], '123456'];
        yield 'missing security version' => [['two_factor_email_auth_code_version' => null], '123456'];
        yield 'disabled' => [['two_factor_enabled' => 0], '123456'];
        yield 'email disabled' => [['two_factor_email_enabled' => 0], '123456'];
        yield 'inactive employee' => [['active' => 0], '123456'];
        yield 'wrong code' => [[], '654321'];
        yield 'four digits' => [[], '1234'];
        yield 'empty' => [[], ''];
    }

    public function testProviderAcceptsOnlyTheAtomicConsumptionWinner(): void
    {
        $this->repository->expects(self::exactly(2))->method('consumeEmailOtp')->willReturnOnConsecutiveCalls(true, false);
        $inner = $this->createMock(TwoFactorProviderInterface::class);
        $renderer = $this->createMock(TwoFactorFormRendererInterface::class);
        $provider = new EmployeeEmailTwoFactorProvider($inner, $this->manager, $renderer);
        self::assertTrue($provider->validateAuthenticationCode($this->employee, '123456'));
        self::assertFalse($provider->validateAuthenticationCode($this->employee, '123456'));
        self::assertSame($renderer, $provider->getFormRenderer());
    }

    public function testSuccessfulVerificationDoesNotResetPreviousFailures(): void
    {
        $this->repository->method('consumeEmailOtp')->willReturn(true);
        for ($i = 0; $i < 4; ++$i) {
            $lock = $this->limiter->startVerification(42);
            try {
                $this->limiter->recordFailure(42, $lock);
            } finally {
                $lock->release();
            }
        }
        self::assertTrue($this->manager->consumeLogin(42, '123456'));
        $lock = $this->limiter->startVerification(42);
        try {
            $this->limiter->recordFailure(42, $lock);
        } finally {
            $lock->release();
        }
        $otherEmployeeLock = $this->limiter->startVerification(43);
        $otherEmployeeLock->release();
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::TOO_MANY_ATTEMPTS);
        $this->limiter->startVerification(42);
    }

    public function testAutomaticPreparationLimitIsRenderedWithoutRedirectOrException(): void
    {
        $this->state['two_factor_email_auth_code'] = null;
        for ($i = 0; $i < 3; ++$i) {
            $this->limiter->consumeSend(42);
        }
        $request = Request::create('/two-factor');
        $request->setSession(new Session(new MockArraySessionStorage()));
        $stack = new RequestStack();
        $stack->push($request);
        $generator = new EmployeeEmailCodeGenerator($this->manager, $stack);
        $generator->generateAndSend($this->employee);
        $token = $this->createMock(TwoFactorTokenInterface::class);
        $token->method('getUser')->willReturn($this->employee);
        $token->method('getFirewallName')->willReturn('admin');
        $storage = new TokenStorage();
        $storage->setToken($token);
        $recorder = $this->createMock(PreparationRecorderInterface::class);
        $recorder->method('isTwoFactorProviderPrepared')->willReturn(false);
        $recorder->expects(self::once())->method('setTwoFactorProviderPrepared')->with('admin', 'email');
        $inner = $this->createMock(TwoFactorFormRendererInterface::class);
        $inner->expects(self::once())->method('renderForm')->with($request, self::callback(
            static fn (array $vars): bool => EmailOtpPreparation::THROTTLED === $vars['emailPreparationStatus']
                && false === $vars['emailChallengeAvailable']
                && null !== $vars['emailRetryAt']
        ))->willReturn(new Response('limited'));
        $response = (new EmployeeEmailFormRenderer($inner, $this->manager, $recorder, $storage))->renderForm($request, []);
        self::assertSame(200, $response->getStatusCode());
        self::assertFalse($response->isRedirection());
    }
}
