<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\EditCmsPageCategoryCommand;

/**
 * Defines contract for EditCmsPageCategoryHandler.
 */
interface EditCmsPageCategoryHandlerInterface
{
    /**
     * @param EditCmsPageCategoryCommand $command
     */
    public function handle(EditCmsPageCategoryCommand $command);
}
