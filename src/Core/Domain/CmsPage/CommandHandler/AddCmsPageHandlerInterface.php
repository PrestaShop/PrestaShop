<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\AddCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;

/**
 * Interface for services that handles AddCmsPageCommand
 */
interface AddCmsPageHandlerInterface
{
    /**
     * @param AddCmsPageCommand $command
     *
     * @return CmsPageId
     */
    public function handle(AddCmsPageCommand $command);
}
