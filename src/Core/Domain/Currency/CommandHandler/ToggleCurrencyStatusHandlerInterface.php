<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;

/**
 * Interface ToggleCurrencyStatusHandlerInterface defines contract for ToggleCurrencyStatusHandler.
 */
interface ToggleCurrencyStatusHandlerInterface
{
    /**
     * Handles currency status toggling logic.
     *
     * @param ToggleCurrencyStatusCommand $command
     */
    public function handle(ToggleCurrencyStatusCommand $command);
}
