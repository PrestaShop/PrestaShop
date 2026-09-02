<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;

/**
 * Defines contract for ToggleTaxStatusHandler
 */
interface ToggleTaxStatusHandlerInterface
{
    /**
     * @param ToggleTaxStatusCommand $command
     */
    public function handle(ToggleTaxStatusCommand $command);
}
