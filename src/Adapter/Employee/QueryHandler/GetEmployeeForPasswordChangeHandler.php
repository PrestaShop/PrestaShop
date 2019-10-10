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

namespace PrestaShop\PrestaShop\Adapter\Employee\QueryHandler;

use Employee;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForPasswordChange;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler\GetEmployeeForPasswordChangeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeForPasswordChange;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Handle getting employee for password change query.
 */
final class GetEmployeeForPasswordChangeHandler implements GetEmployeeForPasswordChangeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetEmployeeForPasswordChange $query)
    {
        $employee = new Employee($query->getEmployeeId()->getValue());

        return new EmployeeForPasswordChange(
            new EmployeeId($employee->id),
            new Email($employee->email)
        );
    }
}
