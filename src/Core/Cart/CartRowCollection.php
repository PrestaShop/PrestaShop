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

namespace PrestaShop\PrestaShop\Core\Cart;

class CartRowCollection implements \Iterator, \Countable
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
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->cartRows[$this->getKey($this->iteratorPosition)];
    }

    #[\ReturnTypeWillChange]
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
