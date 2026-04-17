<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\FoundProduct;

/**
 * Interface for handling SearchProducts query
 */
interface SearchProductsHandlerInterface
{
    /**
     * @param SearchProducts $query
     *
     * @return FoundProduct[]
     */
    public function handle(SearchProducts $query): array;
}
