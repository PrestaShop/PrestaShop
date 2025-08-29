<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
