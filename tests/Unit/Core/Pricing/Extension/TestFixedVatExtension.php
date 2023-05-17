<?php

namespace Tests\Unit\Core\Pricing\Extension;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Extension\AbstractPriceExtension;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvent;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvents;
use PrestaShop\PrestaShop\Core\Pricing\TaxRate;

class TestFixedVatExtension extends AbstractPriceExtension
{
    public function __construct(
        private readonly string $rate,
    ) {
    }

    public function onBeforeTax(PriceEvent $event): void
    {
        $event->getPrice()->applyTaxRate(
            new TaxRate(new DecimalNumber($this->rate))
        );
    }

    public function getEvents(): array
    {
        return [
            PriceEvents::BEFORE_TAX,
        ];
    }
}
