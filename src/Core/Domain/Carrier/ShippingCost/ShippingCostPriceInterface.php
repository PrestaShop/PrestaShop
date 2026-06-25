<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierShippingData;

/**
 * Mutable DTO carrying all state through the shipping cost calculation pipeline.
 */
interface ShippingCostPriceInterface
{
    public function getPhysicalProducts(): array;

    public function getCarrierId(): int;

    public function getAddressId(): ?int;

    public function getCurrencyId(): int;

    public function getOrderTotal(): DecimalNumber;

    public function getCountryZoneId(): int;

    public function getTotalWeight(): DecimalNumber;

    public function setTotalWeight(DecimalNumber $totalWeight): void;

    public function getResolvedZoneId(): ?int;

    public function setResolvedZoneId(int $zoneId): void;

    public function getCarrierData(): ?CarrierShippingData;

    public function setCarrierData(CarrierShippingData $carrierData): void;

    public function isFreeShipping(): bool;

    public function setFreeShipping(bool $isFreeShipping): void;

    public function getCost(): DecimalNumber;

    public function setCost(DecimalNumber $cost): void;

    public function isAvailable(): bool;

    public function setAvailable(bool $isAvailable): void;

    public function getTaxExcluded(): ?DecimalNumber;

    public function setTaxExcluded(DecimalNumber $taxExcluded): void;

    public function getTaxIncluded(): ?DecimalNumber;

    public function setTaxIncluded(DecimalNumber $taxIncluded): void;

    public function getPrecision(): ?int;

    public function setPrecision(int $precision): void;
}
