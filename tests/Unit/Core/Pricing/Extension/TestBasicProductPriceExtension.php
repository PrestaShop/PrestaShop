<?php

namespace Tests\Unit\Core\Pricing\Extension;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Extension\AbstractPriceExtension;
use PrestaShop\PrestaShop\Core\Pricing\Price;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvent;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvents;

class TestBasicProductPriceExtension extends AbstractPriceExtension
{
    public function __construct(
        private readonly array $prices,
    ) {
    }

    public function onBeforePrice(PriceEvent $event): void
    {
        $context = $event->getContext();
        $event->setPrice(new Price(new DecimalNumber($this->prices[$context['productId']])));
    }

    public function getEvents(): array
    {
        return [
            PriceEvents::BEFORE_PRICE,
        ];
    }
}
