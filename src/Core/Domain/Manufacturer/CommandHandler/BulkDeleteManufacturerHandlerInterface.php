<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkDeleteManufacturerCommand;

/**
 * Defines contract for BulkDeleteManufacturerHandler
 */
interface BulkDeleteManufacturerHandlerInterface
{
    /**
     * @param BulkDeleteManufacturerCommand $command
     */
    public function handle(BulkDeleteManufacturerCommand $command);
}
