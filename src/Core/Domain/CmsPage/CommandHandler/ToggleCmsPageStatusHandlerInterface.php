<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\ToggleCmsPageStatusCommand;

/**
 * Defines contract for ToggleCmsPageStatusHandler.
 */
interface ToggleCmsPageStatusHandlerInterface
{
    /**
     * @param ToggleCmsPageStatusCommand $command
     */
    public function handle(ToggleCmsPageStatusCommand $command);
}
