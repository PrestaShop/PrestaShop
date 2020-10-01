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

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\Permission;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

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
     * @var int
     */
    private $tabId;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @var bool
     */
    private $isAddedFromParent;

    /**
     * @param int $profileId
     * @param int $tabId
     * @param string $permission
     * @param bool $expectedStatus
     * @param bool $isAddedFromParent
     */
    public function __construct(int $profileId, int $tabId, string $permission, bool $expectedStatus, bool $isAddedFromParent)
    {
        $this->profileId = new ProfileId($profileId);
        $this->tabId = $tabId;
        $this->permission = new Permission($permission);
        $this->expectedStatus = $expectedStatus;
        $this->isAddedFromParent = $isAddedFromParent;
    }

    /**
     * @return ProfileId
     */
    public function getProfileId(): ProfileId
    {
        return $this->profileId;
    }

    /**
     * @return int
     */
    public function getTabId(): int
    {
        return $this->tabId;
    }

    /**
     * @return Permission
     */
    public function getPermission(): Permission
    {
        return $this->permission;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }

    /**
     * @return bool
     */
    public function isAddedFromParent(): bool
    {
        return $this->isAddedFromParent;
    }
}
