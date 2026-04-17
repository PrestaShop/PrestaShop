<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\DeleteCmsPageCategoryCommand;

/**
 * Interface DeleteCmsPageCategoryHandlerInterface defines contract for DeleteCmsPageCategoryHandler.
 */
interface DeleteCmsPageCategoryHandlerInterface
{
    /**
     * @param DeleteCmsPageCategoryCommand $command
     */
    public function handle(DeleteCmsPageCategoryCommand $command);
}
