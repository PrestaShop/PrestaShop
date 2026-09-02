<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\BulkDeleteCombinationCommand;

/**
 * Defines contract to handle @see BulkDeleteCombinationCommand
 */
interface BulkDeleteCombinationHandlerInterface
{
    /**
     * @param BulkDeleteCombinationCommand $command
     */
    public function handle(BulkDeleteCombinationCommand $command): void;
}
