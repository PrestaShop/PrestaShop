<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Closure;
use Context;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;

/**
 * Adapts ProductController legacy quantity discount extension points without duplicating algorithms.
 *
 * @internal
 */
final class ProductQuantityDiscountPreparationControllerAdapter implements ProductQuantityDiscountPreparationInterface
{
    public function __construct(
        private readonly Closure $getProductCalculator,
        private readonly Closure $formatQuantityDiscounts,
        private readonly Closure $isNewPricingEnabled,
    ) {
    }

    public function getProductCalculator(): ProductCalculatorInterface
    {
        return ($this->getProductCalculator)();
    }

    public function formatQuantityDiscounts(
        array $specificPrices,
        float $price,
        float $taxRate,
        float $ecotaxAmount,
        Context $context,
    ): array {
        return ($this->formatQuantityDiscounts)($specificPrices, $price, $taxRate, $ecotaxAmount, $context);
    }

    public function isNewPricingEnabled(): bool
    {
        return ($this->isNewPricingEnabled)();
    }
}
