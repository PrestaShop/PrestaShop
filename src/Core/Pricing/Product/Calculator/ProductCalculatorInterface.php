<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Calculator;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;

/**
 * A single step in the product pricing pipeline. Each implementation mutates the
 * ProductPrice DTO in place and returns early when not relevant.
 */
interface ProductCalculatorInterface
{
    public function compute(ProductPriceInterface $productPrice): void;
}
