<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;

/**
 * Interface EditCategoryHandlerInterface.
 */
interface EditCategoryHandlerInterface
{
    /**
     * @param EditCategoryCommand $command
     */
    public function handle(EditCategoryCommand $command);
}
