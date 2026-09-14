<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Context;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;

interface ProductQuantityDiscountPreparationInterface
{
    public function getProductCalculator(): ProductCalculatorInterface;

    /**
     * @param array<int, array<string, mixed>> $specificPrices
     *
     * @return array<int, array<string, mixed>>
     */
    public function formatQuantityDiscounts(
        array $specificPrices,
        float $price,
        float $taxRate,
        float $ecotaxAmount,
        Context $context,
    ): array;

    public function isNewPricingEnabled(): bool;
}
