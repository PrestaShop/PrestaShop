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

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\QueryHandler;

use Employee;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler\GetEmployeeForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EditableEmployee;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Handles command that gets employee for editing.
 */
final class GetEmployeeForEditingHandler implements GetEmployeeForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetEmployeeForEditing $query)
    {
        $employeeId = $query->getEmployeeId();
        $employee = new Employee($employeeId->getValue());

        if ($employee->id !== $employeeId->getValue()) {
            throw new EmployeeNotFoundException(
                $employeeId,
                sprintf('Employee with id "%s" was not found', $employeeId->getValue())
            );
        }

        return new EditableEmployee(
            $employeeId,
            new FirstName($employee->firstname),
            new LastName($employee->lastname),
            new Email($employee->email),
            $employee->getImage(),
            (bool) $employee->optin,
            (int) $employee->default_tab,
            (int) $employee->id_lang,
            (bool) $employee->active,
            (int) $employee->id_profile,
            $employee->getAssociatedShops()
        );
    }
}
