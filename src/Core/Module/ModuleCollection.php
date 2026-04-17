<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use PrestaShopException;
use ReturnTypeWillChange;
use Throwable;
use Traversable;

/**
 * This class wrap an array of ModuleInterface
 */
class ModuleCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var ModuleInterface[] */
    private $modules = [];

    private $errors = [];

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
    #[ReturnTypeWillChange]
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

    public function add(ModuleInterface $module): void
    {
        $this->modules[] = $module;
    }

    public function addError(Throwable $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
