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

use Employee;
use PhpEncryption;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SetEmployeeTwoFactorSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\SetEmployeeTwoFactorSecretHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;

/**
 * Handles the command that stores the two-factor authentication secret
 *
 * @internal
 */
#[AsCommandHandler]
final class SetEmployeeTwoFactorSecretHandler implements SetEmployeeTwoFactorSecretHandlerInterface
{
    public function __construct(
        private string $newCookieKey
    ) {
    }
    /**
     * {@inheritdoc}
     */
    public function handle(SetEmployeeTwoFactorSecretCommand $command)
    {
        $employee = new Employee($command->getEmployeeId()->getValue());

        $cipherTool = new PhpEncryption($this->newCookieKey);
        $employee->two_factor_secret = $cipherTool->encrypt($command->getSecret());

        if (false === $employee->update()) {
            throw new EmployeeException(sprintf('Cannot update employee with id "%s"', $employee->id));
        }
    }
}
