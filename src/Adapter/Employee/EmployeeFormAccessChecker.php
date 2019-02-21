<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Employee;
use PrestaShop\PrestaShop\Core\Employee\Access\EmployeeFormAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;

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
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     */
    public function __construct(ContextEmployeeProviderInterface $contextEmployeeProvider)
    {
        $this->contextEmployeeProvider = $contextEmployeeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function isRestrictedAccess($employeeId)
    {
        if (!is_int($employeeId)) {
            throw new \InvalidArgumentException(sprintf(
                'Employee ID must be an integer, %s given',
                gettype($employeeId)
            ));
        }

        return $employeeId === $this->contextEmployeeProvider->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin($employeeId)
    {
        $employee = new Employee($employeeId);

        return $employee->isSuperAdmin();
    }

    /**
     * {@inheritdoc}
     */
    public function canAccessEditFormFor($employeeId)
    {
        // To access super admin edit form you must be a super admin.
        if ($this->isSuperAdmin($employeeId)) {
            return $this->contextEmployeeProvider->isSuperAdmin();
        }

        return true;
    }
}
