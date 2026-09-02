<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\BulkDeleteOrderReturnStateCommand;

/**
 * Defines contract for BulkDeleteOrderStateHandler
 */
interface BulkDeleteOrderReturnStateHandlerInterface
{
    /**
     * @param BulkDeleteOrderReturnStateCommand $command
     */
    public function handle(BulkDeleteOrderReturnStateCommand $command): void;
}
