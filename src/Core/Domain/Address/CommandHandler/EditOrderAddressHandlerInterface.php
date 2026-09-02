<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Command\EditOrderAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Interface for services that handles command which edits order address
 */
interface EditOrderAddressHandlerInterface
{
    /**
     * @param EditOrderAddressCommand $command
     *
     * @return AddressId The newly created address id
     */
    public function handle(EditOrderAddressCommand $command): AddressId;
}
