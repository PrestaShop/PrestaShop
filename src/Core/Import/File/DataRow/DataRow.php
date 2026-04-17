<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use ArrayIterator;
use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCell;
use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCellInterface;
use Traversable;

/**
 * Class DataRow defines a basic data row of imported file.
 */
final class DataRow implements DataRowInterface
{
    /**
     * @var DataCellInterface[]
     */
    private $cells = [];

    /**
     * {@inheritdoc}
     */
    public function addCell(DataCellInterface $cell)
    {
        $this->cells[] = $cell;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromArray(array $data)
    {
        $row = new self();

        foreach ($data as $key => $value) {
            $row->addCell(new DataCell($key, $value));
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->cells);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->cells[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->cells[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->cells[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->cells);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->cells);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        if (0 === count($this->cells)) {
            return true;
        }

        foreach ($this->cells as $cell) {
            // If at least one cell is not empty - the row is not empty.
            if ('' !== $cell->getValue()) {
                return false;
            }
        }

        return true;
    }
}
