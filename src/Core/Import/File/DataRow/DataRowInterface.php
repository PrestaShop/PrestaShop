<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCellInterface;
use ReturnTypeWillChange;

/**
 * Interface DataRowInterface describes a data row from imported file.
 */
interface DataRowInterface extends ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Add a cell to this row.
     *
     * @param DataCellInterface $cell
     *
     * @return self
     */
    public function addCell(DataCellInterface $cell);

    /**
     * Create a data row from given array.
     *
     * @param array $data
     *
     * @return self
     */
    public static function createFromArray(array $data);

    /**
     * @param mixed $offset
     *
     * @return DataCellInterface
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset);

    /**
     * Check if the row is empty.
     *
     * @return bool
     */
    public function isEmpty();
}
