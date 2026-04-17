<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;

/**
 * Defines contract to handle @see BulkDeleteProductCommand
 */
interface BulkDeleteProductHandlerInterface
{
    /**
     * @param BulkDeleteProductCommand $command
     */
    public function handle(BulkDeleteProductCommand $command): void;
}
