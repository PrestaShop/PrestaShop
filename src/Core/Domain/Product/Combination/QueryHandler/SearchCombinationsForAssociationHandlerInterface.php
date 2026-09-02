<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchCombinationsForAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForAssociation;

/**
 * Search a list of combination that you can associate with, the query result contains minimum information
 * to display the product (the returned list can contain product which have no combinations).
 */
interface SearchCombinationsForAssociationHandlerInterface
{
    /**
     * @param SearchCombinationsForAssociation $query
     *
     * @return CombinationForAssociation[]
     */
    public function handle(SearchCombinationsForAssociation $query): array;
}
