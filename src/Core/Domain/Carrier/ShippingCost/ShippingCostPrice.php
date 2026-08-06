<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierShippingData;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

/**
 * Mutable DTO carrying all state through the shipping cost calculation pipeline.
 * Populated incrementally by each calculator in the chain.
 */
final class ShippingCostPrice implements ShippingCostPriceInterface
{
    private DecimalNumber $totalWeight;
    private ?int $resolvedZoneId = null;
    private ?CarrierShippingData $carrierData = null;
    private bool $isFreeShipping = false;
    private bool $isAvailable = true;
    private DecimalNumber $cost;
    private ?DecimalNumber $taxExcluded = null;
    private ?DecimalNumber $taxIncluded = null;
    private ?int $precision = null;

    /**
     * @param array<array{
     *     id_product: int,
     *     id_product_attribute: int,
     *     quantity: int,
     *     weight: float,
     *     weight_attribute: float|null,
     *     is_virtual: bool,
     *     additional_shipping_cost: float,
     *     price_wt: float
     * }> $physicalProducts
     */
    private function __construct(
        private readonly array $physicalProducts,
        private readonly int $carrierId,
        private readonly ?int $addressId,
        private readonly int $currencyId,
        private readonly DecimalNumber $orderTotal,
        private readonly int $countryZoneId,
    ) {
        $this->totalWeight = new DecimalNumber('0');
        $this->cost = new DecimalNumber('0');
    }

    public static function createFromRequest(ShippingCalculationRequest $request): self
    {
        $physicalProducts = array_values(
            array_filter($request->getProducts(), static fn (array $p): bool => $p['is_virtual'] === false)
        );

        $ctx = new self(
            $physicalProducts,
            $request->getCarrierId(),
            $request->getAddressId(),
            $request->getCurrencyId(),
            new DecimalNumber((string) $request->getOrderTotal()),
            $request->getCountryZoneId(),
        );

        if ($request->getZoneId() !== null) {
            $ctx->setResolvedZoneId($request->getZoneId());
        }

        return $ctx;
    }

    /**
     * @return array<array{
     *     id_product: int,
     *     id_product_attribute: int,
     *     quantity: int,
     *     weight: float,
     *     weight_attribute: float|null,
     *     is_virtual: bool,
     *     additional_shipping_cost: float,
     *     price_wt: float
     * }>
     */
    public function getPhysicalProducts(): array
    {
        return $this->physicalProducts;
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getAddressId(): ?int
    {
        return $this->addressId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getOrderTotal(): DecimalNumber
    {
        return $this->orderTotal;
    }

    public function getCountryZoneId(): int
    {
        return $this->countryZoneId;
    }

    public function getTotalWeight(): DecimalNumber
    {
        return $this->totalWeight;
    }

    public function setTotalWeight(DecimalNumber $totalWeight): void
    {
        $this->totalWeight = $totalWeight;
    }

    public function getResolvedZoneId(): ?int
    {
        return $this->resolvedZoneId;
    }

    public function setResolvedZoneId(int $zoneId): void
    {
        $this->resolvedZoneId = $zoneId;
    }

    public function getCarrierData(): ?CarrierShippingData
    {
        return $this->carrierData;
    }

    public function setCarrierData(CarrierShippingData $carrierData): void
    {
        $this->carrierData = $carrierData;
    }

    public function isFreeShipping(): bool
    {
        return $this->isFreeShipping;
    }

    public function setFreeShipping(bool $isFreeShipping): void
    {
        $this->isFreeShipping = $isFreeShipping;
    }

    public function getCost(): DecimalNumber
    {
        return $this->cost;
    }

    public function setCost(DecimalNumber $cost): void
    {
        $this->cost = $cost;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $isAvailable): void
    {
        $this->isAvailable = $isAvailable;
    }

    public function getTaxExcluded(): ?DecimalNumber
    {
        return $this->taxExcluded;
    }

    public function setTaxExcluded(DecimalNumber $taxExcluded): void
    {
        $this->taxExcluded = $taxExcluded;
    }

    public function getTaxIncluded(): ?DecimalNumber
    {
        return $this->taxIncluded;
    }

    public function setTaxIncluded(DecimalNumber $taxIncluded): void
    {
        $this->taxIncluded = $taxIncluded;
    }

    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    public function setPrecision(int $precision): void
    {
        $this->precision = $precision;
    }
}
