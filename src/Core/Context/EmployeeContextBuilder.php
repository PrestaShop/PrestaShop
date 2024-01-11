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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Employee as LegacyEmployee;
use PrestaShop\PrestaShop\Adapter\Employee\EmployeeRepository;

/**
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class EmployeeContextBuilder
{
    private ?int $employeeId = null;
    private ?LegacyEmployee $legacyEmployee = null;

    public function __construct(
        private readonly EmployeeRepository $employeeRepository
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

        return new EmployeeContext($employee);
    }

    public function setEmployeeId(?int $employeeId): self
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    private function getLegacyEmployee(): ?LegacyEmployee
    {
        if (!$this->legacyEmployee && !empty($this->employeeId)) {
            $this->legacyEmployee = $this->employeeRepository->get($this->employeeId);
        }

        return $this->legacyEmployee;
    }
}
