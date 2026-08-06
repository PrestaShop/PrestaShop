<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Debug;

use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\Cart\TrackedCartPrice;

/**
 * Formats a TrackedCartPrice's PriceBreakdown into human-readable strings
 * or structured arrays. Delegates product-level history formatting to
 * ProductPriceHistoryDisplayer.
 *
 * @experimental
 */
class CartPriceHistoryDisplayer
{
    public function __construct(
        protected readonly ProductPriceHistoryDisplayer $productPriceHistoryDisplayer,
    ) {
    }

    /**
     * Formats a CartPrice's breakdown as a human-readable string, including
     * indented product-level breakdowns for each product in the cart.
     */
    public function formatAsString(CartPriceInterface $cartPrice): string
    {
        if (!$cartPrice instanceof TrackedCartPrice) {
            return 'No tracking data available';
        }

        $lines = [];

        $cartBreakdown = $this->productPriceHistoryDisplayer->formatBreakdownAsString($cartPrice->getBreakdown());
        $lines[] = 'Cart #' . $cartPrice->getCartId() . ':';
        $lines[] = $this->indent($cartBreakdown);

        foreach ($cartPrice->getProductPrices() as $productPrice) {
            $lines[] = sprintf(
                '  Product #%d (combination: %d, qty: %d):',
                $productPrice->getProductId(),
                $productPrice->getCombinationId(),
                $productPrice->getQuantity()
            );
            $productHistory = $this->productPriceHistoryDisplayer->formatAsString($productPrice);
            $lines[] = $this->indent($this->indent($productHistory));
        }

        return implode("\n", $lines);
    }

    /**
     * Formats a CartPrice's breakdown as a structured array, with a sub-array
     * for each product's history.
     *
     * @return array{cart: array, products: array<int, array{product_id: int, combination_id: int, quantity: int, history: array}>}
     */
    public function formatAsArray(CartPriceInterface $cartPrice): array
    {
        if (!$cartPrice instanceof TrackedCartPrice) {
            return ['cart' => [], 'products' => []];
        }

        $cartHistory = $this->productPriceHistoryDisplayer->formatBreakdownAsArray($cartPrice->getBreakdown());

        $products = [];
        foreach ($cartPrice->getProductPrices() as $productPrice) {
            $products[] = [
                'product_id' => $productPrice->getProductId(),
                'combination_id' => $productPrice->getCombinationId(),
                'quantity' => $productPrice->getQuantity(),
                'history' => $this->productPriceHistoryDisplayer->formatAsArray($productPrice),
            ];
        }

        return [
            'cart' => $cartHistory,
            'products' => $products,
        ];
    }

    protected function indent(string $text): string
    {
        return '  ' . str_replace("\n", "\n  ", $text);
    }
}
