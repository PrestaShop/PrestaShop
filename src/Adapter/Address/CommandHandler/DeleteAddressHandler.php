<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\DeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\DeleteAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\DeleteAddressException;

/**
 * Handles command which deletes address
 */
#[AsCommandHandler]
final class DeleteAddressHandler extends AbstractAddressHandler implements DeleteAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteAddressCommand $command)
    {
        $addressId = $command->getAddressId();
        $address = $this->getAddress($addressId);

        if (!$this->deleteAddress($address)) {
            throw new DeleteAddressException(sprintf('Cannot delete Address object with id "%s".', $addressId->getValue()), DeleteAddressException::FAILED_DELETE);
        }
    }
}
