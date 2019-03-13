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
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\CommandHandler\EditEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Employee\Access\ProfileAccessCheckerInterface;
use Shop;

/**
 * Handles command which edits employee using legacy object model
 *
 * @internal
 */
final class EditEmployeeHandler extends AbstractEmployeeHandler implements EditEmployeeHandlerInterface
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
    public function handle(EditEmployeeCommand $command)
    {
        if (!$this->profileAccessChecker->canAccessProfile((int) $command->getProfileId())) {
            throw new InvalidProfileException('The provided profile is invalid');
        }

        $employee = new Employee($command->getEmployeeId()->getValue());

        $this->assertEmailIsUsed($employee, $command->getEmail()->getValue());

        $this->updateEmployeeWithCommandData($employee, $command);

        return new EmployeeId((int) $employee->id);
    }

    /**
     * Update employee object model with data from employee edit command.
     *
     * @param Employee $employee
     * @param EditEmployeeCommand $command
     *
     * @throws EmployeeException
     */
    private function updateEmployeeWithCommandData(Employee $employee, EditEmployeeCommand $command)
    {
        $employee->firstname = $command->getFirstName()->getValue();
        $employee->lastname = $command->getLastName()->getValue();
        $employee->email = $command->getEmail()->getValue();
        $employee->optin = $command->isSubscribedToNewsletter();
        $employee->default_tab = $command->getDefaultPageId();
        $employee->id_lang = $command->getLanguageId();
        $employee->active = $command->isActive();
        $employee->id_profile = $command->getProfileId();

        $shopAssociation = $command->getShopAssociation();

        if (!$employee->isSuperAdmin() && empty($shopAssociation)) {
            throw new MissingShopAssociationException(
                'Employee must be associated to at least one shop.'
            );
        }

        if (null !== $command->getPlainPassword()) {
            $employee->passwd = $this->hashing->hash($command->getPlainPassword()->getValue());
        }

        if (false === $employee->update()) {
            throw new EmployeeException(
                sprintf('Cannot update employee with id "%s"', $employee->id)
            );
        }

        if ($employee->isSuperAdmin()) {
            $shopAssociation = array_values(Shop::getShops(false, null, true));
        }

        if (null !== $shopAssociation) {
            $this->associateWithShops($employee, $shopAssociation);
        }
    }

    /**
     * @param Employee $employee
     * @param string $email
     *
     * @throws EmailAlreadyUsedException
     */
    private function assertEmailIsUsed(Employee $employee, $email)
    {
        // Don't count own email as usage.
        if ($employee->email === $email) {
            return;
        }

        if (Employee::employeeExists($email)) {
            throw new EmailAlreadyUsedException(
                $email,
                'An account already exists for this email address'
            );
        }
    }
}
