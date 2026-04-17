<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\DeleteManufacturerCommand;

/**
 * Defines contract for DeleteManufacturerHandler
 */
interface DeleteManufacturerHandlerInterface
{
    /**
     * @param DeleteManufacturerCommand $command
     */
    public function handle(DeleteManufacturerCommand $command);
}
