<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Calculator;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;

/**
 * Main entry point for computing a product price. Implements ProductCalculatorInterface
 * like any other calculator step, but internally delegates to a priority-sorted pipeline
 * of sub-calculators. This is an implementation detail — callers simply call compute().
 */
class ProductCalculator implements ProductCalculatorInterface
{
    /**
     * @param iterable<ProductCalculatorInterface> $calculators Tagged iterator, priority-sorted
     */
    public function __construct(
        protected readonly iterable $calculators,
    ) {
    }

    public function compute(ProductPriceInterface $productPrice): void
    {
        foreach ($this->calculators as $calculator) {
            $calculator->compute($productPrice);
        }
    }
}
