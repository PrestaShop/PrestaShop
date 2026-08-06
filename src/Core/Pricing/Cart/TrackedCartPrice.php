<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart;

use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\PriceBreakdown;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\PriceModification;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePriceInterface;

/**
 * Debug-aware CartPrice that auto-records every setter call as a PriceModification
 * via debug_backtrace, capturing which calculator made the change. Calculators are
 * completely unaware of the tracking — same interface as CartPrice.
 *
 * @experimental
 */
class TrackedCartPrice implements CartPriceInterface
{
    protected TaxablePrice $productTotal;
    protected TaxablePrice $shippingTotal;
    protected TaxablePrice $wrappingTotal;
    protected TaxablePrice $discountTotal;
    protected TaxablePrice $cartTotal;

    /** @var ProductPriceInterface[] */
    protected array $productPrices = [];
    protected PriceBreakdown $breakdown;

    protected function __construct(
        protected readonly int $cartId,
    ) {
        $this->productTotal = TaxablePrice::zero();
        $this->shippingTotal = TaxablePrice::zero();
        $this->wrappingTotal = TaxablePrice::zero();
        $this->discountTotal = TaxablePrice::zero();
        $this->cartTotal = TaxablePrice::zero();
        $this->breakdown = new PriceBreakdown();
    }

    public static function create(int $cartId): self
    {
        return new self($cartId);
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
        $this->recordModification('productTotal', $this->productTotal, $productTotal);
        $this->productTotal = $productTotal;
    }

    public function getShippingTotal(): TaxablePrice
    {
        return $this->shippingTotal;
    }

    public function setShippingTotal(TaxablePrice $shippingTotal): void
    {
        $this->recordModification('shippingTotal', $this->shippingTotal, $shippingTotal);
        $this->shippingTotal = $shippingTotal;
    }

    public function getWrappingTotal(): TaxablePrice
    {
        return $this->wrappingTotal;
    }

    public function setWrappingTotal(TaxablePrice $wrappingTotal): void
    {
        $this->recordModification('wrappingTotal', $this->wrappingTotal, $wrappingTotal);
        $this->wrappingTotal = $wrappingTotal;
    }

    public function getDiscountTotal(): TaxablePrice
    {
        return $this->discountTotal;
    }

    public function setDiscountTotal(TaxablePrice $discountTotal): void
    {
        $this->recordModification('discountTotal', $this->discountTotal, $discountTotal);
        $this->discountTotal = $discountTotal;
    }

    public function getCartTotal(): TaxablePrice
    {
        return $this->cartTotal;
    }

    public function setCartTotal(TaxablePrice $cartTotal): void
    {
        $this->recordModification('cartTotal', $this->cartTotal, $cartTotal);
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

    public function getBreakdown(): PriceBreakdown
    {
        return $this->breakdown;
    }

    protected function recordModification(string $property, TaxablePriceInterface $previous, TaxablePriceInterface $new): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[2] ?? [];

        $this->breakdown->addStep(new PriceModification(
            callerClass: $caller['class'] ?? 'unknown',
            callerLine: $caller['line'] ?? 0,
            property: $property,
            previousValue: (string) $previous->getTaxExcluded(),
            newValue: (string) $new->getTaxExcluded(),
        ));
    }
}
