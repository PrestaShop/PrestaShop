<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

/**
 * This context service gives access to all contextual data related to employee.
 *
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class EmployeeContext
{
    public const SUPER_ADMIN_PROFILE_ID = 1;

    public function __construct(
        protected readonly ?Employee $employee,
        protected readonly array $allShopsIds,
    ) {
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function hasAuthorizationOnShopGroup(int $shopGroupId): bool
    {
        if (!$this->getEmployee()) {
            return false;
        }

        return $this->isSuperAdmin() || in_array($shopGroupId, $this->getEmployee()->getAssociatedShopGroupIds());
    }

    public function hasAuthorizationOnShop(int $shopId): bool
    {
        if (!$this->getEmployee()) {
            return false;
        }

        return $this->isSuperAdmin() || in_array($shopId, $this->getEmployee()->getAssociatedShopIds());
    }

    public function hasAuthorizationForAllShops(): bool
    {
        if (!$this->getEmployee()) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($this->allShopsIds as $shopId) {
            if (!$this->hasAuthorizationOnShop($shopId)) {
                return false;
            }
        }

        return true;
    }

    public function getDefaultShopId(): int
    {
        if (!$this->getEmployee()) {
            return 0;
        }

        return $this->getEmployee()->getDefaultShopId();
    }

    public function isSuperAdmin(): bool
    {
        return $this->getEmployee() && $this->getEmployee()->getProfileId() === self::SUPER_ADMIN_PROFILE_ID;
    }
}
