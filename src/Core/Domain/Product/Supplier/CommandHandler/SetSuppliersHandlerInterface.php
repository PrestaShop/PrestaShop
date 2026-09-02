<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierAssociation;

/**
 * Defines contract to handle @see SetSuppliersCommand
 */
interface SetSuppliersHandlerInterface
{
    /**
     * @param SetSuppliersCommand $command
     *
     * @return ProductSupplierAssociation[]
     */
    public function handle(SetSuppliersCommand $command): array;
}
