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

use Employee;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AuthenticateEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\AuthenticateEmployeeHandlerInterface;
use Tools;

/**
 * Handler for employee authentication command, uses legacy logic.
 *
 * @internal
 */
final class AuthenticateEmployeeHandler extends AbstractEmployeeHandler implements AuthenticateEmployeeHandlerInterface
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
    public function handle(AuthenticateEmployeeCommand $command)
    {
        $employee = new Employee($command->getEmployeeId()->getValue());

        $this->assertEmployeeWasFoundById($command->getEmployeeId(), $employee);

        // Assign logged in employee to the context
        $this->context->getContext()->employee = $employee;
        $this->context->getContext()->employee->remote_addr = (int) ip2long(Tools::getRemoteAddr());

        // Save employee data to cookie
        $cookie = $this->context->getContext()->cookie;
        $cookie->id_employee = $employee->id;
        $cookie->email = $employee->email;
        $cookie->profile = $employee->id_profile;
        $cookie->passwd = $employee->passwd;
        $cookie->remote_addr = $employee->remote_addr;
        $cookie->write();
    }
}
