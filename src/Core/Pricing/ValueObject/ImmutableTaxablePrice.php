<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Immutable TaxablePriceInterface implementation that stores tax-excluded and tax-included
 * as provided, with no auto-sync. Useful when both values have been independently computed
 * (e.g. after rounding) and must not be recomputed from one another.
 */
class ImmutableTaxablePrice implements TaxablePriceInterface
{
    /**
     * Creates an immutable snapshot from any TaxablePriceInterface, freezing its current values.
     */
    public static function fromTaxablePrice(TaxablePriceInterface $price): self
    {
        return new self(
            $price->getTaxExcluded(),
            $price->getTaxIncluded(),
            $price->getTaxAmount(),
            $price->getTaxRate(),
        );
    }

    public function __construct(
        protected readonly DecimalNumber $taxExcluded,
        protected readonly DecimalNumber $taxIncluded,
        protected readonly DecimalNumber $taxAmount,
        protected readonly TaxRate $taxRate,
    ) {
    }

    public function getTaxExcluded(): DecimalNumber
    {
        return $this->taxExcluded;
    }

    public function getTaxIncluded(): DecimalNumber
    {
        return $this->taxIncluded;
    }

    public function getTaxAmount(): DecimalNumber
    {
        return $this->taxAmount;
    }

    public function getTaxRate(): TaxRate
    {
        return $this->taxRate;
    }
}
