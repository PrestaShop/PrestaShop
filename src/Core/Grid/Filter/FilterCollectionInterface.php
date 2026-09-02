<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

/**
 * Interface FilterCollectionInterface defines contract for grid filters.
 */
interface FilterCollectionInterface
{
    /**
     * Add filter to collection.
     *
     * @param FilterInterface $filter
     *
     * @return self
     */
    public function add(FilterInterface $filter);

    /**
     * Remove filter from collection.
     *
     * @param string $filterName
     *
     * @return self
     */
    public function remove($filterName);

    /**
     * Get all filters.
     *
     * @return FilterInterface[]
     */
    public function all();
}
