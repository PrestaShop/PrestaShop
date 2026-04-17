<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;

/**
 * Defines contract to handle @see GetProductSupplierOptions
 */
interface GetProductSupplierOptionsHandlerInterface
{
    /**
     * @param GetProductSupplierOptions $query
     *
     * @return ProductSupplierOptions
     */
    public function handle(GetProductSupplierOptions $query): ProductSupplierOptions;
}
