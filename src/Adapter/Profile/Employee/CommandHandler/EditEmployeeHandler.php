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
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\EditEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Employee\Access\ProfileAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use Shop;

/**
 * Handles command which edits employee using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
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
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param Hashing $hashing
     * @param ProfileAccessCheckerInterface $profileAccessChecker
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        Hashing $hashing,
        ProfileAccessCheckerInterface $profileAccessChecker,
        ContextEmployeeProviderInterface $contextEmployeeProvider,
        LegacyContext $legacyContext
    ) {
        $this->hashing = $hashing;
        $this->profileAccessChecker = $profileAccessChecker;
        $this->contextEmployeeProvider = $contextEmployeeProvider;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditEmployeeCommand $command)
    {
        $canAccessProfile = $this->profileAccessChecker->canEmployeeAccessProfile(
            $this->contextEmployeeProvider->getId(),
            (int) $command->getProfileId()
        );

        if (!$canAccessProfile) {
            throw new InvalidProfileException('You cannot access the provided profile.');
        }

        $employee = new Employee($command->getEmployeeId()->getValue());

        $this->assertEmailIsNotAlreadyUsed($employee, $command->getEmail()->getValue());

        $this->updateEmployeeWithCommandData($employee, $command);

        if (null !== $command->getPlainPassword() && $employee->id == $this->contextEmployeeProvider->getId()) {
            $this->updatePasswordInCookie($employee);
        }
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
        $employee->default_tab = $command->getDefaultPageId();
        $employee->id_lang = $command->getLanguageId();
        $employee->id_last_order = $employee->getLastElementsForNotify('order');
        $employee->id_last_customer_message = $employee->getLastElementsForNotify('customer_message');
        $employee->id_last_customer = $employee->getLastElementsForNotify('customer');
        $employee->has_enabled_gravatar = $command->hasEnabledGravatar();

        // Allow changing profile and active status only when editing not own account.
        if ($employee->id != $this->contextEmployeeProvider->getId()) {
            $employee->id_profile = $command->getProfileId();
            $employee->active = $command->isActive();
        }

        $shopAssociation = $command->getShopAssociation();

        if (!$employee->isSuperAdmin() && empty($shopAssociation)) {
            throw new MissingShopAssociationException('Employee must be associated to at least one shop.');
        }

        if (null !== $command->getPlainPassword()) {
            $employee->passwd = $this->hashing->hash($command->getPlainPassword()->getValue());
        }

        if (false === $employee->update()) {
            throw new EmployeeException(sprintf('Cannot update employee with id "%s"', $employee->id));
        }

        if ($employee->isSuperAdmin()) {
            $shopAssociation = array_values(Shop::getShops(false, null, true));
        }

        // Allow changing shop association only when editing not own account.
        if (null !== $shopAssociation && $employee->id != $this->contextEmployeeProvider->getId()) {
            $this->associateWithShops($employee, $shopAssociation);
        }
    }

    /**
     * @param Employee $employee
     * @param string $email
     *
     * @throws EmailAlreadyUsedException
     */
    private function assertEmailIsNotAlreadyUsed(Employee $employee, $email)
    {
        // Don't count own email as usage.
        if ($employee->email === $email) {
            return;
        }

        if (Employee::employeeExists($email)) {
            throw new EmailAlreadyUsedException($email, 'An account already exists for this email address');
        }
    }

    /**
     * Update employee password in cookie.
     *
     * @param Employee $employee
     */
    private function updatePasswordInCookie(Employee $employee)
    {
        $this->legacyContext->getContext()->cookie->passwd = $employee->passwd;
        $this->legacyContext->getContext()->employee->passwd = $employee->passwd;
        $this->legacyContext->getContext()->cookie->write();
    }
}
