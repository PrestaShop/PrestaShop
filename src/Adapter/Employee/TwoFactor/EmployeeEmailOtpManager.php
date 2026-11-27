<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee\TwoFactor;

use DateTimeImmutable;
use DateTimeZone;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeTwoFactorException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeEmailOtp;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Psr\Clock\ClockInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;

final class EmployeeEmailOtpManager
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EmployeeTwoFactorRateLimiter $rateLimiter,
        private readonly AuthCodeMailerInterface $mailer,
        private readonly ConfigurationInterface $configuration,
        private readonly ClockInterface $clock,
    ) {
    }

    public function prepare(Employee $employee, string $context, bool $resend = false): EmailOtpPreparation
    {
        $state = $this->employeeRepository->getEmailOtpState($employee->getId());
        if (!$this->isEnabled($state)) {
            throw new EmployeeTwoFactorException('Email two-factor authentication is disabled.', EmployeeTwoFactorException::DISABLED);
        }

        $challenge = $this->getValidChallenge($state, $context);
        if (null !== $challenge && !$resend) {
            return new EmailOtpPreparation(EmailOtpPreparation::AVAILABLE);
        }

        $limit = $this->rateLimiter->consumeSend($employee->getId());
        if (!$limit->isAccepted()) {
            return new EmailOtpPreparation(EmailOtpPreparation::THROTTLED, $limit->getRetryAfter());
        }

        if (null === $challenge) {
            $code = (string) random_int(100000, 999999);
            // A newly issued challenge must not accidentally reuse its predecessor's code.
            while ($code === $state['two_factor_email_auth_code']) {
                $code = (string) random_int(100000, 999999);
            }
            $challenge = new EmployeeEmailOtp(
                $employee->getId(),
                $code,
                $this->now(),
                $context,
                (int) $state['two_factor_trusted_version']
            );
            if (!$this->employeeRepository->replaceEmailOtp($challenge, $state)) {
                return new EmailOtpPreparation(EmailOtpPreparation::CHANGED);
            }
        }

        // Never dirty the managed authentication user: a later flush could replay stale OTP data.
        $recipient = clone $employee;
        $recipient->setEmail($state['email']);
        $recipient->setEmailAuthCode($challenge->code);
        try {
            $this->mailer->sendAuthCode($recipient);
        } catch (EmployeeTwoFactorException $e) {
            if (EmployeeTwoFactorException::EMAIL_DELIVERY_FAILED !== $e->getCode()) {
                throw $e;
            }

            return new EmailOtpPreparation(EmailOtpPreparation::FAILED);
        }

        return new EmailOtpPreparation(EmailOtpPreparation::SENT);
    }

    public function hasValidChallenge(int $employeeId, string $context): bool
    {
        return null !== $this->getValidChallenge($this->employeeRepository->getEmailOtpState($employeeId), $context);
    }

    public function getVerificationChallenge(int $employeeId, string $context, string $code): ?EmployeeEmailOtp
    {
        $challenge = $this->getValidChallenge($this->employeeRepository->getEmailOtpState($employeeId), $context);
        $code = str_replace(' ', '', trim($code));

        return null !== $challenge && 1 === preg_match('/^[0-9]{6}$/D', $code) && hash_equals($challenge->code, $code)
            ? $challenge
            : null;
    }

    public function consumeLogin(int $employeeId, string $code): bool
    {
        $challenge = $this->getVerificationChallenge($employeeId, EmployeeEmailOtp::LOGIN, $code);

        return null !== $challenge && $this->employeeRepository->consumeEmailOtp($challenge, $this->now());
    }

    public function now(): DateTimeImmutable
    {
        return $this->clock->now()->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * @param array<string, mixed> $state
     */
    private function getValidChallenge(array $state, string $context): ?EmployeeEmailOtp
    {
        if (
            !$this->isEnabled($state)
            || null === $state['two_factor_email_auth_code']
            || null === $state['two_factor_email_auth_code_issued_at']
            || $context !== $state['two_factor_email_auth_code_context']
            || (int) $state['two_factor_trusted_version'] !== (int) $state['two_factor_email_auth_code_version']
            || null === $state['two_factor_email_auth_code_version']
        ) {
            return null;
        }

        $issuedAt = new DateTimeImmutable($state['two_factor_email_auth_code_issued_at'], new DateTimeZone('UTC'));
        $now = $this->now();
        if ($issuedAt > $now || $issuedAt <= $now->modify('-' . EmployeeEmailOtp::LIFETIME . ' seconds')) {
            return null;
        }

        return new EmployeeEmailOtp((int) $state['id_employee'], $state['two_factor_email_auth_code'], $issuedAt, $context, (int) $state['two_factor_email_auth_code_version']);
    }

    /**
     * @param array<string, mixed> $state
     */
    private function isEnabled(array $state): bool
    {
        return (bool) $this->configuration->get('PS_BACKOFFICE_2FA')
            && (bool) $state['active']
            && (bool) $state['two_factor_enabled']
            && (bool) $state['two_factor_email_enabled'];
    }
}
