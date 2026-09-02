<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;

/**
 * Defines contract to handle @see GetCombinationSuppliers query
 */
interface GetCombinationSuppliersHandlerInterface
{
    /**
     * @param GetCombinationSuppliers $query
     *
     * @return ProductSupplierForEditing[]
     */
    public function handle(GetCombinationSuppliers $query): array;
}
