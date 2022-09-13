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

namespace PrestaShop\PrestaShop\Adapter\Profile\Permission\CommandHandler;

use Access;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateTabPermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\CommandHandler\UpdateTabPermissionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Exception\PermissionUpdateException;
use Profile;

/**
 * Updates permissions for tab (Menu) using legacy object model
 *
 * @internal
 */
final class UpdateTabPermissionsHandler implements UpdateTabPermissionsHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateTabPermissionsCommand $command): void
    {
        $access = new Access();

        try {
            $result = $access->updateLgcAccess(
                $command->getProfileId()->getValue(),
                $command->getTabId()->getValue(),
                $command->getPermission()->getValue(),
                $command->isActive(),
                false // Do not apply to all children
            );

            // Reset cache so that following queries are up-to-date
            Profile::resetStaticCache();
        } catch (Exception $e) {
            // If role slug is not found it raises an exception
            $result = 'error';
        }

        if ('error' === $result) {
            throw new PermissionUpdateException('Failed to update permissions');
        }
    }
}
