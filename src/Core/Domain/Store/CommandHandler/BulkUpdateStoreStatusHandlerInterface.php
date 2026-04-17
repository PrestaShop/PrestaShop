<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkUpdateStoreStatusCommand;

/**
 * Defines contract for BulkUpdateStoreStatusHandler
 */
interface BulkUpdateStoreStatusHandlerInterface
{
    /**
     * @param BulkUpdateStoreStatusCommand $command
     */
    public function handle(BulkUpdateStoreStatusCommand $command): void;
}
