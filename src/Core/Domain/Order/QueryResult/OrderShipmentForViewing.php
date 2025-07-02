<?php

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderShipmentForViewing
{
    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $carrier;

    /**
     * @var int
     */
    private $items;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $weight;

    /**
     * @var string|null
     */
    private $trackingNumber;

    /**
     * @param DateTimeImmutable $date
     * @param int $number
     * @param string $carrier
     * @param int $items
     * @param float $price
     * @param string $weight
     * @param string|null $trackingNumber
     */
    public function __construct(
        DateTimeImmutable $date,
        int $number,
        string $carrier,
        int $items,
        float $price,
        string $weight,
        ?string $trackingNumber
    ) {
        $this->date = $date;
        $this->number = $number;
        $this->carrier = $carrier;
        $this->items = $items;
        $this->price = $price;
        $this->weight = $weight;
        $this->trackingNumber = $trackingNumber;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getCarrier(): string
    {
        return $this->carrier;
    }

    public function getItems(): int
    {
        return $this->items;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getWeight(): string
    {
        return $this->weight;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }
}
