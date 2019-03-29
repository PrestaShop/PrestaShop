<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Context;
use Employee;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\AdminEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\CannotDeleteWarehouseManagerException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeCannotChangeItselfException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use Warehouse;

/**
 * Class AbstractEmployeeStatusHandler.
 */
abstract class AbstractEmployeeHandler extends AbstractObjectModelHandler
{
    /**
     * @param EmployeeId $employeeId
     * @param Employee $employee
     *
     * @throws EmployeeNotFoundException
     */
    protected function assertEmployeeWasFoundById(EmployeeId $employeeId, Employee $employee)
    {
        if (!$employee->id) {
            throw new EmployeeNotFoundException(
                $employeeId,
                sprintf(
                    'Employee with id "%s" cannot be found.',
                    $employeeId->getValue()
                )
            );
        }
    }

    /**
     * If employee is admin and no other admins exists, then terminate command execution.
     *
     * @param Employee $employee
     */
    protected function assertEmployeeIsNotTheOnlyAdminInShop(Employee $employee)
    {
        if ($employee->isLastAdmin()) {
            throw new AdminEmployeeException(
                sprintf(
                    'Employee with id %s is the only admin in shop and status cannot be changed.',
                    $employee->id
                ),
                AdminEmployeeException::CANNOT_CHANGE_LAST_ADMIN
            );
        }
    }

    /**
     * If logged in employee is trying to toggle itself, then terminate execution.
     *
     * @param Employee $employee
     */
    protected function assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee(Employee $employee)
    {
        if (Context::getContext()->employee->id === $employee->id) {
            throw new EmployeeCannotChangeItselfException(
                'Employee cannot change status of itself.',
                EmployeeCannotChangeItselfException::CANNOT_CHANGE_STATUS
            );
        }
    }

    /**
     * Make sure that given employee does not manage any warehouse.
     *
     * Even though Warehouse feature was removed in 1.7
     * but the code related to it still exists
     * thus assertion is kept for BC i guess.
     *
     * @param Employee $employee
     */
    protected function assertEmployeeDoesNotManageWarehouse(Employee $employee)
    {
        $warehouses = Warehouse::getWarehousesByEmployee($employee->id);

        if (count($warehouses) > 0) {
            throw new CannotDeleteWarehouseManagerException(
                sprintf('Employee with id %s is warehouse manager and cannot be deleted.', $employee->id)
            );
        }
    }
}
