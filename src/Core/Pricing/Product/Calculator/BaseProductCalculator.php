<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Calculator;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\Provider\ProductProviderInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\ImmutableTaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

/**
 * First calculator in the pipeline: fetches raw pricing data from the provider
 * and computes originalPrice (price + combination impact) and unitPrice (unit_price + combination impact).
 * Initializes finalPrice with the same value as originalPrice before discounts are applied.
 */
class BaseProductCalculator implements ProductCalculatorInterface
{
    public function __construct(
        protected readonly ProductProviderInterface $productProvider,
    ) {
    }

    public function compute(ProductPriceInterface $productPrice): void
    {
        $priceData = $this->productProvider->getProductPriceData(
            $productPrice->getProductId(),
            $productPrice->getCombinationId()
        );

        $originalPrice = TaxablePrice::fromTaxExcluded(
            $priceData->getPriceTaxExcluded()->plus($priceData->getCombinationImpactTaxExcluded()),
            TaxRate::zero()
        );

        $productPrice->setOriginalPrice($originalPrice);
        $productPrice->setUnitPrice(TaxablePrice::fromTaxExcluded(
            $priceData->getUnitPriceTaxExcluded()->plus($priceData->getCombinationUnitPriceImpactTaxExcluded()),
            TaxRate::zero()
        ));
        $productPrice->setFinalPrice(ImmutableTaxablePrice::fromTaxablePrice($originalPrice));
    }
}
