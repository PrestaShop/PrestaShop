<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\BulkToggleStateStatusCommand;

/**
 * Defines contract for BulkToggleStateStatusHandler
 */
interface BulkToggleStateStatusHandlerInterface
{
    /**
     * @param BulkToggleStateStatusCommand $command
     */
    public function handle(BulkToggleStateStatusCommand $command): void;
}
