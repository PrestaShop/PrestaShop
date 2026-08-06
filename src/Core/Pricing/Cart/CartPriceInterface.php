<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;

/**
 * Mutable DTO carrying the computed prices for a cart.
 * Calculators receive this and mutate it in place.
 *
 * @experimental
 */
interface CartPriceInterface
{
    public function getCartId(): int;

    public function getProductTotal(): TaxablePrice;

    public function setProductTotal(TaxablePrice $productTotal): void;

    public function getShippingTotal(): TaxablePrice;

    public function setShippingTotal(TaxablePrice $shippingTotal): void;

    public function getWrappingTotal(): TaxablePrice;

    public function setWrappingTotal(TaxablePrice $wrappingTotal): void;

    public function getDiscountTotal(): TaxablePrice;

    public function setDiscountTotal(TaxablePrice $discountTotal): void;

    public function getCartTotal(): TaxablePrice;

    public function setCartTotal(TaxablePrice $cartTotal): void;

    /**
     * @return ProductPriceInterface[]
     */
    public function getProductPrices(): array;

    /**
     * @param ProductPriceInterface[] $productPrices
     */
    public function setProductPrices(array $productPrices): void;
}
