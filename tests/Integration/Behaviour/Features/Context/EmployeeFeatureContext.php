<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Context;
use Employee as LegacyEmployee;
use PrestaShop\PrestaShop\Core\Context\Employee;
use Tests\Resources\Context\EmployeeContextDecorator;

/**
 * Class EmployeeFeatureContext
 */
class EmployeeFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given I am logged in as :employeeEmail employee
     */
    public function logsInBackOffice($employeeEmail)
    {
        $employee = new LegacyEmployee();
        $legacyEmployee = $employee->getByEmail($employeeEmail);

        Context::getContext()->employee = $legacyEmployee;

        /** @var EmployeeContextDecorator $employeeContext */
        $employeeContext = CommonFeatureContext::getContainer()->get(EmployeeContextDecorator::class);
        $employeeContext->setOverriddenEmployee(new Employee(
            id: (int) $legacyEmployee->id,
            profileId: (int) $legacyEmployee->id_profile,
            languageId: (int) $legacyEmployee->id_lang,
            firstName: $legacyEmployee->firstname,
            lastName: $legacyEmployee->lastname,
            email: $legacyEmployee->email,
            password: $legacyEmployee->passwd,
            imageUrl: $legacyEmployee->getImage(),
            defaultTabId: (int) $legacyEmployee->default_tab,
            defaultShopId: (int) $legacyEmployee->getDefaultShopID(),
            associatedShopIds: $legacyEmployee->getAssociatedShopIds(),
            associatedShopGroupIds: $legacyEmployee->getAssociatedShopGroupIds()
        ));
    }

    /**
     * @Given I am not logged in as an employee
     */
    public function logsOutBackOffice()
    {
        Context::getContext()->employee = null;
        /** @var EmployeeContextDecorator $employeeContext */
        $employeeContext = CommonFeatureContext::getContainer()->get(EmployeeContextDecorator::class);
        $employeeContext->setOverriddenEmployee(null);
    }

    /**
     * @AfterFeature
     */
    public static function resetEmployeeContext(): void
    {
        Context::getContext()->employee = null;
        /** @var EmployeeContextDecorator $employeeContext */
        $employeeContext = CommonFeatureContext::getContainer()->get(EmployeeContextDecorator::class);
        $employeeContext->resetOverriddenEmployee();
    }
}
