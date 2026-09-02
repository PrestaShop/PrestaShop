<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\DeleteVirtualProductFileCommand;

/**
 * Defines contract to handle @see DeleteVirtualProductFileCommand
 */
interface DeleteVirtualProductFileHandlerInterface
{
    /**
     * @param DeleteVirtualProductFileCommand $command
     */
    public function handle(DeleteVirtualProductFileCommand $command): void;
}
