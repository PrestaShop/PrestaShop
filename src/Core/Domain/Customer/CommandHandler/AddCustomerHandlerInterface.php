<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\AddCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Interface for service that handles command that adds new customer
 */
interface AddCustomerHandlerInterface
{
    /**
     * @param AddCustomerCommand $command
     *
     * @return CustomerId
     */
    public function handle(AddCustomerCommand $command);
}
