<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Calculator;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingServiceInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\ImmutableTaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePriceInterface;

/**
 * Last calculator in the pipeline: rounds the finalPrice only.
 * originalPrice, unitPrice and discountPrice keep their full precision values.
 * This is the only place where rounding occurs.
 */
class RoundingCalculator implements ProductCalculatorInterface
{
    public function __construct(
        protected readonly RoundingServiceInterface $roundingService,
    ) {
    }

    public function compute(ProductPriceInterface $productPrice): void
    {
        $productPrice->setFinalPrice($this->roundPrice($productPrice->getFinalPrice()));
    }

    protected function roundPrice(TaxablePriceInterface $price): ImmutableTaxablePrice
    {
        $roundedExcl = $this->roundingService->round($price->getTaxExcluded());
        $roundedIncl = $this->roundingService->round($price->getTaxIncluded());

        return new ImmutableTaxablePrice(
            $roundedExcl,
            $roundedIncl,
            $roundedIncl->minus($roundedExcl),
            $price->getTaxRate(),
        );
    }
}
