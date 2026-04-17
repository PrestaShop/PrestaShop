<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Interface GridFactoryInterface exposes contract for grid factory which is responsible for creating Grid instances.
 */
interface GridFactoryInterface
{
    /**
     * Create grid with filtered data.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return GridInterface
     */
    public function getGrid(SearchCriteriaInterface $searchCriteria): GridInterface;
}
