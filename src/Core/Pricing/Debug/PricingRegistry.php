<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Debug;

use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\ProductPriceInterface;

/**
 * Request-scoped collector of all computed ProductPrice and CartPrice instances.
 * The orchestrator registers each result here for debug toolbar / profiler usage.
 */
class PricingRegistry
{
    /** @var ProductPriceInterface[] */
    protected array $productPrices = [];

    /** @var CartPriceInterface[] */
    protected array $cartPrices = [];

    public function registerProductPrice(ProductPriceInterface $productPrice): void
    {
        $this->productPrices[] = $productPrice;
    }

    /**
     * @return ProductPriceInterface[]
     */
    public function getProductPrices(): array
    {
        return $this->productPrices;
    }

    public function registerCartPrice(CartPriceInterface $cartPrice): void
    {
        $this->cartPrices[] = $cartPrice;
    }

    /**
     * @return CartPriceInterface[]
     */
    public function getCartPrices(): array
    {
        return $this->cartPrices;
    }

    public function count(): int
    {
        return count($this->productPrices) + count($this->cartPrices);
    }

    public function clear(): void
    {
        $this->productPrices = [];
        $this->cartPrices = [];
    }
}
