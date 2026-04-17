<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkEnableCustomerCommand;

/**
 * Defines interface for handling command that enables given customers.
 */
interface BulkEnableCustomerHandlerInterface
{
    /**
     * @param BulkEnableCustomerCommand $command
     */
    public function handle(BulkEnableCustomerCommand $command);
}
