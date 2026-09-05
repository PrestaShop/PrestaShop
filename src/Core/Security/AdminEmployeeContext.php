<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

/** An authenticated BO employee, without credentials or session tokens. */
final class AdminEmployeeContext
{
    public function __construct(
        private readonly int $employeeId,
        private readonly int $profileId,
    ) {
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getProfileId(): int
    {
        return $this->profileId;
    }
}
