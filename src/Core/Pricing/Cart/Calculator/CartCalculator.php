<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator;

use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;

/**
 * Main entry point for computing a cart price. Implements CartCalculatorInterface
 * like any other calculator step, but internally delegates to a priority-sorted pipeline
 * of sub-calculators. This is an implementation detail — callers simply call compute().
 *
 * @experimental
 */
class CartCalculator implements CartCalculatorInterface
{
    /**
     * @param iterable<CartCalculatorInterface> $calculators Tagged iterator, priority-sorted
     */
    public function __construct(
        protected readonly iterable $calculators,
    ) {
    }

    public function compute(CartPriceInterface $cartPrice): void
    {
        foreach ($this->calculators as $calculator) {
            $calculator->compute($cartPrice);
        }
    }
}
