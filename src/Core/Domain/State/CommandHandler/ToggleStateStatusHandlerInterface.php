<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\ToggleStateStatusCommand;

/**
 * Interface ToggleStateStatusHandlerInterface defines contract for ToggleStateStatusHandler
 */
interface ToggleStateStatusHandlerInterface
{
    /**
     * @param ToggleStateStatusCommand $command
     */
    public function handle(ToggleStateStatusCommand $command): void;
}
