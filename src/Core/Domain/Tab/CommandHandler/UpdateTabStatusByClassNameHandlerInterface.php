<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tab\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;

interface UpdateTabStatusByClassNameHandlerInterface
{
    public function handle(UpdateTabStatusByClassNameCommand $command): void;
}
