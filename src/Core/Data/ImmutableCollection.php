<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Data;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template T
 *
 * @template-implements  IteratorAggregate<T>
 */
abstract class ImmutableCollection implements IteratorAggregate, Countable
{
    /** @var T[] */
    protected $values;

    /**
     * @param T[] $values
     *
     * Keep the constructor protected to keep immutability, the subclasses should not change this constructor
     * and rely on a static factory method for their construction:
     *
     *   public static function from(T ...$values): static
     */
    protected function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return ArrayIterator<string|int, T>|T[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }

    public function count(): int
    {
        return count($this->values);
    }

    /**
     * @return T
     */
    public function first()
    {
        return reset($this->values);
    }

    /**
     * @return static
     */
    public function filter(callable $callback): self
    {
        return new static(array_filter($this->values, $callback));
    }

    public function isEmpty(): bool
    {
        return empty($this->values);
    }
}
