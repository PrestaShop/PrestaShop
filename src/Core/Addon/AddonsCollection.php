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

namespace PrestaShop\PrestaShop\Core\Addon;

use PrestaShop\PrestaShop\Adapter\Module\Module as Addon;
use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * An ArrayCollection is a Collection implementation that wraps a regular PHP array.
 */
class AddonsCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * An array containing the addons of this collection.
     *
     * @var array
     */
    private $addons;

    /**
     * Initializes a new AddonsCollection.
     *
     * @param array $addons
     */
    public function __construct(array $addons = [])
    {
        $this->addons = $addons;
    }

    /**
     * Creates a new instance from the specified elements.
     *
     * This method is provided for derived classes to specify how a new
     * instance should be created when constructor semantics have changed.
     *
     * @param array $addons Elements.
     *
     * @return static
     */
    public static function createFrom(array $addons)
    {
        return new static($addons);
    }

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->addons;
    }

    /**
     * @return ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->addons);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by ArrayAccess interface.
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $addon)
    {
        if (!isset($offset)) {
            $this->add($addon);
            return;
        }

        $this->set($offset, $addon);
    }

    /**
     * Required by interface ArrayAccess.
     *
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Returns true if the key is found in the collection.
     *
     * @param mixed $key the key, can be integer or string
     * @return bool
     */
    public function containsKey($key)
    {
        return isset($this->addons[$key]) || array_key_exists($key, $this->addons);
    }

    /**
     * Returns true if the addon is found in the collection.
     *
     * @param Addon $addon the addon
     * @return bool
     */
    public function contains(Addon $addon)
    {
        return in_array($addon, $this->addons, true);
    }

    /**
     * {@inheritDoc}
     */
    public function indexOf(Addon $addon)
    {
        return array_search($addon, $this->addons, true);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->addons[$key] ? $this->addons[$key] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->addons);
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        return array_values($this->addons);
    }

    /**
     * Add an Addon with a specified key in the collection.
     *
     * @param mixed $key the key
     * @param Addon $addon the specified addon
     */
    public function set($key, Addon $addon)
    {
        $this->addons[$key] = $addon;
    }

    /**
     * Add an Addon in the collection.
     *
     * @param Addon $addon the specified addon
     * @return bool
     */
    public function add(Addon $addon)
    {
        $this->addons[] = $addon;

        return true;
    }

    /**
     * Remove an addon from the collection by key.
     *
     * @param mixed the key (can be int or string).
     * @return bool true if the addon has been found and removed.
     */
    public function removeByKey($key)
    {
        if (! isset($this->addons[$key]) && ! array_key_exists($key, $this->addons)) {
            return null;
        }

        $removed = $this->addons[$key];
        unset($this->addons[$key]);

        return $removed;
    }

    /**
     * Remove an addon from the collection by key.
     *
     * @param Addon $addon the addon to be removed.
     * @return bool true if the addon has been found and removed.
     */
    public function remove(Addon $addon)
    {
        $key = array_search($addon, $this->addons, true);

        if ($key === false) {
            return false;
        }

        unset($this->addons[$key]);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return empty($this->addons);
    }

    /**
     * Gets the sum of addons of the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->addons);
    }
}
