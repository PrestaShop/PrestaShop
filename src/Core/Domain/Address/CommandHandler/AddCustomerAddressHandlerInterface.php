<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\AddCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Interface for service that handles command which adds new customer address
 */
interface AddCustomerAddressHandlerInterface
{
    /**
     * @param AddCustomerAddressCommand $command
     *
     * @return AddressId
     */
    public function handle(AddCustomerAddressCommand $command): AddressId;
}
