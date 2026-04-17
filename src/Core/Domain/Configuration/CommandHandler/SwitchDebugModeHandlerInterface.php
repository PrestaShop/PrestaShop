<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Configuration\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Configuration\Command\SwitchDebugModeCommand;

/**
 * Interface for service that implements debug mode switching
 */
interface SwitchDebugModeHandlerInterface
{
    /**
     * @param SwitchDebugModeCommand $command
     */
    public function handle(SwitchDebugModeCommand $command);
}
