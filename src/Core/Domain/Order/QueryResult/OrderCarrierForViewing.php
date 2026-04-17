<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderCarrierForViewing
{
    /**
     * @var int
     */
    private $orderCarrierId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $carrierId;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string|null
     */
    private $trackingUrl;

    /**
     * @var string|null
     */
    private $trackingNumber;

    /**
     * @var bool
     */
    private $canEdit;

    /**
     * @var string
     */
    private $weight;

    /**
     * @param int $orderCarrierId
     * @param DateTimeImmutable $date
     * @param string $name Carrier name or null in case of virtual order
     * @param string $weight
     * @param int $carrierId
     * @param string $price Price or null in case of virtual order
     * @param string|null $trackingUrl
     * @param string|null $trackingNumber
     * @param bool $canEdit
     */
    public function __construct(
        int $orderCarrierId,
        DateTimeImmutable $date,
        ?string $name,
        string $weight,
        int $carrierId,
        ?string $price,
        ?string $trackingUrl,
        ?string $trackingNumber,
        bool $canEdit
    ) {
        $this->orderCarrierId = $orderCarrierId;
        $this->date = $date;
        $this->name = $name;
        $this->carrierId = $carrierId;
        $this->price = $price;
        $this->trackingUrl = $trackingUrl;
        $this->trackingNumber = $trackingNumber;
        $this->canEdit = $canEdit;
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getOrderCarrierId(): int
    {
        return $this->orderCarrierId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    /**
     * @return string
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @return string|null
     */
    public function getTrackingUrl(): ?string
    {
        return $this->trackingUrl;
    }

    /**
     * @return string|null
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * @return bool
     */
    public function canEdit(): bool
    {
        return $this->canEdit;
    }

    /**
     * @return string
     */
    public function getWeight(): string
    {
        return $this->weight;
    }
}
