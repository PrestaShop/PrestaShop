<?php

namespace Tests\Unit\Core\Pricing\Extension;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Extension\AbstractPriceExtension;
use PrestaShop\PrestaShop\Core\Pricing\Price;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvent;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvents;

class TestHookReplacesPrice extends AbstractPriceExtension
{
    public function __construct(
        private readonly int $productId,
        private readonly string $price,
    ) {
    }

    public function onPriceCalculated(PriceEvent $event): void
    {
        if ($event->getContext()['productId'] === $this->productId) {
            $event->setPrice(new Price(new DecimalNumber($this->price)));
        }
    }

    public function getEvents(): array
    {
        return [
            PriceEvents::PRICE_CALCULATED,
        ];
    }
}
