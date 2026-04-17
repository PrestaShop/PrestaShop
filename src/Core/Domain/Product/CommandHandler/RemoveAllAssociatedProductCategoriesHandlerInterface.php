<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;

/**
 * Defines contract to handle @see RemoveAllAssociatedProductCategoriesCommand
 */
interface RemoveAllAssociatedProductCategoriesHandlerInterface
{
    /**
     * @param RemoveAllAssociatedProductCategoriesCommand $command
     */
    public function handle(RemoveAllAssociatedProductCategoriesCommand $command): void;
}
