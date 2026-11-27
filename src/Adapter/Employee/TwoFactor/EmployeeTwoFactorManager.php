<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee\TwoFactor;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeTwoFactorSettings;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use PrestaShopBundle\SchebTwoFactor\TotpSecretEncryptor;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;

/**
 * Application services for employee self-service 2FA operations in the admin kernel.
 */
final class EmployeeTwoFactorManager
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EmployeeContext $employeeContext,
        private readonly EmployeeFormAccessCheckerInterface $formAccessChecker,
        private readonly ConfigurationInterface $configuration,
        private readonly EmployeeBackupCodeGenerator $backupCodeGenerator,
        private readonly TotpAuthenticatorInterface $totpAuthenticator,
        private readonly TotpSecretEncryptor $secretEncryptor,
        private readonly AuthCodeMailerInterface $mailer,
    ) {
    }

    public function getSettings(int $employeeId, string $preferredProvider = ''): EmployeeTwoFactorSettings
    {
        $employee = $this->getSelf($employeeId);
        $enabled = (bool) $this->configuration->get('PS_BACKOFFICE_2FA');
        $providers = $enabled ? $this->getAvailableProviders($employee) : [];
        $secret = '';
        $qrCodeSrc = '';
        // A query can display an existing pending secret, but must never create one.
        if ($enabled && !$employee->isTotpAuthenticationEnabled() && $employee->getTwoFactorSecret()) {
            $secret = $this->secretEncryptor->decrypt($employee->getTwoFactorSecret());
            $employee->setTwoFactorTotpSecretPlain($secret);
            $qrCodeSrc = Builder::create()
                ->writer(new PngWriter())
                ->data($this->totpAuthenticator->getQRContent($employee))
                ->size(220)
                ->margin(10)
                ->build()
                ->getDataUri();
        }

        $provider = $this->selectProvider($providers, $preferredProvider);

        return new EmployeeTwoFactorSettings(
            [] !== $providers,
            !empty($employee->getTwoFactorBackupCodes()),
            $providers,
            $provider,
            $secret,
            $qrCodeSrc,
            'email' === $provider && null === $employee->getEmailAuthCode()
        );
    }

    public function initializeTotpSecret(int $employeeId): void
    {
        $employee = $this->getSelf($employeeId);
        $this->assertGloballyEnabled();
        if ($employee->getTwoFactorSecret()) {
            return;
        }
        if ($employee->isTotpAuthenticationEnabled()) {
            throw new EmployeeTwoFactorException('The active TOTP secret cannot be replaced.', EmployeeTwoFactorException::NOT_CONFIGURED);
        }

        $this->employeeRepository->initializeTotpSecret(
            $employeeId,
            $this->secretEncryptor->encrypt($this->totpAuthenticator->generateSecret())
        );
    }

    public function generateBackupCodes(int $employeeId): GeneratedEmployeeBackupCodes
    {
        $employee = $this->getConfiguredSelf($employeeId);
        if (!empty($employee->getTwoFactorBackupCodes())) {
            throw new EmployeeTwoFactorException('Confirm regeneration before replacing backup codes.', EmployeeTwoFactorException::CONFIRMATION_REQUIRED);
        }

        return $this->persistBackupCodes($employeeId, $this->getBackupCodesSnapshot($employeeId, $employee));
    }

    /**
     * Returns the selected provider for the redirect, without exposing the email code.
     */
    public function prepareRegeneration(int $employeeId, string $preferredProvider, bool $resend = false): string
    {
        $employee = $this->getConfiguredSelf($employeeId);
        if (empty($employee->getTwoFactorBackupCodes())) {
            throw new EmployeeTwoFactorException('No backup codes to regenerate.', EmployeeTwoFactorException::NOT_CONFIGURED);
        }
        $provider = $this->selectProvider($this->getAvailableProviders($employee), $preferredProvider);
        if ('email' === $provider && ($resend || null === $employee->getEmailAuthCode())) {
            $code = RandomString::generateFromCharacters('0123456789', 6);
            $this->employeeRepository->saveEmailAuthCode($employeeId, $code);
            $employee->setEmailAuthCode($code);
            $this->mailer->sendAuthCode($employee);
        }

        return $provider;
    }

    public function regenerateBackupCodes(int $employeeId, string $preferredProvider, string $code): GeneratedEmployeeBackupCodes
    {
        $employee = $this->getConfiguredSelf($employeeId);
        $provider = $this->selectProvider($this->getAvailableProviders($employee), $preferredProvider);
        $code = trim($code);
        $valid = '' !== $code && ('totp' === $provider
            ? $this->totpAuthenticator->checkCode($employee, $code)
            : (null !== $employee->getEmailAuthCode() && hash_equals($employee->getEmailAuthCode(), $code)));
        if (!$valid) {
            // Preserve the existing retry behavior without doing any work on GET.
            $this->prepareRegeneration($employeeId, $provider);
            throw new EmployeeTwoFactorException('Invalid verification code.', EmployeeTwoFactorException::INVALID_CODE);
        }

        return $this->persistBackupCodes(
            $employeeId,
            $this->getBackupCodesSnapshot($employeeId, $employee),
            true,
            $provider,
            'email' === $provider ? $employee->getEmailAuthCode() : $employee->getTwoFactorSecret()
        );
    }

    private function persistBackupCodes(
        int $employeeId,
        ?string $expectedCodes,
        bool $clearEmailCode = false,
        string $provider = '',
        ?string $expectedVerificationValue = null,
    ): GeneratedEmployeeBackupCodes {
        $codeSet = $this->backupCodeGenerator->generateBackupCodeSet();
        $this->employeeRepository->saveBackupCodes($employeeId, $codeSet['hashedBackupCodes'], $expectedCodes, $clearEmailCode, $provider, $expectedVerificationValue);

        return new GeneratedEmployeeBackupCodes($employeeId, $codeSet['plainBackupCodes']);
    }

    private function getBackupCodesSnapshot(int $employeeId, Employee $employee): ?string
    {
        $snapshot = $this->employeeRepository->getBackupCodesSnapshot($employeeId);
        $codes = null === $snapshot ? null : json_decode($snapshot, true, 512, JSON_THROW_ON_ERROR);
        if ($codes !== $employee->getTwoFactorBackupCodes()) {
            throw new EmployeeTwoFactorException('Backup codes changed during this operation.', EmployeeTwoFactorException::CONCURRENT_UPDATE);
        }

        return $snapshot;
    }

    private function getSelf(int $employeeId): Employee
    {
        if ($employeeId !== $this->employeeContext->getEmployee()?->getId()
            || !$this->formAccessChecker->isRestrictedAccess($employeeId)) {
            throw new EmployeeTwoFactorException('Two-factor settings can only be managed by their owner.', EmployeeTwoFactorException::NOT_SELF);
        }

        return $this->employeeRepository->getForTwoFactor($employeeId);
    }

    private function getConfiguredSelf(int $employeeId): Employee
    {
        $employee = $this->getSelf($employeeId);
        $this->assertGloballyEnabled();
        if ([] === $this->getAvailableProviders($employee)) {
            throw new EmployeeTwoFactorException('Enable at least one two-factor authentication method.', EmployeeTwoFactorException::NOT_CONFIGURED);
        }

        return $employee;
    }

    private function assertGloballyEnabled(): void
    {
        if (!$this->configuration->get('PS_BACKOFFICE_2FA')) {
            throw new EmployeeTwoFactorException('Two-factor authentication is disabled.', EmployeeTwoFactorException::DISABLED);
        }
    }

    /**
     * @return string[]
     */
    private function getAvailableProviders(Employee $employee): array
    {
        $providers = [];
        if ($employee->isTotpAuthenticationEnabled()) {
            $providers[] = 'totp';
        }
        if ($employee->isEmailAuthEnabled()) {
            $providers[] = 'email';
        }

        return $providers;
    }

    /**
     * @param string[] $providers
     */
    private function selectProvider(array $providers, string $preferredProvider): string
    {
        return in_array($preferredProvider, $providers, true) ? $preferredProvider : ($providers[0] ?? '');
    }
}
