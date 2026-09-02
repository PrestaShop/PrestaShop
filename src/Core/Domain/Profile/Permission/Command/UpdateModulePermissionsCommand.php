<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
