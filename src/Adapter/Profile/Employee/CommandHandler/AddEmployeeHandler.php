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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Crypto\Hashing;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\AddEmployeeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmailAlreadyUsedException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidProfileException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Employee\Access\ProfileAccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;

/**
 * Handles command which adds new employee using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
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
     * @var ContextEmployeeProviderInterface
     */
    private $contextEmployeeProvider;

    /**
     * @param Hashing $hashing
     * @param ProfileAccessCheckerInterface $profileAccessChecker
     * @param ContextEmployeeProviderInterface $contextEmployeeProvider
     */
    public function __construct(
        Hashing $hashing,
        ProfileAccessCheckerInterface $profileAccessChecker,
        ContextEmployeeProviderInterface $contextEmployeeProvider
    ) {
        $this->hashing = $hashing;
        $this->profileAccessChecker = $profileAccessChecker;
        $this->contextEmployeeProvider = $contextEmployeeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddEmployeeCommand $command)
    {
        $canAccessProfile = $this->profileAccessChecker->canEmployeeAccessProfile(
            $this->contextEmployeeProvider->getId(),
            (int) $command->getProfileId()
        );

        if (!$canAccessProfile) {
            throw new InvalidProfileException('You cannot access the provided profile.');
        }

        $this->assertEmailIsNotAlreadyUsed($command->getEmail()->getValue());

        $employee = $this->createLegacyEmployeeObjectFromCommand($command);

        $this->associateWithShops($employee, $command->getShopAssociation());

        return new EmployeeId((int) $employee->id);
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
        $employee->active = $command->isActive();
        $employee->passwd = $this->hashing->hash($command->getPlainPassword()->getValue());
        $employee->id_last_order = $employee->getLastElementsForNotify('order');
        $employee->id_last_customer_message = $employee->getLastElementsForNotify('customer_message');
        $employee->id_last_customer = $employee->getLastElementsForNotify('customer');
        $employee->has_enabled_gravatar = $command->hasEnabledGravatar();

        if (false === $employee->add()) {
            throw new EmployeeException(sprintf('Failed to add new employee with email "%s"', $command->getEmail()->getValue()));
        }

        return $employee;
    }

    /**
     * @param string $email
     *
     * @throws EmailAlreadyUsedException
     */
    private function assertEmailIsNotAlreadyUsed($email)
    {
        if (Employee::employeeExists($email)) {
            throw new EmailAlreadyUsedException($email, 'An account already exists for this email address');
        }
    }
}
