<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\RemoveAllProductsFromPackCommand;

/**
 * Defines contract to handle @see RemoveAllProductsFromPackCommand
 */
interface RemoveAllProductsFromPackHandlerInterface
{
    /**
     * @param RemoveAllProductsFromPackCommand $command
     */
    public function handle(RemoveAllProductsFromPackCommand $command): void;
}
