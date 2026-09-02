<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;

/**
 * Interface ToggleSupplierStatusHandlerInterface defines contract for ToggleSupplierStatusHandler.
 */
interface ToggleSupplierStatusHandlerInterface
{
    /**
     * @param ToggleSupplierStatusCommand $command
     */
    public function handle(ToggleSupplierStatusCommand $command);
}
