<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Utility;

use Cart;
use Currency;
use PrestaShopException;

/**
 * Payment module whose order creation fails, to test what validateOrder() leaves behind
 * when it is interrupted by an exception.
 */
class OrderCreationFailingPaymentModuleFake extends PaymentModuleFake
{
    protected function createOrderFromCart(
        Cart $cart,
        Currency $currency,
        $productList,
        $addressId,
        $context,
        $reference,
        $secure_key,
        $payment_method,
        $name,
        $dont_touch_amount,
        $amount_paid,
        $warehouseId,
        $cart_total_paid,
        $debug,
        $order_status,
        $id_order_state,
        $carrierId = null,
    ) {
        throw new PrestaShopException('Order creation failed');
    }
}
