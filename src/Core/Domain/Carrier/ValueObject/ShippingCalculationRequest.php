<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use InvalidArgumentException;

final class ShippingCalculationRequest
{
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
     * }> $products Array of product data for shipping calculation
     * @param int $carrierId Carrier ID
     * @param int|null $zoneId Zone ID (optional, will be resolved from address or country)
     * @param int|null $addressId Delivery address ID
     * @param int $countryZoneId Country's default zone ID (fallback)
     * @param int $currencyId Currency ID
     * @param int|null $customerId Customer ID
     * @param float $orderTotal Total order amount
     *
     * @throws InvalidArgumentException If products array is invalid or missing required fields
     */
    public function __construct(
        private readonly array $products,
        private readonly int $carrierId,
        private readonly ?int $zoneId,
        private readonly ?int $addressId,
        private readonly int $countryZoneId,
        private readonly int $currencyId,
        private readonly ?int $customerId,
        private readonly float $orderTotal,
    ) {
        foreach ($products as $product) {
            $this->validateProduct($product);
        }
    }

    /**
     * @param array{
     *     id_product: int,
     *     quantity: int,
     *     is_virtual: bool
     * } $product
     *
     * @throws InvalidArgumentException
     */
    private function validateProduct(array $product): void
    {
        $required = ['id_product', 'quantity', 'is_virtual'];
        foreach ($required as $field) {
            if (!isset($product[$field])) {
                throw new InvalidArgumentException("Product missing required field: {$field}");
            }
        }
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
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getZoneId(): ?int
    {
        return $this->zoneId;
    }

    public function getAddressId(): ?int
    {
        return $this->addressId;
    }

    public function getCountryZoneId(): int
    {
        return $this->countryZoneId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function getOrderTotal(): float
    {
        return $this->orderTotal;
    }
}
