<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Utility;

use Cart;
use Order;

/**
 * Payment module calling createOrderCartRules() from its own order validation, outside validateOrder().
 */
class OrderCartRulesPaymentModuleFake extends PaymentModuleFake
{
    public function addCartRulesToOrder(Order $order, Cart $cart, int $orderStateId): void
    {
        $order->product_list = $cart->getProducts();

        $this->createOrderCartRules($order, $cart, [$order], 0, 0, $orderStateId);
    }
}
