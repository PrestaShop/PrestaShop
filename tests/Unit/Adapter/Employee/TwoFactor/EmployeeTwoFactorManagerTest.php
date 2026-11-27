<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Employee\TwoFactor;

use PhpEncryption;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeBackupCodeGenerator;
use PrestaShop\PrestaShop\Adapter\Employee\TwoFactor\EmployeeTwoFactorManager;
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
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\TotpSecretEncryptor;
use RuntimeException;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

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
        $repository->expects(self::never())->method('saveEmailAuthCode');
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
        ), null, false, '', null);
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
        $repository->expects(self::once())->method('saveEmailAuthCode')->with(42, self::callback(
            static function (string $code) use (&$saved): bool {
                $saved = true;

                return preg_match('/^[0-9]{6}$/', $code) === 1;
            }
        ));
        $mailer->expects(self::once())->method('sendAuthCode')->with($employee)->willReturnCallback(
            static function () use (&$saved): void { self::assertTrue($saved); }
        );
        self::assertSame('email', (new PrepareEmployeeBackupCodeRegenerationHandler($manager))->handle(new PrepareEmployeeBackupCodeRegenerationCommand(42)));
    }

    public function testEmailPreparationKeepsExistingCodeUnlessResendRequested(): void
    {
        [$manager, $repository, , $mailer, $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $repository->expects(self::once())->method('saveEmailAuthCode');
        $mailer->expects(self::once())->method('sendAuthCode');
        $manager->prepareRegeneration(42, 'email');
        self::assertSame('123456', $employee->getEmailAuthCode());
        $manager->prepareRegeneration(42, 'email', true);
    }

    public function testTotpPreparationDoesNotSendEmail(): void
    {
        [$manager, $repository, , $mailer, $employee] = $this->dependencies(true, true);
        $employee->setTwoFactorBackupCodes(['existing-hash']);
        $repository->expects(self::never())->method('saveEmailAuthCode');
        $mailer->expects(self::never())->method('sendAuthCode');
        self::assertSame('totp', $manager->prepareRegeneration(42, 'totp', true));
    }

    public function testValidEmailCodeReplacesCodesAndClearsEmailCodeInOneWrite(): void
    {
        [$manager, $repository, , , $employee] = $this->dependencies();
        $employee->setTwoFactorBackupCodes(['existing-hash'])->setEmailAuthCode('123456');
        $repository->expects(self::once())->method('saveBackupCodes')->with(42, self::callback(static fn (array $codes): bool => count($codes) === 8), '["existing-hash"]', true, 'email', '123456');
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
        $employee = $this->createPartialMock(Employee::class, ['isTotpAuthenticationEnabled']);
        $employee->method('isTotpAuthenticationEnabled')->willReturn($totpEnabled);
        $employee->setTwoFactorEnabled(true)->setTwoFactorEmailEnabled(true);
        $repository = $this->createMock(EmployeeRepository::class);
        $repository->method('getForTwoFactor')->with(42)->willReturn($employee);
        $repository->method('getBackupCodesSnapshot')->with(42)->willReturnCallback(
            static fn (): ?string => null === $employee->getTwoFactorBackupCodes() ? null : json_encode($employee->getTwoFactorBackupCodes(), JSON_THROW_ON_ERROR)
        );
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

        return [new EmployeeTwoFactorManager($repository, new EmployeeContext($actor, []), $checker, $config, new EmployeeBackupCodeGenerator($hasher), $totp, $encryptor, $mailer), $repository, $totp, $mailer, $employee, $encryptor];
    }
}
