<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\UpdateCustomerThreadStatusCommand;

/**
 * Interface for service that handles updating thread status
 */
interface UpdateCustomerThreadStatusHandlerInterface
{
    /**
     * @param UpdateCustomerThreadStatusCommand $command
     */
    public function handle(UpdateCustomerThreadStatusCommand $command);
}
