<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Command\SetRequiredFieldsForCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler\SetRequiredFieldsForCustomerHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CannotSetRequiredFieldsForCustomerException;

/**
 * Handles command which sets required fields for customer.
 *
 * @internal
 */
#[AsCommandHandler]
final class SetRequiredFieldsForCustomerHandler implements SetRequiredFieldsForCustomerHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(SetRequiredFieldsForCustomerCommand $command)
    {
        $customer = new Customer();

        if (!$customer->addFieldsRequiredDatabase($command->getRequiredFields())) {
            throw new CannotSetRequiredFieldsForCustomerException(sprintf('Cannot set "%s" required fields for customer', implode(',', $command->getRequiredFields())));
        }
    }
}
