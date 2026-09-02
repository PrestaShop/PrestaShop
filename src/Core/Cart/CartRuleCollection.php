<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Cart;

use Iterator;
use ReturnTypeWillChange;

class CartRuleCollection implements Iterator
{
    /**
     * @var CartRuleData[]
     */
    protected $cartRules = [];
    protected $iteratorPosition = 0;

    public function addCartRule(CartRuleData $cartRule)
    {
        $this->cartRules[] = $cartRule;
    }

    public function rewind(): void
    {
        $this->iteratorPosition = 0;
    }

    /**
     * @return CartRuleData
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->cartRules[$this->getKey($this->iteratorPosition)];
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
                   $this->cartRules
               );
    }

    protected function getKey($iteratorPosition)
    {
        $keys = array_keys($this->cartRules);
        if (!isset($keys[$iteratorPosition])) {
            return null;
        } else {
            return $keys[$iteratorPosition];
        }
    }
}
