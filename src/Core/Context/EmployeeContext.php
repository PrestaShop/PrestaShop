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

use PrestaShop\PrestaShop\Core\Model\EmployeeInterface;

/**
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class EmployeeContext
{
    public const SUPER_ADMIN_PROFILE_ID = 1;

    public function __construct(
        private readonly ?EmployeeInterface $employee
    ) {
    }

    public function getEmployee(): ?EmployeeInterface
    {
        return $this->employee;
    }

    public function hasAuthorizationOnShopGroup(int $shopGroupId): bool
    {
        if (!$this->employee) {
            return false;
        }

        return $this->isSuperAdmin() || in_array($shopGroupId, $this->employee->getAssociatedShopGroupIds());
    }

    public function hasAuthorizationOnShop(int $shopId): bool
    {
        if (!$this->employee) {
            return false;
        }

        return $this->isSuperAdmin() || in_array($shopId, $this->employee->getAssociatedShopIds());
    }

    public function getDefaultShopId(): int
    {
        if (!$this->employee) {
            return 0;
        }

        return $this->employee->getDefaultShopId();
    }

    public function isSuperAdmin(): bool
    {
        return $this->employee && $this->employee->getProfileId() === self::SUPER_ADMIN_PROFILE_ID;
    }
}
