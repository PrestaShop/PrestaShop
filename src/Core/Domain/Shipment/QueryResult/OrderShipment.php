<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult;

use DateTime;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;

class OrderShipment
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var int
     */
    private int $orderId;

    /**
     * @var CarrierSummary
     */
    private CarrierSummary $carrierSummary;

    /**
     * @var int
     */
    private int $addressId;

    /**
     * @var DecimalNumber
     */
    private DecimalNumber $shippingCostTaxExcluded;

    /**
     * @var DecimalNumber
     */
    private DecimalNumber $shippingCostTaxIncluded;

    /**
     * @var string
     */
    private ?string $trackingNumber;

    /**
     * @var DateTime
     */
    private ?DateTime $shippedAt;

    /**
     * @var DateTime
     */
    private ?DateTime $deliveredAt;

    /**
     * @var DateTime
     */
    private ?DateTime $cancelledAt;

    /**
     * @var int
     */
    private int $productsCount;

    public function __construct(
        int $id,
        int $orderId,
        CarrierSummary $carrierSummary,
        int $addressId,
        DecimalNumber $shippingCostTaxExcluded,
        DecimalNumber $shippingCostTaxIncluded,
        int $productsCount,
        ?string $trackingNumber,
        ?DateTime $shippedAt,
        ?DateTime $deliveredAt,
        ?DateTime $cancelledAt
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->carrierSummary = $carrierSummary;
        $this->addressId = $addressId;
        $this->shippingCostTaxExcluded = $shippingCostTaxExcluded;
        $this->shippingCostTaxIncluded = $shippingCostTaxIncluded;
        $this->productsCount = $productsCount;
        $this->trackingNumber = $trackingNumber;
        $this->shippedAt = $shippedAt;
        $this->deliveredAt = $deliveredAt;
        $this->cancelledAt = $cancelledAt;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return CarrierSummary
     */
    public function getCarrierSummary(): CarrierSummary
    {
        return $this->carrierSummary;
    }

    /**
     * @return int
     */
    public function getAddressId(): int
    {
        return $this->addressId;
    }

    /**
     * @return DecimalNumber
     */
    public function getShippingCostTaxExcluded(): DecimalNumber
    {
        return $this->shippingCostTaxExcluded;
    }

    /**
     * @return DecimalNumber
     */
    public function getShippingCostTaxIncluded(): DecimalNumber
    {
        return $this->shippingCostTaxIncluded;
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * @return DateTime
     */
    public function getShippedAt(): ?DateTime
    {
        return $this->shippedAt;
    }

    /**
     * @return DateTime
     */
    public function getDeliveredAt(): ?DateTime
    {
        return $this->deliveredAt;
    }

    /**
     * @return DateTime
     */
    public function getCancelledAt(): ?DateTime
    {
        return $this->cancelledAt;
    }

    /**
     * @return int
     */
    public function getProductsCount(): int
    {
        return $this->productsCount;
    }
}
