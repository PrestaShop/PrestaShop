<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\UpdateCategoryPositionCommand;

/**
 * Interface for service that updates category position
 */
interface UpdateCategoryPositionHandlerInterface
{
    /**
     * @param UpdateCategoryPositionCommand $command
     */
    public function handle(UpdateCategoryPositionCommand $command);
}
