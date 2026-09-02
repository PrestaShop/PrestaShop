<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetPrivateNoteAboutCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\SetPrivateNoteAboutCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;

/**
 * Handles command that saves private note for customer
 *
 * @internal
 */
#[AsCommandHandler]
final class SetPrivateNoteAboutCustomerHandler implements SetPrivateNoteAboutCustomerHandlerInterface
{
    /**
     * @param SetPrivateNoteAboutCustomerCommand $command
     */
    public function handle(SetPrivateNoteAboutCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($customerId->getValue());

        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId->getValue()));
        }

        $customer->note = $command->getPrivateNote();
        $customer->update();
    }
}
