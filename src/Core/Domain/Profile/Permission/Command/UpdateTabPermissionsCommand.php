<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
