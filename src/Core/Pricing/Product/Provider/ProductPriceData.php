<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Provider;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Raw pricing data fetched from the database for a product and optionally its combination.
 * Contains only the values as stored — no computation is performed here.
 */
class ProductPriceData
{
    public function __construct(
        protected readonly DecimalNumber $price,
        protected readonly DecimalNumber $unitPrice,
        protected readonly DecimalNumber $combinationImpact,
        protected readonly DecimalNumber $combinationUnitPriceImpact,
    ) {
    }

    /**
     * ps_product.price — the base catalog price.
     */
    public function getPriceTaxExcluded(): DecimalNumber
    {
        return $this->price;
    }

    /**
     * ps_product.unit_price — the base unit price.
     */
    public function getUnitPriceTaxExcluded(): DecimalNumber
    {
        return $this->unitPrice;
    }

    /**
     * ps_product_attribute.price — the combination impact on catalog price (0 when no combination).
     */
    public function getCombinationImpactTaxExcluded(): DecimalNumber
    {
        return $this->combinationImpact;
    }

    /**
     * ps_product_attribute.unit_price_impact — the combination unit price impact (0 when no combination).
     */
    public function getCombinationUnitPriceImpactTaxExcluded(): DecimalNumber
    {
        return $this->combinationUnitPriceImpact;
    }
}
