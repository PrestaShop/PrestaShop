<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use PrestaShop\PrestaShop\Core\Action\ActionsBarButtonsCollection;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/employee_dropdown.html.twig')]
class EmployeeDropdown
{
    /**
     * Protected on purpose: a public property is exposed to the component template under its own
     * name, and a template reading it directly gets this null instead of going through the getter
     * that dispatches the hook. That is exactly how displayBackOfficeEmployeeMenu stopped firing.
     */
    protected ?ActionsBarButtonsCollection $displayBackOfficeEmployeeMenu = null;

    public function __construct(
        protected readonly HookDispatcherInterface $hookDispatcher,
        protected readonly EmployeeContext $employeeContext
    ) {
    }

    public function getEmployee(): ?Employee
    {
        return $this->employeeContext->getEmployee();
    }

    public function getDisplayBackOfficeEmployeeMenu(): ActionsBarButtonsCollection
    {
        if ($this->displayBackOfficeEmployeeMenu === null) {
            $menuLinksCollections = new ActionsBarButtonsCollection();

            $this->hookDispatcher->dispatchWithParameters(
                'displayBackOfficeEmployeeMenu',
                [
                    'links' => $menuLinksCollections,
                ]
            );

            $this->displayBackOfficeEmployeeMenu = $menuLinksCollections;
        }

        return $this->displayBackOfficeEmployeeMenu;
    }
}
