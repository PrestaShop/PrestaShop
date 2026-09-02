<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Collection;

use Countable;
use Iterator;
use ReturnTypeWillChange;

/**
 * Class AbstractCollection is responsible for providing base collection implementation.
 */
abstract class AbstractCollection implements Iterator, Countable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * {@inheritdoc}
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return false !== $this->current();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->items);
    }
}
