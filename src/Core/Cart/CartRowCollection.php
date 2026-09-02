<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Countable;
use Iterator;
use ReturnTypeWillChange;

class CartRowCollection implements Iterator, Countable
{
    /**
     * @var CartRow[]
     */
    protected $cartRows = [];
    protected $iteratorPosition = 0;

    public function addCartRow(CartRow $cartRow)
    {
        $this->cartRows[] = $cartRow;
    }

    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    /**
     * @return CartRow
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->cartRows[$this->getKey($this->iteratorPosition)];
    }

    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->getKey($this->iteratorPosition);
    }

    public function next(): void
    {
        ++$this->iteratorPosition;
    }

    public function valid(): bool
    {
        return $this->getKey($this->iteratorPosition) !== null
               && array_key_exists(
                   $this->getKey($this->iteratorPosition),
                   $this->cartRows
               );
    }

    protected function getKey($iteratorPosition)
    {
        $keys = array_keys($this->cartRows);
        if (!isset($keys[$iteratorPosition])) {
            return null;
        } else {
            return $keys[$iteratorPosition];
        }
    }

    public function count(): int
    {
        return count($this->cartRows);
    }

    /**
     * return product data as array.
     *
     * @return array
     */
    public function getProducts()
    {
        $products = [];
        foreach ($this->cartRows as $cartRow) {
            $products[] = $cartRow->getRowData();
        }

        return $products;
    }
}
