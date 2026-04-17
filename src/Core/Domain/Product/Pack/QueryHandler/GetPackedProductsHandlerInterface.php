<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;

/**
 * Defines contract for GetPackedProductsHandler
 */
interface GetPackedProductsHandlerInterface
{
    /**
     * @param GetPackedProducts $query
     *
     * @return PackedProductDetails[]
     */
    public function handle(GetPackedProducts $query): array;
}
