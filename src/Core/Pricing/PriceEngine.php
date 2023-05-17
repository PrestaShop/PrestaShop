<?php

namespace PrestaShop\PrestaShop\Core\Pricing;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Extension\PriceExtensionInterface;

class PriceEngine
{
    private array $listeners = [];

    public function __construct(
        private array $extensions,
    ) {
        foreach ($this->extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    public function getPrice(int $productId, array $context = []): Price
    {
        $quantity = 1; // todo, find what do to with this. Is this should live inside the context or as an argument for example.

        $context += [
            'productId' => $productId,
        ];

        $context['quantity'] = $context['quantity'] ?? $quantity;

        $priceTaxExcluded = new Price(new DecimalNumber('0'));
        $priceEvent = new PriceEvent($priceTaxExcluded, $context);

        $this->dispatchEvent($priceEvent, PriceEvents::BEFORE_PRICE);

        $totalUnitPrice = $priceEvent->getPrice()->getTotal()->times(new DecimalNumber((string) $quantity));

        $priceEvent = new PriceEvent(
            new Price($totalUnitPrice), $context
        );

        $this->dispatchEvent($priceEvent, PriceEvents::AFTER_PRICE);
        $this->dispatchEvent($priceEvent, PriceEvents::BEFORE_TAX);
        $this->dispatchEvent($priceEvent, PriceEvents::AFTER_TAX);
        $this->dispatchEvent($priceEvent, PriceEvents::PRICE_CALCULATED);

        return $priceEvent->getPrice();
    }

    public function addExtension(PriceExtensionInterface $extension): self
    {
        $fqcn = get_class($extension);
        $this->extensions[get_class($extension)] = $extension;

        foreach ($extension->getEvents() as $event) {
            $this->listeners[$event][] = $fqcn;
        }

        return $this;
    }

    private function dispatchEvent(PriceEvent $event, string $eventName): void
    {
        if (!array_key_exists($eventName, $this->listeners)) {
            return;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            $this->extensions[$listener]->{$eventName}($event);
        }
    }
}
