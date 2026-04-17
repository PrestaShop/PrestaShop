<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataCell;

/**
 * Interface DataCellInterface describes a data cell from imported file.
 */
interface DataCellInterface
{
    /**
     * Get the value of the cell.
     *
     * @return string
     */
    public function getValue();

    /**
     * Get the key of the cell.
     *
     * @return string
     */
    public function getKey();
}
