<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\DeleteCustomerCommand;

/**
 * Defines interface for handling command that deletes given customer.
 */
interface DeleteCustomerHandlerInterface
{
    /**
     * @param DeleteCustomerCommand $command
     */
    public function handle(DeleteCustomerCommand $command);
}
