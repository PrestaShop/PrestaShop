<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Sets the two-factor authentication secret
 */
class SetEmployeeTwoFactorSecretCommand
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @param int $employeeId
     */
    public function __construct(
        int $employeeId,
        private string $secret,
        private string $secretPlain,
    ) {
        $this->employeeId = new EmployeeId($employeeId);
    }

    public function getSecret()
    {
        return $this->secret;
    }

    public function getSecretPlain()
    {
        return $this->secretPlain;
    }

    public function getEmployeeId()
    {
        return $this->employeeId;
    }
}
