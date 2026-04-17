<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkUpdateCategoriesStatusCommand;

/**
 * Interface UpdateCategoriesStatusHandlerInterface.
 */
interface BulkUpdateCategoriesStatusHandlerInterface
{
    /**
     * @param BulkUpdateCategoriesStatusCommand $command
     */
    public function handle(BulkUpdateCategoriesStatusCommand $command);
}
