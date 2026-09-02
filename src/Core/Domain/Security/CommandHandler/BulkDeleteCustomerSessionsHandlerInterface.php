<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Security\Command\BulkDeleteCustomerSessionsCommand;

/**
 * Defines interface for customer bulk delete command handler.
 */
interface BulkDeleteCustomerSessionsHandlerInterface
{
    /**
     * @param BulkDeleteCustomerSessionsCommand $command
     */
    public function handle(BulkDeleteCustomerSessionsCommand $command): void;
}
