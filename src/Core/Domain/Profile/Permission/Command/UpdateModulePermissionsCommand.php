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

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command;

use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleId;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ModulePermission;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Updates module permissions for employee's profile
 */
class UpdateModulePermissionsCommand
{
    /**
     * @var ProfileId
     */
    private $profileId;

    /**
     * @var ModuleId
     */
    private $moduleId;

    /**
     * @var ModulePermission
     */
    private $permission;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @param int $profileId
     * @param int $moduleId
     * @param string $permission
     * @param bool $isActive
     */
    public function __construct(int $profileId, int $moduleId, string $permission, bool $isActive)
    {
        $this->profileId = new ProfileId($profileId);
        $this->moduleId = new ModuleId($moduleId);
        $this->permission = new ModulePermission($permission);
        $this->isActive = $isActive;
    }

    /**
     * @return ProfileId
     */
    public function getProfileId(): ProfileId
    {
        return $this->profileId;
    }

    /**
     * @return ModuleId
     */
    public function getModuleId(): ModuleId
    {
        return $this->moduleId;
    }

    /**
     * @return ModulePermission
     */
    public function getPermission(): ModulePermission
    {
        return $this->permission;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
}
