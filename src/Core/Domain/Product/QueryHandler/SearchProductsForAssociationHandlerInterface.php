<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProductsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForAssociation;

/**
 * Search a list of product that you can associate with, the query result contains minimum information
 * to display the product.
 */
interface SearchProductsForAssociationHandlerInterface
{
    /**
     * @param SearchProductsForAssociation $query
     *
     * @return ProductForAssociation[]
     */
    public function handle(SearchProductsForAssociation $query): array;
}
