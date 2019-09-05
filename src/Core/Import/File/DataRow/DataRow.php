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

namespace PrestaShop\PrestaShop\Core\Import\File\DataRow;

use ArrayIterator;
use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCell;
use PrestaShop\PrestaShop\Core\Import\File\DataCell\DataCellInterface;

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
    public function offsetExists($offset)
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
    public function offsetSet($offset, $value)
    {
        $this->cells[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->cells[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->cells);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
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
