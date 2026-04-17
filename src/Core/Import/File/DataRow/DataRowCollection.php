<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use ArrayIterator;
use ReturnTypeWillChange;
use Traversable;

/**
 * Class DataRowCollection defines a collection of data rows.
 */
final class DataRowCollection implements DataRowCollectionInterface
{
    /**
     * @var array of DataRowInterface objects
     */
    private $dataRows = [];

    /**
     * {@inheritdoc}
     */
    public function addDataRow(DataRowInterface $dataRow)
    {
        $this->dataRows[] = $dataRow;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->dataRows);
    }

    /**
     * {@inheritdoc}
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            return null;
        }

        return $this->dataRows[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->dataRows[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->dataRows[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->dataRows);
    }

    /**
     * {@inheritdoc}
     */
    public function getLargestRowSize()
    {
        $maxSize = 0;

        foreach ($this->dataRows as $dataRow) {
            $maxSize = max($maxSize, count($dataRow));
        }

        return $maxSize;
    }
}
