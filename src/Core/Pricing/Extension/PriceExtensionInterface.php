<?php

namespace PrestaShop\PrestaShop\Core\Pricing\Extension;

use PrestaShop\PrestaShop\Core\Pricing\PriceEvent;

interface PriceExtensionInterface
{
    public function getEvents(): array;

    public function onBeforeTax(PriceEvent $event): void;

    public function onAfterTax(PriceEvent $event): void;

    public function onBeforePrice(PriceEvent $event): void;

    public function onAfterPrice(PriceEvent $event): void;

    public function onPriceCalculated(PriceEvent $event): void;
}
