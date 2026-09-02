<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkEnableCmsPageCategoryCommand;

/**
 * Interface BulkEnableCmsPageCategoryHandlerInterface defines contract for BulkEnableCmsPageCategoryHandler.
 */
interface BulkEnableCmsPageCategoryHandlerInterface
{
    /**
     * @param BulkEnableCmsPageCategoryCommand $command
     */
    public function handle(BulkEnableCmsPageCategoryCommand $command);
}
