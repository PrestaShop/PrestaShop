<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;

/**
 * Defines contract to handle @see SetProductDefaultSupplierCommand
 */
interface SetProductDefaultSupplierHandlerInterface
{
    /**
     * @param SetProductDefaultSupplierCommand $command
     */
    public function handle(SetProductDefaultSupplierCommand $command): void;
}
