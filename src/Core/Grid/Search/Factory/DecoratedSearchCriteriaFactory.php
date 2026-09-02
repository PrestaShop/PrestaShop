<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Search\Factory;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Interface DecoratedSearchCriteriaFactory defines contract for decorated search criteria factory.
 */
interface DecoratedSearchCriteriaFactory
{
    /**
     * Create new search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return SearchCriteriaInterface
     */
    public function createFrom(SearchCriteriaInterface $searchCriteria);
}
