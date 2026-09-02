<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddManufacturerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Interface for services that handles command which adds new manufacturer address
 */
interface AddManufacturerAddressHandlerInterface
{
    /**
     * @param AddManufacturerAddressCommand $command
     *
     * @return AddressId
     */
    public function handle(AddManufacturerAddressCommand $command);
}
