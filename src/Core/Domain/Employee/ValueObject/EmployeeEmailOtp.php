<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use DateTimeImmutable;

/**
 * Snapshot used to conditionally consume a single email challenge.
 */
final class EmployeeEmailOtp
{
    public const LOGIN = 'login';
    public const BACKUP_REGENERATION = 'backup_regeneration';
    public const LIFETIME = 600;

    public function __construct(
        public readonly int $employeeId,
        public readonly string $code,
        public readonly DateTimeImmutable $issuedAt,
        public readonly string $context,
        public readonly int $version,
    ) {
    }
}
