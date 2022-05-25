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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShopException;
use Traversable;

/**
 * This class wrap an array of ModuleInterface
 */
class ModuleCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var ModuleInterface[] */
    private $modules = [];

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
     * @param ModuleInterface[] $modules
     *
     * @return ModuleCollection
     */
    public static function createFrom(array $modules): ModuleCollection
    {
        return new static($modules);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->modules);
    }

    public function count(): int
    {
        return count($this->modules);
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->modules);
    }

    /**
     * @param mixed $offset
     *
     * @return ModuleInterface|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->modules[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof ModuleInterface) {
            throw new PrestaShopException(
                sprintf('%s only accept %s elements.', self::class, ModuleInterface::class)
            );
        }
        $this->modules[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->modules[$offset]);
        }
    }

    public function filter(callable $callable): ModuleCollection
    {
        return static::createFrom(array_filter($this->modules, $callable));
    }
}
