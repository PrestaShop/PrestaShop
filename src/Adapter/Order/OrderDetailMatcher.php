<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

/**
 * Finds the order detail matching a given product line of an order.
 *
 * An order can hold several order details for the very same product and combination, distinguished only by
 * their customization: a customizable product ordered twice with two different customizations, or ordered
 * both plain and customized, produces one order detail per line. Matching on the product alone - or even on
 * the product and its combination - therefore returns an arbitrary row.
 */
final class OrderDetailMatcher
{
    /**
     * @param array<array<string, mixed>> $orderDetails order_detail rows, as returned by OrderDetail::getList(),
     *                                                  Order::getOrderDetailList() or Order::getProductsDetail()
     * @param int $productId
     * @param int $combinationId 0 when the product has no combination
     * @param int $customizationId 0 when the product line is not customized
     *
     * @return array<string, mixed>|null
     */
    public function match(array $orderDetails, int $productId, int $combinationId = 0, int $customizationId = 0): ?array
    {
        foreach ($orderDetails as $orderDetail) {
            if (
                (int) $orderDetail['product_id'] === $productId
                && (int) ($orderDetail['product_attribute_id'] ?? 0) === $combinationId
                && (int) ($orderDetail['id_customization'] ?? 0) === $customizationId
            ) {
                return $orderDetail;
            }
        }

        return null;
    }

    /**
     * Same as match(), for a cart product line: those carry the same identifiers under their cart column names.
     *
     * @param array<array<string, mixed>> $orderDetails
     * @param array<string, mixed> $cartProduct
     *
     * @return array<string, mixed>|null
     */
    public function matchCartProduct(array $orderDetails, array $cartProduct): ?array
    {
        return $this->match(
            $orderDetails,
            (int) $cartProduct['id_product'],
            (int) ($cartProduct['id_product_attribute'] ?? 0),
            (int) ($cartProduct['id_customization'] ?? 0)
        );
    }
}
