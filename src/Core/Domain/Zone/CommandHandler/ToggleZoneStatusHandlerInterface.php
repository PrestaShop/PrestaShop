<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Zone\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Zone\Command\ToggleZoneStatusCommand;

/**
 * Interface ToggleZoneStatusHandlerInterface defines contract for ToggleZoneStatusHandler
 */
interface ToggleZoneStatusHandlerInterface
{
    /**
     * @param ToggleZoneStatusCommand $command
     */
    public function handle(ToggleZoneStatusCommand $command): void;
}
