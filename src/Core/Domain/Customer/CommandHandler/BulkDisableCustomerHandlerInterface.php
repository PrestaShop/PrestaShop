<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\BulkDisableCustomerCommand;

/**
 * Defines interface for handling command that disables given customers.
 */
interface BulkDisableCustomerHandlerInterface
{
    /**
     * @param BulkDisableCustomerCommand $command
     */
    public function handle(BulkDisableCustomerCommand $command);
}
