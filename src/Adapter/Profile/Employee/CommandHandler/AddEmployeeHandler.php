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
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\CommandHandler\AddEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Employee\Access\ProfileAccessCheckerInterface;

final class AddEmployeeHandler extends AbstractEmployeeHandler implements AddEmployeeHandlerInterface
{
    /**
     * @var Hashing
     */
    private $hashing;

    /**
     * @var ProfileAccessCheckerInterface
     */
    private $profileAccessChecker;

    /**
     * @param Hashing $hashing
     * @param ProfileAccessCheckerInterface $profileAccessChecker
     */
    public function __construct(
        Hashing $hashing,
        ProfileAccessCheckerInterface $profileAccessChecker
    ) {
        $this->hashing = $hashing;
        $this->profileAccessChecker = $profileAccessChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddEmployeeCommand $command)
    {
        if (!$this->profileAccessChecker->canAccessProfile((int) $command->getProfileId())) {
            throw new InvalidProfileException('The provided profile is invalid');
        }

        $this->assertEmailIsUsed($command->getEmail()->getValue());

        $employee = $this->createLegacyEmployeeObjectFromCommand($command);

        $this->associateWithShops($employee, $command->getShopAssociation());

        return new EmployeeId($employee->id);
    }

    /**
     * Create legacy employee object.
     *
     * @param AddEmployeeCommand $command
     *
     * @return Employee
     */
    private function createLegacyEmployeeObjectFromCommand(AddEmployeeCommand $command)
    {
        $employee = new Employee();
        $employee->firstname = $command->getFirstName()->getValue();
        $employee->lastname = $command->getLastName()->getValue();
        $employee->email = $command->getEmail()->getValue();
        $employee->id_lang = $command->getLanguageId();
        $employee->id_profile = $command->getProfileId();
        $employee->default_tab = $command->getDefaultPageId();
        $employee->optin = $command->isSubscribedToNewsletter();
        $employee->active = $command->isActive();
        $employee->passwd = $this->hashing->hash($command->getPlainPassword()->getValue());

        if (false === $employee->add()) {
            throw new EmployeeException(
                sprintf('Failed to add new employee with email "%s"', $command->getEmail()->getValue())
            );
        }

        return $employee;
    }

    /**
     * @param string $email
     *
     * @throws EmailAlreadyUsedException
     */
    private function assertEmailIsUsed($email)
    {
        if (Employee::employeeExists($email)) {
            throw new EmailAlreadyUsedException(
                $email,
                'An account already exists for this email address'
            );
        }
    }
}
