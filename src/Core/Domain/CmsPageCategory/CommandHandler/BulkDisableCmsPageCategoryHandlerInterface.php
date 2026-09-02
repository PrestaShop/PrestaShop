<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkDisableCmsPageCategoryCommand;

/**
 * Interface BulkDisableCmsPageCategoryHandlerInterface defines contract for BulkDisableCmsPageCategoryHandler.
 */
interface BulkDisableCmsPageCategoryHandlerInterface
{
    /**
     * @param BulkDisableCmsPageCategoryCommand $command
     */
    public function handle(BulkDisableCmsPageCategoryCommand $command);
}
