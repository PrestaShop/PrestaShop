<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Context;
use Employee;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\AdminEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeCannotChangeItselfException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use Profile;

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
            throw new EmployeeNotFoundException($employeeId, sprintf('Employee with id "%s" cannot be found.', $employeeId->getValue()));
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
            throw new AdminEmployeeException(sprintf('Employee with id %s is the only admin in shop and status cannot be changed.', $employee->id), AdminEmployeeException::CANNOT_CHANGE_LAST_ADMIN);
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
            throw new EmployeeCannotChangeItselfException('Employee cannot change status of itself.', EmployeeCannotChangeItselfException::CANNOT_CHANGE_STATUS);
        }
    }

    /**
     * @throws EmployeeConstraintException
     */
    protected function assertHomepageIsAccessible(int $tabId, int $profileId): void
    {
        if (empty($tabId) || empty($profileId) || $profileId === EmployeeContext::SUPER_ADMIN_PROFILE_ID) {
            return;
        }

        $tabAccess = Profile::getProfileAccesses($profileId);
        if ($tabAccess[$tabId]['view'] === '1') {
            return;
        }

        throw new EmployeeConstraintException('Selected homepage is not accessible for this profile', EmployeeConstraintException::INVALID_HOMEPAGE);
    }
}
