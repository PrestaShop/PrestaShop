<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\Permission;
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
     * @var int
     */
    private $moduleId;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param int $profileId
     * @param int $moduleId
     * @param string $permission
     * @param bool $expectedStatus
     */
    public function __construct($profileId, $moduleId, $permission, $expectedStatus)
    {
        $this->profileId = new ProfileId($profileId);
        $this->moduleId = $moduleId;
        $this->permission = new Permission($permission);
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return ProfileId
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return int
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * @return Permission
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus()
    {
        return $this->expectedStatus;
    }
}
