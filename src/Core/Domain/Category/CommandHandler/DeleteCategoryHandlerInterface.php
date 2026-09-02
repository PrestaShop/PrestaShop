<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;

/**
 * Interface DeleteCategoryHandlerInterface.
 */
interface DeleteCategoryHandlerInterface
{
    /**
     * @param DeleteCategoryCommand $command
     */
    public function handle(DeleteCategoryCommand $command);
}
