<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Context;
use Employee;
use Language;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\AdminEmployeeException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeCannotChangeItselfException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Tab\Exception\TabNotFoundException;
use Profile;
use Shop;
use Tab;

/**
 * Class AbstractEmployeeStatusHandler.
 */
abstract class AbstractEmployeeHandler extends AbstractObjectModelHandler
{
    /**
     * @param EmployeeId $employeeId
     * @param Employee $employee
     *
     * @throws EmployeeNotFoundException
     */
    protected function assertEmployeeWasFoundById(EmployeeId $employeeId, Employee $employee)
    {
        if (!$employee->id) {
            throw new EmployeeNotFoundException($employeeId, sprintf('Employee with id "%s" cannot be found.', $employeeId->getValue()));
        }
    }

    /**
     * If employee is admin and no other admins exists, then terminate command execution.
     *
     * @param Employee $employee
     */
    protected function assertEmployeeIsNotTheOnlyAdminInShop(Employee $employee)
    {
        if ($employee->isLastAdmin()) {
            throw new AdminEmployeeException(sprintf('Employee with id %s is the only admin in shop and status cannot be changed.', $employee->id), AdminEmployeeException::CANNOT_CHANGE_LAST_ADMIN);
        }
    }

    /**
     * If logged in employee is trying to toggle itself, then terminate execution.
     *
     * @param Employee $employee
     */
    protected function assertLoggedInEmployeeIsNotTheSameAsBeingUpdatedEmployee(Employee $employee)
    {
        // No employee in the context when the command comes from the Admin API or the CLI
        if (Context::getContext()->employee?->id === $employee->id) {
            throw new EmployeeCannotChangeItselfException('Employee cannot change status of itself.', EmployeeCannotChangeItselfException::CANNOT_CHANGE_STATUS);
        }
    }

    /**
     * Only a super admin manages a super admin account, like the back-office form access checker enforces: an
     * Admin API client or a CLI command, which have no employee in the context, are not super admins.
     *
     * @throws AdminEmployeeException
     */
    protected function assertContextEmployeeCanManage(Employee $employee): void
    {
        if ($employee->isSuperAdmin() && !Context::getContext()->employee?->isSuperAdmin()) {
            throw new AdminEmployeeException(sprintf('Employee with id %s is a super admin, only a super admin can change it.', $employee->id), AdminEmployeeException::CANNOT_MANAGE_SUPER_ADMIN);
        }
    }

    /**
     * @throws EmployeeConstraintException
     * @throws TabNotFoundException
     */
    protected function assertHomepageIsAccessible(int $tabId, int $profileId): void
    {
        if (empty($tabId) || empty($profileId)) {
            return;
        }

        // An unknown tab is refused for every profile, the super admin one included
        if (!Tab::existsInDatabase($tabId)) {
            throw new TabNotFoundException(sprintf('Homepage with id %d does not exist', $tabId));
        }

        if ($profileId === EmployeeContext::SUPER_ADMIN_PROFILE_ID) {
            return;
        }

        $tabAccess = Profile::getProfileAccesses($profileId);
        if (($tabAccess[$tabId]['view'] ?? null) === '1') {
            return;
        }

        throw new EmployeeConstraintException('Selected homepage is not accessible for this profile', EmployeeConstraintException::INVALID_HOMEPAGE);
    }

    /**
     * @throws ProfileNotFoundException
     */
    protected function assertProfileExists(int $profileId): void
    {
        if (!Profile::existsInDatabase($profileId)) {
            throw new ProfileNotFoundException(sprintf('Profile with id %d does not exist', $profileId));
        }
    }

    /**
     * @throws LanguageNotFoundException
     */
    protected function assertLanguageExists(int $languageId): void
    {
        if (!Language::existsInDatabase($languageId)) {
            throw new LanguageNotFoundException(new LanguageId($languageId), sprintf('Language with id %d does not exist', $languageId));
        }
    }

    /**
     * @param int[] $shopIds
     *
     * @throws ShopNotFoundException
     */
    protected function assertShopsExist(array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            if (!Shop::existsInDatabase((int) $shopId)) {
                throw new ShopNotFoundException(sprintf('Shop with id %d does not exist', (int) $shopId));
            }
        }
    }
}
