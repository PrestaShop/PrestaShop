<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDisableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\BulkDisableCustomerHandlerInterface;

/**
 * Handles command that disables customers in bulk action.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDisableCustomerHandler extends AbstractCustomerHandler implements BulkDisableCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDisableCustomerCommand $command)
    {
        foreach ($command->getCustomerIds() as $customerId) {
            $customer = new Customer($customerId->getValue());

            $this->assertCustomerWasFound($customerId, $customer);

            $customer->active = false;
            $customer->update();
        }
    }
}
