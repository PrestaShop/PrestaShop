<?php

namespace PrestaShop\PrestaShop\Core\Pricing;

use PrestaShop\Decimal\DecimalNumber;

class Price
{
    /**
     * @var DecimalNumber
     */
    private $amountWithoutTax;

    /**
     * @var TaxAmount|null
     */
    private $taxAmount;

    public function __construct(
        DecimalNumber $amountWithoutTax,
        TaxAmount $taxAmount = null
    ) {
        $this->amountWithoutTax = $amountWithoutTax;
        $this->taxAmount = $taxAmount;
    }

    public function getTotal(): DecimalNumber
    {
        if (null === $this->taxAmount) {
            // we clone the amount without tax to avoid having mutable objects.
            return clone $this->getAmountWithoutTax();
        }

        return $this->getAmountWithoutTax()->plus($this->taxAmount->getAmount());
    }

    public function applyTaxRate(TaxRate $rate): self
    {
        $this->taxAmount = new TaxAmount(
            $this->getAmountWithoutTax()->times($rate->getRate()),
            $rate
        );

        return $this;
    }

    public function getAmountWithoutTax(): DecimalNumber
    {
        return $this->amountWithoutTax;
    }

    public function getTaxAmount(): ?TaxAmount
    {
        return $this->taxAmount;
    }

    public function hasTax(): bool
    {
        return null !== $this->taxAmount;
    }
}
