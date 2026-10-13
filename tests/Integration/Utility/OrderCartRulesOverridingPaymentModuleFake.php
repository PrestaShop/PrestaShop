<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Utility;

use Cart;
use CartRule;
use Order;

/**
 * Payment module overriding createOrderCartRules() without calling the parent, with the quantity
 * decrement of the previous versions of the method.
 */
class OrderCartRulesOverridingPaymentModuleFake extends PaymentModuleFake
{
    protected function createOrderCartRules(
        Order $order,
        Cart $cart,
        $order_list,
        $total_reduction_value_ti,
        $total_reduction_value_tex,
        $id_order_state
    ) {
        foreach ($cart->getCartRules() as $cartRuleData) {
            /** @var CartRule $cartRule */
            $cartRule = $cartRuleData['obj'];
            $order->addCartRule(
                $cartRule->id,
                $cartRule->name,
                ['tax_incl' => $cartRuleData['value_real'], 'tax_excl' => $cartRuleData['value_tax_exc']],
                0,
                $cartRule->free_shipping
            );

            $cartRuleToUpdate = new CartRule((int) $cartRule->id);
            if ($cartRuleToUpdate->quantity !== null) {
                $cartRuleToUpdate->quantity = max(0, $cartRuleToUpdate->quantity - 1);
            }
            $cartRuleToUpdate->update();
        }

        return [];
    }
}
