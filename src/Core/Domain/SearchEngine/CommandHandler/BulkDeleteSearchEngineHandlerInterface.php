<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\BulkDeleteSearchEngineCommand;

/**
 * Defines contract for BulkDeleteSearchEngineHandler.
 */
interface BulkDeleteSearchEngineHandlerInterface
{
    /**
     * @param BulkDeleteSearchEngineCommand $command
     */
    public function handle(BulkDeleteSearchEngineCommand $command): void;
}
