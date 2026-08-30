<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Per-module value bag inside an ExtraPropertiesBag.
 *
 * Keys are field names (property_name from the definition). Writes are tracked
 * for persistence; getModifiedValues() returns the dirty [propertyName => value]
 * map — scope routing and storage column resolution happen inside the writer.
 *
 * Usage (via parent ExtraPropertiesBag):
 *   $product->extra_properties['demoextrafield']['is_dangerous']       // read
 *   $product->extra_properties['demoextrafield']['is_dangerous'] = 1   // write + mark dirty
 */
final class ModuleFieldsBag implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    /** @var array<string, mixed> */
    private array $values;
    /** @var array<string, mixed> */
    private array $modifiedFields = [];

    /**
     * @param array<string, mixed> $initialValues
     */
    public function __construct(array $initialValues = [])
    {
        $this->values = $initialValues;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->values);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->values[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->values[$offset] = $value;
        $this->modifiedFields[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->values[$offset]);
        $this->modifiedFields[$offset] = null;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }

    /**
     * @return array<string, mixed> [propertyName => value]
     */
    public function jsonSerialize(): array
    {
        return $this->values;
    }

    public function hasModifications(): bool
    {
        return !empty($this->modifiedFields);
    }

    /**
     * @return array<string, mixed> [propertyName => value] map of dirty fields
     */
    public function getModifiedValues(): array
    {
        return $this->modifiedFields;
    }
}
