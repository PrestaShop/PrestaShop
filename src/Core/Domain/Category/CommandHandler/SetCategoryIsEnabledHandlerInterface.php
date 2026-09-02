<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Category\Command\SetCategoryIsEnabledCommand;

/**
 * Interface for service that set category to be enabled or disabled.
 */
interface SetCategoryIsEnabledHandlerInterface
{
    /**
     * @param SetCategoryIsEnabledCommand $command
     */
    public function handle(SetCategoryIsEnabledCommand $command);
}
