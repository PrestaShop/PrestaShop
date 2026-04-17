<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\ToggleCmsPageCategoryStatusCommand;

/**
 * Interface ToggleCmsPageCategoryStatusHandlerInterface defines contract for ToggleCmsPageCategoryStatusHandler.
 */
interface ToggleCmsPageCategoryStatusHandlerInterface
{
    /**
     * @param ToggleCmsPageCategoryStatusCommand $command
     */
    public function handle(ToggleCmsPageCategoryStatusCommand $command);
}
