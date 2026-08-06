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
 * Lightweight CartPrice DTO with no tracking overhead. Setters simply assign values.
 *
 * @experimental
 */
class CartPrice implements CartPriceInterface
{
    protected TaxablePrice $productTotal;
    protected TaxablePrice $shippingTotal;
    protected TaxablePrice $wrappingTotal;
    protected TaxablePrice $discountTotal;
    protected TaxablePrice $cartTotal;

    /** @var ProductPriceInterface[] */
    protected array $productPrices = [];

    protected function __construct(
        protected readonly int $cartId,
    ) {
        $this->productTotal = TaxablePrice::zero();
        $this->shippingTotal = TaxablePrice::zero();
        $this->wrappingTotal = TaxablePrice::zero();
        $this->discountTotal = TaxablePrice::zero();
        $this->cartTotal = TaxablePrice::zero();
    }

    public static function create(int $cartId): static
    {
        return new static($cartId);
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function getProductTotal(): TaxablePrice
    {
        return $this->productTotal;
    }

    public function setProductTotal(TaxablePrice $productTotal): void
    {
        $this->productTotal = $productTotal;
    }

    public function getShippingTotal(): TaxablePrice
    {
        return $this->shippingTotal;
    }

    public function setShippingTotal(TaxablePrice $shippingTotal): void
    {
        $this->shippingTotal = $shippingTotal;
    }

    public function getWrappingTotal(): TaxablePrice
    {
        return $this->wrappingTotal;
    }

    public function setWrappingTotal(TaxablePrice $wrappingTotal): void
    {
        $this->wrappingTotal = $wrappingTotal;
    }

    public function getDiscountTotal(): TaxablePrice
    {
        return $this->discountTotal;
    }

    public function setDiscountTotal(TaxablePrice $discountTotal): void
    {
        $this->discountTotal = $discountTotal;
    }

    public function getCartTotal(): TaxablePrice
    {
        return $this->cartTotal;
    }

    public function setCartTotal(TaxablePrice $cartTotal): void
    {
        $this->cartTotal = $cartTotal;
    }

    /**
     * @return ProductPriceInterface[]
     */
    public function getProductPrices(): array
    {
        return $this->productPrices;
    }

    /**
     * @param ProductPriceInterface[] $productPrices
     */
    public function setProductPrices(array $productPrices): void
    {
        $this->productPrices = $productPrices;
    }
}
