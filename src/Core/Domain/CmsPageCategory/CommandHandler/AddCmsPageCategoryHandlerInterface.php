<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\AddCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Interface AddCmsPageCategoryHandlerInterface defines contract for AddCmsPageCategoryHandler.
 */
interface AddCmsPageCategoryHandlerInterface
{
    /**
     * @param AddCmsPageCategoryCommand $command
     *
     * @return CmsPageCategoryId
     */
    public function handle(AddCmsPageCategoryCommand $command);
}
