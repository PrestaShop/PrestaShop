<?php

/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Localization\Specification;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface as NumberSpecificationInterface;

/**
 * Number specifications collection
 * Contains a list of Number specification items (all implementing NumberInterface).
 */
class NumberCollection implements IteratorAggregate, Countable
{
    /**
     * The Number specification items.
     *
     * @var NumberSpecificationInterface[]
     */
    protected $numberSpecifications = [];

    /**
     * Gets the current NumberCollection as an Iterator that includes all Number specification items.
     *
     * It implements \IteratorAggregate.
     *
     * @return ArrayIterator|NumberSpecificationInterface[]
     *                                                      An ArrayIterator object for iterating over Number specification items
     */
    public function getIterator()
    {
        return new ArrayIterator($this->numberSpecifications);
    }

    /**
     * Gets the number of Number specification items in this collection.
     *
     * @return int
     *             The number of Number specification items
     */
    public function count()
    {
        return count($this->numberSpecifications);
    }

    /**
     * Adds a Number specification item at the end of the collection.
     *
     * @param int|string                   $index
     *                                                          The item index
     * @param numberSpecificationInterface $numberSpecification
     *                                                          The Number specification item to add
     *
     * @return NumberCollection
     *                          Fluent interface
     */
    public function add($index, NumberSpecificationInterface $numberSpecification)
    {
        $this->numberSpecifications[$index] = $numberSpecification;

        return $this;
    }

    /**
     * Returns all Number specification items in this collection.
     *
     * @return NumberSpecificationInterface[]
     *                                        An array of Number specification items
     */
    public function all()
    {
        return $this->numberSpecifications;
    }

    /**
     * Gets a Number specification item by index.
     *
     * @param int|string $index
     *                          The Number specification item index into this collection
     *                          (@see NumberCollection::add())
     *
     * @return NumberSpecificationInterface|null
     *                                           A Number specification instance or null when not found
     */
    public function get($index)
    {
        return isset($this->numberSpecifications[$index])
            ? $this->numberSpecifications[$index]
            : null;
    }

    /**
     * Removes a Number specification item or an array of Number specification items by index from the collection.
     *
     * @param int|string|int[]|string[]|array $index
     *                                               The Number specification item index or an array of Number specification item indexes
     *
     * @return NumberCollection
     *                          Fluent interface
     */
    public function remove($index)
    {
        foreach ((array) $index as $i) {
            unset($this->numberSpecifications[$i]);
        }

        return $this;
    }

    /**
     * Clear the collection, removing all contained Number specification items.
     *
     * @return NumberCollection
     *                          Fluent interface
     */
    public function clear()
    {
        $this->numberSpecifications = [];

        return $this;
    }
}
