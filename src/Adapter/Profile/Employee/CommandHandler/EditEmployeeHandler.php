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
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\EmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\Exception\MissingShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Employee\ValueObject\EmployeeId;
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
     * @param Hashing $hashing
     */
    public function __construct(Hashing $hashing)
    {
        $this->hashing = $hashing;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditEmployeeCommand $command)
    {
        $employee = new Employee($command->getEmployeeId()->getValue());

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
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
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
}
