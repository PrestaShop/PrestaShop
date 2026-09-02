<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Module\Command\UninstallModuleCommand;

interface UninstallModuleHandlerInterface
{
    public function handle(UninstallModuleCommand $command): void;
}
