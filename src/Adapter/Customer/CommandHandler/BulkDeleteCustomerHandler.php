<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDeleteCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\BulkDeleteCustomerHandlerInterface;

/**
 * Handles command that deletes customers in bulk action.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDeleteCustomerHandler extends AbstractCustomerHandler implements BulkDeleteCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteCustomerCommand $command)
    {
        foreach ($command->getCustomerIds() as $customerId) {
            $customer = new Customer($customerId->getValue());

            $this->assertCustomerWasFound($customerId, $customer);

            if ($command->getDeleteMethod()->isAllowedToRegisterAfterDelete()) {
                $customer->delete();

                continue;
            }

            $customer->deleted = true;
            $customer->update();
        }
    }
}
