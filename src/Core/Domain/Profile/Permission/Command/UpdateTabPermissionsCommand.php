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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerAllPermissions;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerPermission;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\PermissionInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use PrestaShop\PrestaShop\Core\Domain\Tab\ValueObject\AllTab;
use PrestaShop\PrestaShop\Core\Domain\Tab\ValueObject\TabId;
use PrestaShop\PrestaShop\Core\Domain\Tab\ValueObject\TabIdInterface;

/**
 * Updates tab permissions for employee's profile
 */
class UpdateTabPermissionsCommand
{
    /**
     * @var ProfileId
     */
    private $profileId;

    /**
     * @var TabId
     */
    private $tabId;

    /**
     * @var PermissionInterface
     */
    private $permission;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @param int $profileId
     * @param int $tabId
     * @param string $permission
     * @param bool $isActive
     */
    public function __construct(int $profileId, int $tabId, string $permission, bool $isActive)
    {
        $this->profileId = new ProfileId($profileId);
        $this->tabId = $tabId === AllTab::ALL_TAB_ID ? new AllTab() : new TabId($tabId);
        $this->permission = $permission === ControllerAllPermissions::ALL ? new ControllerAllPermissions() : new ControllerPermission($permission);
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
     * @return TabId
     */
    public function getTabId(): TabIdInterface
    {
        return $this->tabId;
    }

    /**
     * @return PermissionInterface
     */
    public function getPermission(): PermissionInterface
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
