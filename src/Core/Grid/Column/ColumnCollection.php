<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
    const POSITION_AFTER = 'after';

    /**
     * @internal
     */
    const POSITION_BEFORE = 'before';

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
     * Insert new column into collection at given position.
     *
     * @param string $id Existing column id
     * @param ColumnInterface $newColumn Column to insert
     * @param string $position Position: "before" or "after"
     *
     * @throws ColumnNotFoundException When column with gieven $id does not exist
     */
    private function insertByPosition($id, ColumnInterface $newColumn, $position)
    {
        if (!isset($this->items[$id])) {
            throw new ColumnNotFoundException(sprintf(
                'Cannot insert new column into collection. Column with id "%s" was not found.',
                $id
            ));
        }

        $existingColumnKeyPosition = array_search($id, array_keys($this->items));

        if (self::POSITION_AFTER === $position) {
            ++$existingColumnKeyPosition;
        }

        $columns = array_slice($this->items, 0, $existingColumnKeyPosition, true) +
            [$newColumn->getId() => $newColumn] +
            array_slice($this->items, $existingColumnKeyPosition, $this->count(), true);

        $this->items = $columns;
    }
}
