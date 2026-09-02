<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\DeleteCmsPageCommand;

/**
 * Defines contract for DeleteCmsPageHandler.
 */
interface DeleteCmsPageHandlerInterface
{
    /**
     * @param DeleteCmsPageCommand $command
     */
    public function handle(DeleteCmsPageCommand $command);
}
