<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryThumbnailImageCommand;

/**
 * Defines contract for service which handles thumbnail image delete command.
 */
interface DeleteCategoryThumbnailImageHandlerInterface
{
    /**
     * @param DeleteCategoryThumbnailImageCommand $command
     */
    public function handle(DeleteCategoryThumbnailImageCommand $command);
}
