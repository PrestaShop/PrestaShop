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

namespace PrestaShop\PrestaShop\Core\Filter;

use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;

/**
 * This class filters associative arrays.
 *
 * Usage:
 *
 * ```php
 * $map = [
 *     'foo' => 'something',
 *     'bar' => null,
 *     'baz' => array(),
 * ];
 *
 * $filter = (new HashMapFilter())
 *     ->whitelist(
 *         ['foo', 'baz']
 *     );
 *
 * $filtered = $filter->filter();
 * // returns [ 'foo' => something, 'baz' => [] ];
 * ```
 *
 * You can also nest filters:
 *
 * ```php
 * $map = [
 *     'foo' => 'something',
 *     'bar' => null,
 *     'baz' => [true, false, 1, 0]
 * ];
 *
 * $filter = (new HashMapFilter())
 *     ->whitelist([
 *         'foo',
 *         'baz' => OnlyTruthyValuesInCollectionFilter()
 *     ]);
 *
 * $filtered = $filter->filter();
 * // returns [ 'foo' => something, 'baz' => [ true, 1 ] ];
 * ```
 */
class HashMapWhitelistFilter implements FilterInterface
{
    /**
     * Index of $keyToKeep => true.
     *
     * @var true[]
     */
    protected $whitelistItems = [];

    /**
     * Nested filters, indexed by $keyToKeep.
     *
     * @var FilterInterface[]
     */
    protected $filters = [];

    /**
     * Adds keys to the whitelist.
     *
     * This method accepts either:
     * - string[] an array of keys to keep
     * - FilterInterface[] an array of filters, indexed by keys to keep
     * - A mixture of the two
     *
     * @param string[]|FilterInterface[] $definition
     *
     * @return $this
     */
    public function whitelist($definition)
    {
        foreach ($definition as $k => $value) {
            $this->addWhitelistItem($k, $value);
        }

        return $this;
    }

    /**
     * Removes the provided key from the whitelist.
     *
     * @param string|int $key
     *
     * @return $this
     *
     * @throws FilterException if $key is not scalar
     */
    public function removeFromWhitelist($key)
    {
        if (!is_scalar($key)) {
            throw new FilterException(
                sprintf('Invalid parameter %s', print_r($key, true))
            );
        }

        unset(
            $this->whitelistItems[$key],
            $this->filters[$key]
        );

        return $this;
    }

    /**
     * Returns the white list.
     *
     * @return true[]
     */
    public function getWhitelistItems()
    {
        return $this->whitelistItems;
    }

    /**
     * Returns the nested filters, indexed by $keyToKeep.
     *
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Filters the subject.
     *
     * @param array $subject
     *
     * @return array The filtered subject
     *
     * @throws \RuntimeException
     */
    public function filter($subject)
    {
        // keep whitelisted items
        if ($subject instanceof AbstractLazyArray) {
            // avoid modifying the original object
            $subject = clone $subject;
            $subject->intersectKey($this->whitelistItems);
            // run nested filters
            foreach ($this->filters as $key => $filter) {
                if ($subject->offsetExists($key)) {
                    $filteredValue = $filter->filter($subject->offsetGet($key));
                    $subject->offsetSet($key, $filteredValue, true);
                }
            }
        } else {
            $subject = array_intersect_key($subject, $this->whitelistItems);
            // run nested filters
            foreach ($this->filters as $key => $filter) {
                if (array_key_exists($key, $subject)) {
                    $subject[$key] = $filter->filter($subject[$key]);
                }
            }
        }

        return $subject;
    }

    /**
     * Adds an element to the whitelist.
     *
     * @param int|string $paramKey
     * @param string|FilterInterface $paramValue
     *
     * @return $this
     */
    private function addWhitelistItem($paramKey, $paramValue)
    {
        $keyToWhitelist = $paramValue;
        if ($paramValue instanceof FilterInterface) {
            $this->filters[$paramKey] = $paramValue;

            $keyToWhitelist = $paramKey;
        }

        // add as key to allow faster search
        $this->whitelistItems[$keyToWhitelist] = true;

        return $this;
    }
}
