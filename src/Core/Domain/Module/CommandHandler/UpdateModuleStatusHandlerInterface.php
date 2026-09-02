<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Module\Command\UpdateModuleStatusCommand;

interface UpdateModuleStatusHandlerInterface
{
    public function handle(UpdateModuleStatusCommand $command): void;
}
