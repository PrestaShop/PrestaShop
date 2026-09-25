<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\SchebTwoFactor;

use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShopBundle\Entity\Employee\Employee;
use Scheb\TwoFactorBundle\Model\PersisterInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Backup\BackupCodeManagerInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

final class EmployeeBackupCodeManager implements BackupCodeManagerInterface
{
    private const DEFAULT_CODE_COUNT = 8;
    private const DEFAULT_CODE_LENGTH = 10;
    private const CODE_CHARACTERS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function __construct(
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly PersisterInterface $persister,
    ) {
    }

    /**
     * @return string[]
     */
    public function generateBackupCodes(
        int $codeCount = self::DEFAULT_CODE_COUNT,
        int $codeLength = self::DEFAULT_CODE_LENGTH
    ): array {
        $backupCodes = [];

        while (count($backupCodes) < $codeCount) {
            $backupCode = RandomString::generateFromCharacters(self::CODE_CHARACTERS, $codeLength);
            $backupCodes[$backupCode] = $backupCode;
        }

        return array_values($backupCodes);
    }

    /**
     * @param string[] $backupCodes
     *
     * @return string[]
     */
    public function hashBackupCodes(array $backupCodes): array
    {
        return array_map(
            fn (string $backupCode): string => $this->passwordHasher->hash($backupCode),
            array_values($backupCodes)
        );
    }

    /**
     * @return array{plainBackupCodes: string[], hashedBackupCodes: string[]}
     */
    public function generateBackupCodeSet(
        int $codeCount = self::DEFAULT_CODE_COUNT,
        int $codeLength = self::DEFAULT_CODE_LENGTH
    ): array {
        $plainBackupCodes = $this->generateBackupCodes($codeCount, $codeLength);

        return [
            'plainBackupCodes' => $plainBackupCodes,
            'hashedBackupCodes' => $this->hashBackupCodes($plainBackupCodes),
        ];
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
            return;
        }

        $remainingBackupCodes = [];
        $hasInvalidatedCode = false;

        foreach ($user->getTwoFactorBackupCodes() ?? [] as $hashedBackupCode) {
            if (!$hasInvalidatedCode && $this->passwordHasher->verify($hashedBackupCode, $code)) {
                $hasInvalidatedCode = true;

                continue;
            }

            $remainingBackupCodes[] = $hashedBackupCode;
        }

        if (!$hasInvalidatedCode) {
            return;
        }

        $user->setTwoFactorBackupCodes($remainingBackupCodes);
        $this->persister->persist($user);
    }
}
