<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\BulkToggleManufacturerStatusCommand;

/**
 * Defines contract for BulkToggleManufacturerStatusHandler
 */
interface BulkToggleManufacturerStatusHandlerInterface
{
    /**
     * @param BulkToggleManufacturerStatusCommand $command
     */
    public function handle(BulkToggleManufacturerStatusCommand $command);
}
