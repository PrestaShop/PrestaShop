<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Employee\TwoFactor;

use DateTimeImmutable;
use PhpEncryption;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeBackupCodeGenerator;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeEmailOtpManager;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorManager;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorRateLimiter;
use PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler\GenerateEmployeeBackupCodesHandler;
use PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler\InitializeEmployeeTotpSecretHandler;
use PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler\PrepareEmployeeBackupCodeRegenerationHandler;
use PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler\RegenerateEmployeeBackupCodesHandler;
use PrestaShop\PrestaShop\Adapter\Profile\Employee\QueryHandler\GetEmployeeTwoFactorSettingsHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\Employee as ContextEmployee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\GenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\InitializeEmployeeTotpSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\PrepareEmployeeBackupCodeRegenerationCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\RegenerateEmployeeBackupCodesCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeTwoFactorSettings;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeEmailOtp;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\TotpSecretEncryptor;
use RuntimeException;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\InMemoryStore;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class EmployeeTwoFactorManagerTest extends TestCase
{
    /**
     * @param 'initialize'|'generate'|'prepare'|'regenerate'|'query' $operation
     */
    #[DataProvider('operations')]
    public function testEveryOperationRejectsAnotherEmployee(string $operation): void
    {
        [$manager, $repository, , $mailer] = $this->dependencies();
        $repository->expects(self::never())->method('getForTwoFactor');
        $mailer->expects(self::never())->method('sendAuthCode');
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::NOT_SELF);
        $this->execute($manager, $operation, 43);
    }

    /**
     * @param 'initialize'|'generate'|'prepare'|'regenerate'|'query' $operation
     */
    #[DataProvider('operations')]
    public function testWritesRejectGloballyDisabledFeature(string $operation): void
    {
        [$manager, $repository, , $mailer] = $this->dependencies(false);
        $repository->expects(self::never())->method('saveBackupCodes');
        $repository->expects(self::never())->method('initializeTotpSecret');
        $mailer->expects(self::never())->method('sendAuthCode');
        if ('query' === $operation) {
            self::assertFalse($manager->getSettings(42)->isEnabled());

            return;
        }
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::DISABLED);
        $this->execute($manager, $operation, 42);
    }

    public static function operations(): iterable
    {
        foreach (['initialize', 'generate', 'prepare', 'regenerate', 'query'] as $operation) {
            yield $operation => [$operation];
        }
    }

    public function testQueryNeverWritesOrSendsEmail(): void
    {
        [$manager, $repository, $totp, $mailer] = $this->dependencies();
        $repository->expects(self::never())->method('initializeTotpSecret');
        $repository->expects(self::never())->method('replaceEmailOtp');
        $totp->expects(self::never())->method('generateSecret');
        $mailer->expects(self::never())->method('sendAuthCode');
        $settings = (new GetEmployeeTwoFactorSettingsHandler($manager))->handle(new GetEmployeeTwoFactorSettings(42));
        self::assertSame('', $settings->getTotpSecret());
        self::assertSame('', $settings->getQrCodeSrc());
        self::assertTrue($settings->isEmailCodePreparationRequired());
    }

    public function testSetupQueryDisplaysExistingDraftWithoutWriting(): void
    {
        [$manager, $repository, $totp, $mailer, $employee, $encryptor] = $this->dependencies();
        $employee->setTwoFactorSecret($encryptor->encrypt('JBSWY3DPEHPK3PXP'));
        $repository->expects(self::never())->method('initializeTotpSecret');
        $totp->expects(self::never())->method('generateSecret');
        $totp->method('getQRContent')->willReturn('otpauth://totp/employee?secret=JBSWY3DPEHPK3PXP');
        $mailer->expects(self::never())->method('sendAuthCode');
        $settings = $manager->getSettings(42);
        self::assertSame('JBSWY3DPEHPK3PXP', $settings->getTotpSecret());
        self::assertStringStartsWith('data:image/png;base64,', $settings->getQrCodeSrc());
    }

    public function testInitializeEncryptsSecretWithoutEnablingTotp(): void
    {
        [$manager, $repository, $totp, , $employee, $encryptor] = $this->dependencies();
        $totp->expects(self::once())->method('generateSecret')->willReturn('JBSWY3DPEHPK3PXP');
        $repository->expects(self::once())->method('initializeTotpSecret')->with(42, self::callback(
            static fn (string $cipher): bool => 'JBSWY3DPEHPK3PXP' === $encryptor->decrypt($cipher)
        ));
        (new InitializeEmployeeTotpSecretHandler($manager))->handle(new InitializeEmployeeTotpSecretCommand(42));
        self::assertFalse($employee->isTotpAuthenticationEnabled());
    }

    public function testInitializeReusesExistingSecret(): void
    {
        [$manager, $repository, $totp, , $employee, $encryptor] = $this->dependencies();
        $employee->setTwoFactorSecret($encryptor->encrypt('JBSWY3DPEHPK3PXP'));
        $repository->expects(self::never())->method('initializeTotpSecret');
        $totp->expects(self::never())->method('generateSecret');
        $manager->initializeTotpSecret(42);
    }

    public function testActiveSecretCannotBeReplacedOrExposedBySetupQuery(): void
    {
        [$manager, $repository, $totp, , $employee, $encryptor] = $this->dependencies(true, true);
        $employee->setTwoFactorSecret($encryptor->encrypt('JBSWY3DPEHPK3PXP'));
        $repository->expects(self::never())->method('initializeTotpSecret');
        $totp->expects(self::never())->method('generateSecret');
        $manager->initializeTotpSecret(42);
        self::assertSame('', $manager->getSettings(42)->getTotpSecret());
    }

    public function testGenerationReturnsPlaintextAfterSavingOnlyHashes(): void
    {
        [$manager, $repository] = $this->dependencies();
        $hashes = null;
        $repository->expects(self::once())->method('saveBackupCodes')->with(42, self::callback(
            static function (array $saved) use (&$hashes): bool {
                $hashes = $saved;

                return count($saved) === 8;
            }
        ), null, '', null);
        $result = (new GenerateEmployeeBackupCodesHandler($manager))->handle(new GenerateEmployeeBackupCodesCommand(42));
        self::assertSame(42, $result->getValue());
        self::assertSame(array_map(static fn (string $code): string => 'hashed:' . $code, $result->getBackupCodes()), $hashes);
    }

    public function testGenerationCannotReplaceExistingCodes(): void
    {
        [$manager, $repository, , , $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash']);
        $repository->expects(self::never())->method('saveBackupCodes');
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::CONFIRMATION_REQUIRED);
        $manager->generateBackupCodes(42);
    }

    public function testEmailPreparationSavesBeforeSending(): void
    {
        [$manager, $repository, , $mailer, $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash']);
        $saved = false;
        $repository->expects(self::once())->method('replaceEmailOtp')->with(self::callback(
            static function (EmployeeEmailOtp $challenge) use (&$saved): bool {
                $saved = true;

                return EmployeeEmailOtp::BACKUP_REGENERATION === $challenge->context && 1 === preg_match('/^[0-9]{6}$/', $challenge->code);
            }
        ))->willReturn(true);
        $mailer->expects(self::once())->method('sendAuthCode')->willReturnCallback(
            static function () use (&$saved): void { self::assertTrue($saved); }
        );
        self::assertSame('email', (new PrepareEmployeeBackupCodeRegenerationHandler($manager))->handle(new PrepareEmployeeBackupCodeRegenerationCommand(42)));
        self::assertNull($employee->getEmailAuthCode());
    }

    public function testEmailPreparationKeepsExistingCodeUnlessResendRequested(): void
    {
        [$manager, $repository, , $mailer, $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $repository->expects(self::never())->method('replaceEmailOtp');
        $mailer->expects(self::once())->method('sendAuthCode');
        $manager->prepareRegeneration(42, 'email');
        self::assertSame('123456', $employee->getEmailAuthCode());
        $manager->prepareRegeneration(42, 'email', true);
    }

    public function testTotpPreparationDoesNotSendEmail(): void
    {
        [$manager, $repository, , $mailer, $employee] = $this->dependencies(true, true);
        $employee->setTwoFactorBackupCodes(['existing-hash']);
        $repository->expects(self::never())->method('replaceEmailOtp');
        $mailer->expects(self::never())->method('sendAuthCode');
        self::assertSame('totp', $manager->prepareRegeneration(42, 'totp', true));
    }

    public function testValidEmailCodeReplacesCodesAndClearsEmailCodeInOneWrite(): void
    {
        [$manager, $repository, , , $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $repository->expects(self::once())->method('saveBackupCodes')->with(42, self::callback(static fn (array $codes): bool => count($codes) === 8), '["existing-hash"]', 'email', null, self::isInstanceOf(EmployeeEmailOtp::class), self::isInstanceOf(DateTimeImmutable::class));
        $result = (new RegenerateEmployeeBackupCodesHandler($manager))->handle(new RegenerateEmployeeBackupCodesCommand(42, 'email', ' 123456 '));
        self::assertCount(8, $result->getBackupCodes());
    }

    public function testInvalidCodeNeverReplacesBackupCodes(): void
    {
        [$manager, $repository, , , $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $repository->expects(self::never())->method('saveBackupCodes');
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::INVALID_CODE);
        $manager->regenerateBackupCodes(42, 'email', '000000');
    }

    public function testValidTotpCodeUsesAuthenticator(): void
    {
        [$manager, $repository, $totp, , $employee] = $this->dependencies(true, true);
        $employee->setTwoFactorBackupCodes(['existing-hash']);
        $totp->expects(self::once())->method('checkCode')->with($employee, '123456')->willReturn(true);
        $repository->expects(self::once())->method('saveBackupCodes');
        self::assertCount(8, $manager->regenerateBackupCodes(42, 'totp', '123456')->getBackupCodes());
    }

    public function testSuccessfulRegenerationsDoNotConsumeTheVerificationBudget(): void
    {
        [$manager, $repository, $totp, , $employee] = $this->dependencies(true, true);
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $totp->method('checkCode')->willReturn(true);
        $repository->expects(self::exactly(8))->method('saveBackupCodes');
        for ($i = 0; $i < 8; ++$i) {
            $manager->regenerateBackupCodes(42, 'totp', '123456');
        }
        for ($i = 0; $i < 5; ++$i) {
            try {
                $manager->regenerateBackupCodes(42, 'email', '000000');
                self::fail('An incorrect code must fail.');
            } catch (EmployeeTwoFactorException $e) {
                self::assertSame(EmployeeTwoFactorException::INVALID_CODE, $e->getCode());
            }
        }
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::TOO_MANY_ATTEMPTS);
        $manager->regenerateBackupCodes(42, 'totp', '123456');
    }

    public function testSuccessfulRegenerationPreservesEarlierFailures(): void
    {
        [$manager, $repository, $totp, , $employee] = $this->dependencies(true, true);
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $totp->method('checkCode')->willReturn(true);
        $repository->expects(self::once())->method('saveBackupCodes');
        for ($i = 0; $i < 4; ++$i) {
            try {
                $manager->regenerateBackupCodes(42, 'email', '000000');
                self::fail('An incorrect code must fail.');
            } catch (EmployeeTwoFactorException $e) {
                self::assertSame(EmployeeTwoFactorException::INVALID_CODE, $e->getCode());
            }
        }
        $manager->regenerateBackupCodes(42, 'totp', '123456');
        try {
            $manager->regenerateBackupCodes(42, 'email', '000000');
            self::fail('The fifth incorrect code must fail.');
        } catch (EmployeeTwoFactorException $e) {
            self::assertSame(EmployeeTwoFactorException::INVALID_CODE, $e->getCode());
        }
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::TOO_MANY_ATTEMPTS);
        $manager->regenerateBackupCodes(42, 'totp', '123456');
    }

    public function testTechnicalRegenerationErrorsReleaseTheLockWithoutConsumingQuota(): void
    {
        [$manager, $repository, $totp, , $employee, , $lockFactory] = $this->dependencies(true, true);
        $employee->setTwoFactorBackupCodes(['existing-hash']);
        $totp->method('checkCode')->willReturn(true);
        $repository->method('saveBackupCodes')->willThrowException(new RuntimeException('Database unavailable'));
        for ($i = 0; $i < 6; ++$i) {
            try {
                $manager->regenerateBackupCodes(42, 'totp', '123456');
                self::fail('The database error must propagate.');
            } catch (RuntimeException $e) {
                self::assertSame('Database unavailable', $e->getMessage());
            }
            $probe = $lockFactory->createLock('employee_2fa_verification:42');
            try {
                self::assertTrue($probe->acquire());
            } finally {
                $probe->release();
            }
        }
    }

    public function testFailedWriteDoesNotReturnPlaintext(): void
    {
        [$manager, $repository] = $this->dependencies();
        $repository->method('saveBackupCodes')->willThrowException(new RuntimeException('Write failed'));
        $this->expectException(RuntimeException::class);
        $manager->generateBackupCodes(42);
    }

    public function testConcurrentWriteDoesNotReturnPlaintext(): void
    {
        [$manager, $repository] = $this->dependencies();
        $repository->method('saveBackupCodes')->willThrowException(new EmployeeTwoFactorException(
            'Concurrent update', EmployeeTwoFactorException::CONCURRENT_UPDATE
        ));
        $this->expectException(EmployeeTwoFactorException::class);
        $this->expectExceptionCode(EmployeeTwoFactorException::CONCURRENT_UPDATE);
        $manager->generateBackupCodes(42);
    }

    /**
     * @param 'initialize'|'generate'|'prepare'|'regenerate'|'query' $operation
     */
    private function execute(EmployeeTwoFactorManager $manager, string $operation, int $employeeId): void
    {
        match ($operation) {
            'initialize' => $manager->initializeTotpSecret($employeeId),
            'generate' => $manager->generateBackupCodes($employeeId),
            'prepare' => $manager->prepareRegeneration($employeeId, ''),
            'regenerate' => $manager->regenerateBackupCodes($employeeId, '', ''),
            'query' => $manager->getSettings($employeeId),
        };
    }

    private function dependencies(bool $global = true, bool $totpEnabled = false): array
    {
        $employee = $this->createPartialMock(Employee::class, ['isTotpAuthenticationEnabled', 'getId']);
        $employee->method('getId')->willReturn(42);
        $employee->method('isTotpAuthenticationEnabled')->willReturn($totpEnabled);
        $employee->setTwoFactorEnabled(true)->setTwoFactorEmailEnabled(true);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getForTwoFactor')->with(42)->willReturn($employee);
        $repository->method('getBackupCodesSnapshot')->with(42)->willReturnCallback(
            static fn (): ?string => null === $employee->getTwoFactorBackupCodes() ? null : json_encode($employee->getTwoFactorBackupCodes(), JSON_THROW_ON_ERROR)
        );
        $repository->method('getEmailOtpState')->willReturnCallback(static fn (): array => [
            'id_employee' => 42,
            'email' => 'employee@example.com',
            'active' => 1,
            'two_factor_enabled' => 1,
            'two_factor_email_enabled' => 1,
            'two_factor_trusted_version' => 1,
            'two_factor_email_auth_code' => $employee->getEmailAuthCode(),
            'two_factor_email_auth_code_issued_at' => '2026-11-27 12:00:00.000000',
            'two_factor_email_auth_code_context' => EmployeeEmailOtp::BACKUP_REGENERATION,
            'two_factor_email_auth_code_version' => 1,
        ]);
        $actor = $this->createMock(ContextEmployee::class);
        $actor->method('getId')->willReturn(42);
        $checker = $this->createMock(EmployeeFormAccessCheckerInterface::class);
        $checker->method('isRestrictedAccess')->with(42)->willReturn(true);
        $config = $this->createMock(ConfigurationInterface::class);
        $config->method('get')->with('PS_BACKOFFICE_2FA')->willReturn($global);
        $hasher = $this->createMock(PasswordHasherInterface::class);
        $hasher->method('hash')->willReturnCallback(static fn (string $code): string => 'hashed:' . $code);
        $totp = $this->createMock(TotpAuthenticatorInterface::class);
        $mailer = $this->createMock(AuthCodeMailerInterface::class);
        $encryptor = new TotpSecretEncryptor(PhpEncryption::createNewRandomKey());

        $lockFactory = new LockFactory(new InMemoryStore());
        $limiter = new EmployeeTwoFactorRateLimiter(
            new RateLimiterFactory(['id' => 'check', 'policy' => 'fixed_window', 'limit' => 5, 'interval' => '15 minutes'], new InMemoryStorage()),
            new RateLimiterFactory(['id' => 'send', 'policy' => 'fixed_window', 'limit' => 3, 'interval' => '15 minutes'], new InMemoryStorage()),
            $lockFactory
        );
        $otpManager = new EmployeeEmailOtpManager($repository, $limiter, $mailer, $config, new MockClock('2026-11-27 12:00:00 UTC'));

        return [new EmployeeTwoFactorManager($repository, new EmployeeContext($actor, []), $checker, $config, new EmployeeBackupCodeGenerator($hasher), $totp, $encryptor, $otpManager, $limiter), $repository, $totp, $mailer, $employee, $encryptor, $lockFactory];
    }
}
