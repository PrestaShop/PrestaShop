<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Employee\Access;

use PrestaShop\PrestaShop\Core\Employee\EmployeeDataProviderInterface;

/**
 * Class ProfileAccessChecker checks profile access for employee.
 */
final class ProfileAccessChecker implements ProfileAccessCheckerInterface
{
    /**
     * @var EmployeeDataProviderInterface
     */
    private $employeeDataProvider;

    /**
     * @var int
     */
    private $superAdminProfileId;

    /**
     * @param EmployeeDataProviderInterface $employeeDataProvider
     * @param int $superAdminProfileId
     */
    public function __construct(
        EmployeeDataProviderInterface $employeeDataProvider,
        $superAdminProfileId
    ) {
        $this->employeeDataProvider = $employeeDataProvider;
        $this->superAdminProfileId = $superAdminProfileId;
    }

    /**
     * {@inheritdoc}
     */
    public function canEmployeeAccessProfile($employeeId, $profileId)
    {
        if ($this->employeeDataProvider->isSuperAdmin($employeeId)) {
            return true;
        }

        return $profileId !== $this->superAdminProfileId;
    }
}
