<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\CommandHandler;

use CustomerAddress;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Command\SetRequiredFieldsForAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Address\CommandHandler\SetRequiredFieldsForAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotSetRequiredFieldsForAddressException;
use PrestaShopDatabaseException;

/**
 * Handles command which sets required fields for address.
 *
 * @internal
 */
#[AsCommandHandler]
final class SetRequiredFieldsForAddressHandler implements SetRequiredFieldsForAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotSetRequiredFieldsForAddressException
     */
    public function handle(SetRequiredFieldsForAddressCommand $command)
    {
        $address = new CustomerAddress();

        try {
            if ($address->addFieldsRequiredDatabase($command->getRequiredFields())) {
                return;
            }
        } catch (PrestaShopDatabaseException) {
        }

        throw new CannotSetRequiredFieldsForAddressException(sprintf('Cannot set "%s" required fields for customer', implode(',', $command->getRequiredFields())));
    }
}
