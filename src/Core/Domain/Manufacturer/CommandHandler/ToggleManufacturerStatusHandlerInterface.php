<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\ToggleManufacturerStatusCommand;

/**
 * Defines contract for ToggleManufacturerStatusHandler
 */
interface ToggleManufacturerStatusHandlerInterface
{
    /**
     * @param ToggleManufacturerStatusCommand $command
     */
    public function handle(ToggleManufacturerStatusCommand $command);
}
