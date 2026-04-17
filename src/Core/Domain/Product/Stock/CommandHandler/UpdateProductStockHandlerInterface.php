<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Stock\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;

/**
 * Defines contract to handle @var UpdateProductStockAvailableCommand
 */
interface UpdateProductStockHandlerInterface
{
    /**
     * @param UpdateProductStockAvailableCommand $command
     */
    public function handle(UpdateProductStockAvailableCommand $command): void;
}
