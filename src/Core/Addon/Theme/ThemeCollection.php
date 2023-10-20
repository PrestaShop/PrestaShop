<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme as AddonTheme;
use Traversable;

/**
 * An ArrayCollection is a Collection implementation that wraps a regular PHP array.
 */
class ThemeCollection implements ArrayAccess, Countable, IteratorAggregate
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
     * @param array $addons elements
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
     * @return ArrayIterator|Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->addons);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by ArrayAccess interface.
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $addon): void
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
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * Returns true if the key is found in the collection.
     *
     * @param mixed $key the key, can be integer or string
     *
     * @return bool
     */
    public function containsKey($key)
    {
        return isset($this->addons[$key]) || array_key_exists($key, $this->addons);
    }

    /**
     * Returns true if the addon is found in the collection.
     *
     * @param AddonTheme $addon the addon
     *
     * @return bool
     */
    public function contains(AddonTheme $addon)
    {
        return in_array($addon, $this->addons, true);
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf(AddonTheme $addon)
    {
        return array_search($addon, $this->addons, true);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->addons[$key] ? $this->addons[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return array_keys($this->addons);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return array_values($this->addons);
    }

    /**
     * Add an Addon with a specified key in the collection.
     *
     * @param mixed $key the key
     * @param AddonTheme $addon the specified addon
     */
    public function set($key, AddonTheme $addon)
    {
        $this->addons[$key] = $addon;
    }

    /**
     * Add an Addon in the collection.
     *
     * @param AddonTheme $addon the specified addon
     *
     * @return bool
     */
    public function add(AddonTheme $addon)
    {
        $this->addons[] = $addon;

        return true;
    }

    /**
     * Remove an addon from the collection by key.
     *
     * @param int|string $key
     *
     * @return bool|null true if the addon has been found and removed
     */
    public function removeByKey($key)
    {
        if (!isset($this->addons[$key]) && !array_key_exists($key, $this->addons)) {
            return null;
        }

        $removed = $this->addons[$key];
        unset($this->addons[$key]);

        return $removed;
    }

    /**
     * Remove an addon from the collection by key.
     *
     * @param AddonTheme $addon the addon to be removed
     *
     * @return bool true if the addon has been found and removed
     */
    public function remove(AddonTheme $addon)
    {
        $key = array_search($addon, $this->addons, true);

        if ($key === false) {
            return false;
        }

        unset($this->addons[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
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
    public function count(): int
    {
        return count($this->addons);
    }
}
