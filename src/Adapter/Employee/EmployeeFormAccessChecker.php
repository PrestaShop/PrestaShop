<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShop\PrestaShop\Core\Employee\EmployeeDataProviderInterface;

/**
 * Class EmployeeFormAccessChecker checks employee's access to the employee form.
 */
final class EmployeeFormAccessChecker implements EmployeeFormAccessCheckerInterface
{
    /**
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @var EmployeeDataProviderInterface
     */
    private $employeeDataProvider;

    /**
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     * @param EmployeeDataProviderInterface $employeeDataProvider
     */
    public function __construct(
        ContextEmployeeProviderInterface $contextEmployeeProvider,
        EmployeeDataProviderInterface $employeeDataProvider
    ) {
        $this->contextEmployeeProvider = $contextEmployeeProvider;
        $this->employeeDataProvider = $employeeDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function isRestrictedAccess(int $employeeId): bool
    {
        if (!is_int($employeeId)) {
            throw new InvalidArgumentException(sprintf('Employee ID must be an integer, %s given', gettype($employeeId)));
        }

        return $employeeId === $this->contextEmployeeProvider->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function canAccessEditFormFor(int $employeeId): bool
    {
        // To access super admin edit form you must be a super admin.
        if ($this->employeeDataProvider->isSuperAdmin($employeeId)) {
            return $this->contextEmployeeProvider->isSuperAdmin();
        }

        return true;
    }
}
