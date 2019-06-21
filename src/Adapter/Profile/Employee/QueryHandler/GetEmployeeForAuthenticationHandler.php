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

use Access;
use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\AuthorizationOptions;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\AuthenticatingEmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForAuthentication;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler\GetEmployeeForAuthenticationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EmployeeForAuthentication;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShopException;

/**
 * Handles employee authentication command.
 */
final class GetEmployeeForAuthenticationHandler implements GetEmployeeForAuthenticationHandlerInterface
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @param LegacyContext $context
     */
    public function __construct(LegacyContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetEmployeeForAuthentication $query)
    {
        try {
            $employee = $this->getLegacyEmployee($query);
        } catch (PrestaShopException $e) {
            throw new AuthenticatingEmployeeNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        if (!$employee || !$employee->id) {
            throw new AuthenticatingEmployeeNotFoundException();
        }

        return new EmployeeForAuthentication(
            new EmployeeId((int) $employee->id),
            new Email($employee->email),
            $employee->passwd,
            $this->context->getAdminLink($employee->getDefaultTabClassName()),
            (int) $employee->id_profile,
            array_merge(
                [AuthorizationOptions::DEFAULT_EMPLOYEE_ROLE],
                Access::getRoles($employee->id_profile)
            )
        );
    }

    /**
     * Gets legacy employee from authentication query.
     *
     * @param GetEmployeeForAuthentication $query
     *
     * @return Employee
     */
    private function getLegacyEmployee(GetEmployeeForAuthentication $query)
    {
        if (null !== $query->getEmployeeId()) {
            return new Employee($query->getEmployeeId()->getValue());
        }

        return (new Employee())->getByEmail($query->getEmail());
    }
}
