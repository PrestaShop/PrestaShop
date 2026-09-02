<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;

/**
 * Defines contract to handle @see AddVirtualProductFileCommand
 */
interface AddVirtualProductFileHandlerInterface
{
    /**
     * @param AddVirtualProductFileCommand $command
     */
    public function handle(AddVirtualProductFileCommand $command): VirtualProductFileId;
}
