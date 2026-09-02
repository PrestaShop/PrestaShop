<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

/**
 * Interface FilterInterface defines contract for grid filter.
 */
interface FilterInterface
{
    /**
     * Get filter type to use.
     *
     * @return string Fully qualified filter type class name
     */
    public function getType();

    /**
     * Get filter name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set filter type options.
     *
     * @param array $filterTypeOptions
     *
     * @return self
     */
    public function setTypeOptions(array $filterTypeOptions);

    /**
     * Get filter type options.
     *
     * @return array
     */
    public function getTypeOptions();

    /**
     * Set column ID if filter is associated with column.
     *
     * @param string $columnId
     *
     * @return self
     */
    public function setAssociatedColumn($columnId);

    /**
     * Get associated column.
     *
     * @return string|null
     */
    public function getAssociatedColumn();
}
