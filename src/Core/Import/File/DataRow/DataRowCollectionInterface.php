<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use ArrayAccess;
use IteratorAggregate;

/**
 * Interface DataRowCollectionInterface describes a collection of data rows.
 */
interface DataRowCollectionInterface extends ArrayAccess, IteratorAggregate
{
    /**
     * Add a data row to this collection.
     *
     * @param DataRowInterface $dataRow
     *
     * @return self
     */
    public function addDataRow(DataRowInterface $dataRow);

    /**
     * Get the number of cells in the largest row of collection.
     *
     * @return int
     */
    public function getLargestRowSize();
}
