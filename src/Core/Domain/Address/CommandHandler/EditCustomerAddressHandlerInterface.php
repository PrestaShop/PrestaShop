<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditCustomerAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Interface for services that handles command which edits customer address
 */
interface EditCustomerAddressHandlerInterface
{
    /**
     * @param EditCustomerAddressCommand $command
     *
     * @return AddressId The (potentially) newly created address id
     */
    public function handle(EditCustomerAddressCommand $command): AddressId;
}
