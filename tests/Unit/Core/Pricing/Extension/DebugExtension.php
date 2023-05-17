<?php

namespace Tests\Unit\Core\Pricing\Extension;

use PrestaShop\PrestaShop\Core\Pricing\Extension\AbstractPriceExtension;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvent;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvents;

class DebugExtension extends AbstractPriceExtension
{
    public function onBeforeTax(PriceEvent $event): void
    {
        dump($event);
    }

    public function onAfterTax(PriceEvent $event): void
    {
        dump($event);
    }

    public function onBeforePrice(PriceEvent $event): void
    {
        dump($event);
    }

    public function onAfterPrice(PriceEvent $event): void
    {
        dump($event);
    }

    public function getEvents(): array
    {
        return [
            PriceEvents::BEFORE_PRICE,
            PriceEvents::AFTER_PRICE,
            PriceEvents::BEFORE_TAX,
            PriceEvents::AFTER_TAX,
        ];
    }
}
