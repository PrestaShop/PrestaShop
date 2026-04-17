<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchProductCombinations;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationsCollection;

interface SearchProductCombinationsHandlerInterface
{
    /**
     * @param SearchProductCombinations $query
     *
     * @return ProductCombinationsCollection
     */
    public function handle(SearchProductCombinations $query): ProductCombinationsCollection;
}
