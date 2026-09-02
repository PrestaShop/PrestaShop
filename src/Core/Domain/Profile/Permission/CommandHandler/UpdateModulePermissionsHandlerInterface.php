<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateModulePermissionsCommand;

interface UpdateModulePermissionsHandlerInterface
{
    /**
     * @param UpdateModulePermissionsCommand $command
     */
    public function handle(UpdateModulePermissionsCommand $command): void;
}
