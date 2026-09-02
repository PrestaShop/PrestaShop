<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkEnableCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\BulkEnableCustomerHandlerInterface;

/**
 * Handles command which enables given customers.
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkEnableCustomerHandler extends AbstractCustomerHandler implements BulkEnableCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkEnableCustomerCommand $command)
    {
        foreach ($command->getCustomerIds() as $customerId) {
            $customer = new Customer($customerId->getValue());

            $this->assertCustomerWasFound($customerId, $customer);

            $customer->active = true;
            $customer->update();
        }
    }
}
