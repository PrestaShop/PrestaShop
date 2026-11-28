<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Backup\BackupCodeManagerInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

final class EmployeeBackupCodeManager implements BackupCodeManagerInterface
{
    public function __construct(
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly EmployeeRepository $employeeRepository,
    ) {
    }

    public function isBackupCode(object $user, string $code): bool
    {
        if (!$user instanceof Employee) {
            return false;
        }

        foreach ($user->getTwoFactorBackupCodes() ?? [] as $hashedBackupCode) {
            if ($this->passwordHasher->verify($hashedBackupCode, $code)) {
                return true;
            }
        }

        return false;
    }

    public function invalidateBackupCode(object $user, string $code): void
    {
        if (!$user instanceof Employee) {
            throw new BadCredentialsException('The backup code is no longer valid.');
        }

        try {
            $snapshot = $this->employeeRepository->getBackupCodesSnapshot($user->getId());
            $currentCodes = null === $snapshot ? [] : json_decode($snapshot, true, 512, JSON_THROW_ON_ERROR);
            $remainingCodes = [];
            $hasInvalidatedCode = false;
            foreach ($currentCodes as $hashedCode) {
                if (!$hasInvalidatedCode && $this->passwordHasher->verify($hashedCode, $code)) {
                    $hasInvalidatedCode = true;

                    continue;
                }
                $remainingCodes[] = $hashedCode;
            }
            if (!$hasInvalidatedCode) {
                throw new BadCredentialsException('The backup code is no longer valid.');
            }

            $this->employeeRepository->saveBackupCodes($user->getId(), $remainingCodes, $snapshot);
            // Do not dirty the managed user: a later ORM flush must not replay an obsolete code set.
        } catch (EmployeeException $e) {
            // Scheb grants authentication after this void method returns, so a conflict must fail closed.
            throw new BadCredentialsException('The backup code is no longer valid.', 0, $e);
        }
    }
}
