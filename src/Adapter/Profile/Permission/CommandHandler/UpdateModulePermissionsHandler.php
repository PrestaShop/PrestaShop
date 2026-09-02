<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Profile\Permission\CommandHandler;

use Access;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateModulePermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\CommandHandler\UpdateModulePermissionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Exception\PermissionUpdateException;

/**
 * Updates permissions for modules using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
final class UpdateModulePermissionsHandler implements UpdateModulePermissionsHandlerInterface
{
    /**
     * @param UpdateModulePermissionsCommand $command
     *
     * @throws PermissionUpdateException
     */
    public function handle(UpdateModulePermissionsCommand $command): void
    {
        $result = (new Access())->updateLgcModuleAccess(
            $command->getProfileId()->getValue(),
            $command->getModuleId()->getValue(),
            $command->getPermission()->getValue(),
            $command->isActive()
        );

        if ('error' === $result) {
            throw new PermissionUpdateException('Failed to update module permissions');
        }
    }
}
