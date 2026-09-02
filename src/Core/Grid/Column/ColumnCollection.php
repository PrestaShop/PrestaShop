<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;

/**
 * Class ColumnCollection holds collection of columns for grid.
 *
 * @property ColumnInterface[] $items
 */
final class ColumnCollection extends AbstractCollection implements ColumnCollectionInterface
{
    /**
     * @internal
     */
    public const POSITION_AFTER = 'after';

    /**
     * @internal
     */
    public const POSITION_BEFORE = 'before';

    /**
     * {@inheritdoc}
     */
    public function add(ColumnInterface $column)
    {
        $this->items[$column->getId()] = $column;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAfter($id, ColumnInterface $newColumn)
    {
        $this->insertByPosition($id, $newColumn, self::POSITION_AFTER);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addBefore($id, ColumnInterface $newColumn)
    {
        $this->insertByPosition($id, $newColumn, self::POSITION_BEFORE);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        if (isset($this->items[$id])) {
            unset($this->items[$id]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $columns = [];

        foreach ($this->items as $item) {
            $columns[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'type' => $item->getType(),
                'options' => $item->getOptions(),
            ];
        }

        return $columns;
    }

    /**
     * Move an existing Column to a specific position.
     *
     * @param string $id the Column ID original position in the Collection
     * @param int $position the Column ID destination position in the Collection
     *
     * @return self
     */
    public function move($id, $position)
    {
        if (!isset($this->items[$id])) {
            throw new ColumnNotFoundException(sprintf('Cannot insert new column into collection. Column with id "%s" was not found.', $id));
        }

        $column = $this->items[$id];
        unset($this->items[$id]);

        $columns = array_slice($this->items, 0, $position, true) +
            [$column->getId() => $column] +
            array_slice($this->items, $position, null, true);

        $this->items = $columns;

        return $this;
    }

    /**
     * Insert new column into collection at given position.
     *
     * @param string $id Existing column id
     * @param ColumnInterface $newColumn Column to insert
     * @param string $position Position: "before" or "after"
     *
     * @throws ColumnNotFoundException When column with given $id does not exist
     */
    private function insertByPosition($id, ColumnInterface $newColumn, $position)
    {
        if (!isset($this->items[$id])) {
            throw new ColumnNotFoundException(sprintf('Cannot insert new column into collection. Column with id "%s" was not found.', $id));
        }

        $existingColumnKeyPosition = (int) array_search($id, array_keys($this->items));

        if (self::POSITION_AFTER === $position) {
            ++$existingColumnKeyPosition;
        }

        $columns = array_slice($this->items, 0, $existingColumnKeyPosition, true)
            + [$newColumn->getId() => $newColumn]
            + array_slice($this->items, $existingColumnKeyPosition, null, true)
        ;

        $this->items = $columns;
    }
}
