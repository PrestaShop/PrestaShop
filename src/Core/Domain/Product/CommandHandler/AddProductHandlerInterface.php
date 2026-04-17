<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Defines contract for AddProductHandler
 */
interface AddProductHandlerInterface
{
    /**
     * @param AddProductCommand $command
     *
     * @return ProductId
     */
    public function handle(AddProductCommand $command): ProductId;
}
