<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Profile\Permission\CommandHandler;

use Access;
use Exception;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateTabPermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\CommandHandler\UpdateTabPermissionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Exception\PermissionUpdateException;
use Profile;

/**
 * Updates permissions for tab (Menu) using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
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
        } catch (Exception) {
            // If role slug is not found it raises an exception
            $result = 'error';
        }

        if ('error' === $result) {
            throw new PermissionUpdateException('Failed to update permissions');
        }
    }
}
