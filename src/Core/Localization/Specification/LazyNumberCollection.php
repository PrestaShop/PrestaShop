<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Localization\Specification;

use ArrayIterator;
use Closure;
use Traversable;

/**
 * Number specifications collection whose items are built on first access.
 */
final class LazyNumberCollection extends NumberCollection
{
    private Closure $builder;

    /**
     * @param string[] $indexes Indexes of the items that can be built
     * @param Closure $builder Receives an index and returns the matching NumberInterface item
     */
    public function __construct(array $indexes, Closure $builder)
    {
        $this->numberSpecifications = array_fill_keys($indexes, null); // @phpstan-ignore assign.propertyType
        $this->builder = $builder;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }

    public function count(): int
    {
        return count($this->all());
    }

    public function all()
    {
        foreach (array_keys($this->numberSpecifications) as $index) {
            $this->get($index);
        }

        return parent::all();
    }

    public function get($index)
    {
        // @phpstan-ignore identical.alwaysFalse
        if (array_key_exists($index, $this->numberSpecifications) && null === $this->numberSpecifications[$index]) {
            $this->numberSpecifications[$index] = ($this->builder)($index);
        }

        return parent::get($index);
    }
}
