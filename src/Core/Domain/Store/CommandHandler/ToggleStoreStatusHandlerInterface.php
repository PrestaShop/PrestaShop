<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Store\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;

/**
 * Interface for ToggleStoreStatusHandler
 */
interface ToggleStoreStatusHandlerInterface
{
    /**
     * @param ToggleStoreStatusCommand $command
     */
    public function handle(ToggleStoreStatusCommand $command): void;
}
