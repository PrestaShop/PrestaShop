<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Search;

/**
 * Interface SearchCriteriaInterface.
 */
interface SearchCriteriaInterface
{
    /**
     * @return string|null Return order by or null to disable ordering
     */
    public function getOrderBy();

    /**
     * @return string|null Return order by or null to disable ordering
     */
    public function getOrderWay();

    /**
     * @return int|null Return offset or null to disable offset
     */
    public function getOffset();

    /**
     * @return int|null Return limit or null to disable limiting
     */
    public function getLimit();

    /**
     * @return array Return filters
     */
    public function getFilters();
}
