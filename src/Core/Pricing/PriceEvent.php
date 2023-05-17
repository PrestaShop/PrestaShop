<?php

namespace PrestaShop\PrestaShop\Core\Pricing;

class PriceEvent
{
    public function __construct(
        private Price $price,
        private array $context,
    ) {
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setPrice(Price $price): self
    {
        $this->price = $price;

        return $this;
    }
}
