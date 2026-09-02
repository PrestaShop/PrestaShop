<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Employee as LegacyEmployee;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;

/**
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class EmployeeContextBuilder implements LegacyContextBuilderInterface
{
    use LegacyObjectCheckerTrait;

    private ?int $employeeId = null;
    private ?LegacyEmployee $legacyEmployee = null;

    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly ContextStateManager $contextStateManager,
        private readonly ShopRepository $shopRepository,
    ) {
    }

    public function build(): EmployeeContext
    {
        $employee = null;
        $legacyEmployee = $this->getLegacyEmployee();
        if ($legacyEmployee) {
            $employee = new Employee(
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
            );
        }

        return new EmployeeContext($employee, $this->shopRepository->getAllShopIds());
    }

    public function buildLegacyContext(): void
    {
        $legacyEmployee = $this->getLegacyEmployee();
        if (!empty($legacyEmployee)) {
            // Only update the legacy context when the employee is not the expected one, if not leave the context unchanged
            if ($this->legacyObjectNeedsUpdate($this->contextStateManager->getContext()->employee, (int) $legacyEmployee->id)) {
                $this->contextStateManager->setEmployee($legacyEmployee);
            }
        }
    }

    public function setEmployeeId(?int $employeeId): self
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    private function getLegacyEmployee(): ?LegacyEmployee
    {
        if ($this->legacyObjectNeedsUpdate($this->legacyEmployee, $this->employeeId)) {
            $this->legacyEmployee = $this->employeeRepository->get($this->employeeId);
        }

        return $this->legacyEmployee;
    }
}
