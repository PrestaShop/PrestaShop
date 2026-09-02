<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductsPositionsCommand;

/**
 * Defines contract to handle @see UpdateProductsPositionsCommand
 */
interface UpdateProductsPositionsHandlerInterface
{
    /**
     * @param UpdateProductsPositionsCommand $command
     */
    public function handle(UpdateProductsPositionsCommand $command): void;
}
