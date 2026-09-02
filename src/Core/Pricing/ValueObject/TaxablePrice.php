<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\PricingConstants;

/**
 * Mutable price that automatically keeps tax-excluded, tax-included and tax-amount in sync
 * through its associated TaxRate.
 */
class TaxablePrice implements TaxablePriceInterface
{
    protected DecimalNumber $taxExcluded;
    protected DecimalNumber $taxIncluded;
    protected DecimalNumber $taxAmount;
    protected TaxRate $taxRate;

    protected function __construct(DecimalNumber $taxExcluded, TaxRate $taxRate)
    {
        $this->taxExcluded = $taxExcluded;
        $this->taxRate = $taxRate;
        $this->syncFromTaxExcluded();
    }

    /**
     * Builds from a tax-excluded value: derives taxIncluded from taxExcluded * taxRate multiplier.
     */
    public static function fromTaxExcluded(DecimalNumber $taxExcluded, TaxRate $taxRate): self
    {
        return new self($taxExcluded, $taxRate);
    }

    /**
     * Builds from a tax-included value: derives taxExcluded from taxIncluded / taxRate multiplier.
     */
    public static function fromTaxIncluded(DecimalNumber $taxIncluded, TaxRate $taxRate): self
    {
        $taxExcluded = $taxIncluded->dividedBy($taxRate->getMultiplier(), PricingConstants::INTERMEDIATE_PRECISION);
        $instance = new self($taxExcluded, $taxRate);
        // Override taxIncluded with the exact value provided
        $instance->taxIncluded = $taxIncluded;
        $instance->taxAmount = $taxIncluded->minus($taxExcluded);

        return $instance;
    }

    public static function zero(): self
    {
        return new self(new DecimalNumber('0'), TaxRate::zero());
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

    /**
     * Sets tax-excluded and recomputes tax-included and tax amount.
     */
    public function setTaxExcluded(DecimalNumber $taxExcluded): void
    {
        $this->taxExcluded = $taxExcluded;
        $this->syncFromTaxExcluded();
    }

    /**
     * Sets tax-included and recomputes tax-excluded and tax amount.
     */
    public function setTaxIncluded(DecimalNumber $taxIncluded): void
    {
        $this->taxIncluded = $taxIncluded;
        $this->taxExcluded = $taxIncluded->dividedBy($this->taxRate->getMultiplier(), PricingConstants::INTERMEDIATE_PRECISION);
        $this->taxAmount = $taxIncluded->minus($this->taxExcluded);
    }

    /**
     * Sets the tax rate and recomputes tax-included and tax amount from tax-excluded (source of truth).
     */
    public function setTaxRate(TaxRate $taxRate): void
    {
        $this->taxRate = $taxRate;
        $this->syncFromTaxExcluded();
    }

    /**
     * Recomputes taxIncluded and taxAmount from taxExcluded (source of truth).
     */
    protected function syncFromTaxExcluded(): void
    {
        $this->taxIncluded = $this->taxExcluded->times($this->taxRate->getMultiplier());
        $this->taxAmount = $this->taxIncluded->minus($this->taxExcluded);
    }
}
