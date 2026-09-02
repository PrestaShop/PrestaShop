<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;

/**
 * Defines interface to handle @see RemoveAllRelatedProductsCommand
 */
interface RemoveAllRelatedProductsHandlerInterface
{
    /**
     * @param RemoveAllRelatedProductsCommand $command
     */
    public function handle(RemoveAllRelatedProductsCommand $command): void;
}
