<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;

/**
 * Defines contract to handle @see UpdateCombinationSuppliersCommand
 */
interface UpdateCombinationSuppliersHandlerInterface
{
    /**
     * @param UpdateCombinationSuppliersCommand $command
     *
     * @return ProductSupplierAssociation[] new product combination suppliers ids list
     */
    public function handle(UpdateCombinationSuppliersCommand $command): array;
}
