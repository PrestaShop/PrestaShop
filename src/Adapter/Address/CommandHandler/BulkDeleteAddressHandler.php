<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Address\AbstractAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\BulkDeleteAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\BulkDeleteAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\BulkDeleteAddressException;

/**
 * Handles command which deletes addresses in bulk action
 */
#[AsCommandHandler]
final class BulkDeleteAddressHandler extends AbstractAddressHandler implements BulkDeleteAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BulkDeleteAddressException
     */
    public function handle(BulkDeleteAddressCommand $command)
    {
        $errors = [];

        foreach ($command->getAdressIds() as $addressId) {
            try {
                $address = $this->getAddress($addressId);

                if (!$this->deleteAddress($address)) {
                    $errors[] = $address->id;
                }
            } catch (AddressException) {
                $errors[] = $addressId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteAddressException($errors, 'Failed to delete all of selected addresses');
        }
    }
}
