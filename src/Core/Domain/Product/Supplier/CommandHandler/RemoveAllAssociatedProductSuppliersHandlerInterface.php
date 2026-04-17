<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;

/**
 * Defines contract to handle @see RemoveAllAssociatedProductSuppliersCommand
 */
interface RemoveAllAssociatedProductSuppliersHandlerInterface
{
    /**
     * @param RemoveAllAssociatedProductSuppliersCommand $command
     */
    public function handle(RemoveAllAssociatedProductSuppliersCommand $command): void;
}
