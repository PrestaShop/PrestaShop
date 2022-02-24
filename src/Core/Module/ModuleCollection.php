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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Module;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShopException;
use Traversable;

/**
 * An ArrayCollection is a Collection implementation that wraps a regular PHP array.
 */
class ModuleCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * An array containing the modules of this collection.
     *
     * @var ModuleInterface[]
     */
    private $modules = [];

    /**
     * Initializes a new ModuleCollection.
     *
     * @param ModuleInterface[] $modules
     *
     * @throws PrestaShopException
     */
    public function __construct(array $modules = [])
    {
        foreach ($modules as $module) {
            if (!$module instanceof ModuleInterface) {
                throw new PrestaShopException(
                    sprintf('%s only accept %s elements.', self::class, ModuleInterface::class)
                );
            }
            $this->modules[] = $module;
        }
    }

    /**
     * Creates a new instance from the specified elements.
     *
     * This method is provided for derived classes to specify how a new
     * instance should be created when constructor semantics have changed.
     *
     * @param array $modules elements
     *
     * @return static
     */
    public static function createFrom(array $modules): ModuleCollection
    {
        return new static($modules);
    }

    /**
     * Gets a native PHP array representation of the collection.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->modules;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->modules);
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
    public function offsetGet($offset): ?ModuleInterface
    {
        return $this->get($offset);
    }

    /**
     * Required by ArrayAccess interface.
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $module): void
    {
        if (!isset($offset)) {
            $this->add($module);

            return;
        }

        $this->set($offset, $module);
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
    public function containsKey($key): bool
    {
        return isset($this->modules[$key]) || array_key_exists($key, $this->modules);
    }

    /**
     * Returns true if the module is found in the collection.
     *
     * @param ModuleInterface $module the module
     *
     * @return bool
     */
    public function contains(ModuleInterface $module): bool
    {
        return in_array($module, $this->modules, true);
    }

    /**
     * {@inheritdoc}
     */
    public function indexOf(ModuleInterface $module)
    {
        return array_search($module, $this->modules, true);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key): ?ModuleInterface
    {
        return $this->modules[$key] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys(): array
    {
        return array_keys($this->modules);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): array
    {
        return array_values($this->modules);
    }

    /**
     * Add a module with a specified key in the collection.
     *
     * @param mixed $key the key
     * @param ModuleInterface $module the specified module
     */
    public function set($key, ModuleInterface $module): void
    {
        $this->modules[$key] = $module;
    }

    /**
     * Add a Module in the collection.
     *
     * @param ModuleInterface $module the specified module
     *
     * @return bool
     */
    public function add(ModuleInterface $module): bool
    {
        $this->modules[] = $module;

        return true;
    }

    /**
     * Remove a module from the collection by key.
     *
     * @param int|string $key
     *
     * @return ModuleInterface|null returns the removed module or null if not found
     */
    public function removeByKey($key): ?ModuleInterface
    {
        if (!isset($this->modules[$key])) {
            return null;
        }

        $removed = $this->modules[$key];
        unset($this->modules[$key]);

        return $removed;
    }

    /**
     * Remove a module from the collection.
     *
     * @param ModuleInterface $module the module to be removed
     *
     * @return bool true if the module has been found and removed
     */
    public function remove(ModuleInterface $module): bool
    {
        $key = array_search($module, $this->modules, true);

        if ($key === false) {
            return false;
        }

        unset($this->modules[$key]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->modules);
    }

    /**
     * Gets the sum of modules of the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->modules);
    }
}
