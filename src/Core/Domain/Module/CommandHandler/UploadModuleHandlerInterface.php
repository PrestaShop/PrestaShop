<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Module\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Module\Command\UploadModuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Module\QueryResult\ModuleInfos;

interface UploadModuleHandlerInterface
{
    public function handle(UploadModuleCommand $command): ModuleInfos;
}
