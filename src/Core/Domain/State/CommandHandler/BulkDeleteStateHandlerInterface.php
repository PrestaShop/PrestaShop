<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkDeleteStateCommand;

/**
 * Defines contract for BulkDeleteStateHandler
 */
interface BulkDeleteStateHandlerInterface
{
    /**
     * @param BulkDeleteStateCommand $command
     */
    public function handle(BulkDeleteStateCommand $command): void;
}
