<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;

/**
 * Defines contract to handle @see UpdateProductSuppliersCommand
 */
interface UpdateProductSuppliersHandlerInterface
{
    /**
     * @param UpdateProductSuppliersCommand $command
     *
     * @return ProductSupplierAssociation[] new product suppliers ids list
     */
    public function handle(UpdateProductSuppliersCommand $command): array;
}
