<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetRelatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RelatedProduct;

/**
 * Defines contract to handle @see GetRelatedProducts query
 */
interface GetRelatedProductsHandlerInterface
{
    /**
     * @param GetRelatedProducts $query
     *
     * @return RelatedProduct[]
     */
    public function handle(GetRelatedProducts $query): array;
}
