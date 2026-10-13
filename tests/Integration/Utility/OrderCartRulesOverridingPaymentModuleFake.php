<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            $cartRuleToUpdate->quantity = max(0, $cartRuleToUpdate->quantity - 1);
            $cartRuleToUpdate->update();
        }

        return [];
    }
}
