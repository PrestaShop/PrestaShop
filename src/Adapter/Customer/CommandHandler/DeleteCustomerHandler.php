<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\DeleteCustomerHandlerInterface;

/**
 * Handles delete customer command.
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteCustomerHandler extends AbstractCustomerHandler implements DeleteCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteCustomerCommand $command)
    {
        $customerId = $command->getCustomerId();
        $customer = new Customer($customerId->getValue());

        $this->assertCustomerWasFound($customerId, $customer);

        if ($command->getDeleteMethod()->isAllowedToRegisterAfterDelete()) {
            $customer->delete();

            return;
        }

        // soft delete customer
        // in order to forbid signing in again
        $customer->deleted = true;
        $customer->update();
    }
}
