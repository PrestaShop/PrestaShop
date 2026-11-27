<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

/**
 * Newly generated plaintext codes, returned only after successful persistence.
 */
final class GeneratedEmployeeBackupCodes
{
    /**
     * @param string[] $backupCodes
     */
    public function __construct(
        private readonly int $employeeId,
        private readonly array $backupCodes,
    ) {
    }

    /**
     * Allows identifiable forms to persist extra properties using the employee ID.
     */
    public function getValue(): int
    {
        return $this->employeeId;
    }

    /**
     * @return string[]
     */
    public function getBackupCodes(): array
    {
        return $this->backupCodes;
    }
}
