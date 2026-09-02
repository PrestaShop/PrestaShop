<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditManufacturerAddressCommand;

/**
 * Interface for services that handles command which edits manufacturer address
 */
interface EditManufacturerAddressHandlerInterface
{
    /**
     * @param EditManufacturerAddressCommand $command
     */
    public function handle(EditManufacturerAddressCommand $command);
}
