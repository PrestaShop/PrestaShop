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

use PrestaShop\PrestaShop\Core\Domain\Address\QueryResult\ShippingAdressSummary;
use PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult\CarrierSummary;

class ShipmentForViewing
{
    private int $id;
    private ?string $trackingNumber;
    private CarrierSummary $carrierSummary;
    private ShippingAdressSummary $shippingAdressSummary;

    public function __construct(
        int $id,
        ?string $trackingNumber,
        CarrierSummary $carrierSummary,
        ShippingAdressSummary $shippingAdressSummary
    ) {
        $this->id = $id;
        $this->trackingNumber = $trackingNumber;
        $this->carrierSummary = $carrierSummary;
        $this->shippingAdressSummary = $shippingAdressSummary;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(?string $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    public function getCarrierSummary(): CarrierSummary
    {
        return $this->carrierSummary;
    }

    public function setCarrierSummary(CarrierSummary $carrierSummary): void
    {
        $this->carrierSummary = $carrierSummary;
    }

    public function getShippingAdressSummary(): ShippingAdressSummary
    {
        return $this->shippingAdressSummary;
    }

    public function setShippingAdressSummary(ShippingAdressSummary $shippingAdressSummary): void
    {
        $this->shippingAdressSummary = $shippingAdressSummary;
    }
}
