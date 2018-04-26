<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column;

final class ColumnCollection
{
    /**
     * The columns.
     *
     * @var array
     */
    private $columns;

    /**
     * Add a new Column to the collection.
     *
     * @param ColumnInterface $column The column.
     * @return self
     */
    public function add(ColumnInterface $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Remove a Column from collection by his name.
     *
     * @param $columnName The Column name.
     */
    public function removeByName($columnName)
    {
        foreach ($this->columns as $key => $column) {
            if ($column->getName() === $columnName) {
                unset($this->columns[$key]);
            }
        }
    }

    /**
     * @return array the ordered list of columns by priority.
     */
    public function all()
    {
        usort($this->columns, function (Column $columnA, Column $columnB) {
            $priorityA = $columnA->getPriority();
            $priorityB = $columnB->getPriority();

            if ($priorityA === $priorityB) {
                return 0;
            }

            return ($priorityA < $priorityB) ? -1 : 1;
        });

        return $this->columns;
    }

    /**
     * Helper method to ease columns creation.
     *
     * @param array $columns
     *
     * @return ColumnCollection
     */
    public function createFromArray(array $columns)
    {
        $collection = new self();
        foreach ($columns as $column) {
            if (array_key_exists('name', $column)) {
                $collectionItem = new Column($column['name']);
                if (array_key_exists('priority', $column)) {
                    $collectionItem->setPriority($column['priority']);
                }

                if (array_key_exists('type', $column)) {
                    $collectionItem->setType($column['type']);
                }

                $collection->add($collectionItem);
            }
        }

        return $collection;
    }
}
