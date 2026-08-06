<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product;

use PrestaShop\PrestaShop\Core\Pricing\ValueObject\ImmutableTaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;

/**
 * Mutable DTO carrying the computed prices for a single product (or combination).
 * Calculators receive this and mutate it in place.
 */
interface ProductPriceInterface
{
    public function getProductId(): int;

    public function getCombinationId(): int;

    public function getQuantity(): int;

    public function getUnitPrice(): TaxablePrice;

    public function setUnitPrice(TaxablePrice $unitPrice): void;

    public function getOriginalPrice(): TaxablePrice;

    public function setOriginalPrice(TaxablePrice $originalPrice): void;

    public function getDiscountPrice(): TaxablePrice;

    public function setDiscountPrice(TaxablePrice $discountPrice): void;

    /**
     * The final rounded price after all discounts have been applied (originalPrice - discountPrice).
     */
    public function getFinalPrice(): ImmutableTaxablePrice;

    public function setFinalPrice(ImmutableTaxablePrice $finalPrice): void;
}
