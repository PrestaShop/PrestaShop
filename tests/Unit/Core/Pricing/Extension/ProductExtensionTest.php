<?php

namespace Tests\Unit\Core\Pricing\Extension;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Extension\AbstractPriceExtension;
use PrestaShop\PrestaShop\Core\Pricing\Price;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvent;
use PrestaShop\PrestaShop\Core\Pricing\PriceEvents;

class ProductExtensionTest extends AbstractPriceExtension
{
    public function __construct(
        private array $productCombinationAssociation,
        private array $combinationPrices
    ) {
    }

    public function onBeforePrice(PriceEvent $event): void
    {
        $context = $event->getContext();
        $combinationId = $this->productCombinationAssociation[$context['productId']];

        $event->setPrice(new Price(new DecimalNumber($this->combinationPrices[$combinationId])));
    }

    public function getEvents(): array
    {
        return [
            PriceEvents::BEFORE_PRICE,
        ];
    }
}
