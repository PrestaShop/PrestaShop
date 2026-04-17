<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\UpdateVirtualProductFileCommand;

/**
 * Defines contract to handle @see UpdateVirtualProductFileCommand
 */
interface UpdateVirtualProductFileHandlerInterface
{
    /**
     * @param UpdateVirtualProductFileCommand $command
     */
    public function handle(UpdateVirtualProductFileCommand $command): void;
}
