<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use Countable;
use Iterator;

/**
 * Interface ColumnCollectionInterface defines contract for grid column collection.
 */
interface ColumnCollectionInterface extends Iterator, Countable
{
    /**
     * Add column to collection.
     *
     * @param ColumnInterface $column
     *
     * @return static
     */
    public function add(ColumnInterface $column);

    /**
     * Add column after given column.
     *
     * @param string $id Column id
     * @param ColumnInterface $column
     *
     * @return static
     */
    public function addAfter($id, ColumnInterface $column);

    /**
     * @param string $id Column id
     * @param ColumnInterface $column
     *
     * @return static
     */
    public function addBefore($id, ColumnInterface $column);

    /**
     * Remove column from collection.
     *
     * @param string $id
     *
     * @return static
     */
    public function remove($id);

    /**
     * Get columns as array.
     *
     * @return array
     */
    public function toArray();
}
