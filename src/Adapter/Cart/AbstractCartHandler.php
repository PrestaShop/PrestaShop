<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart;

use Cart;
use CartRule;
use Context;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Cart\AmountImmutable;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShopException;
use Shop;

/**
 * Provides reusable methods for cart handlers
 *
 * @internal
 */
abstract class AbstractCartHandler
{
    /**
     * @param CartId $cartId
     *
     * @return Cart
     *
     * @throws CartNotFoundException
     */
    protected function getCart(CartId $cartId)
    {
        try {
            $cart = new Cart($cartId->getValue());
        } catch (PrestaShopException) {
            throw new CartException(sprintf('An error occurred when trying to load cart with id "%s', $cartId->getValue()));
        }

        if (!Validate::isLoadedObject($cart) || $cartId->getValue() !== (int) $cart->id) {
            throw new CartNotFoundException(sprintf('Cart with id "%s" was not found', $cartId->getValue()));
        }

        return $cart;
    }

    /**
     * @param ContextStateManager $contextStateManager
     * @param Cart $cart
     */
    protected function setCartContext(ContextStateManager $contextStateManager, Cart $cart): void
    {
        $contextStateManager
            ->saveCurrentContext()
            ->setCart($cart)
            ->setCustomer(new Customer($cart->id_customer))
            ->setCurrency(new Currency($cart->id_currency))
            ->setLanguage($cart->getAssociatedLanguage())
            ->setCountry($cart->getTaxCountry())
            ->setShop(new Shop($cart->id_shop))
        ;
    }

    /**
     * Discount charged by each cart rule, from the cart calculator. Free shipping and gift rules are left out,
     * their lines also carry the shipping or the gift product.
     *
     * @return array<int, AmountImmutable> indexed by cart rule id
     */
    protected function getCalculatedCartRuleDiscounts(Cart $cart): array
    {
        $calculator = $cart->newCalculator(
            $cart->getProducts(),
            $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false),
            null,
            Context::getContext()->getComputingPrecision()
        );
        $calculator->processCalculation();

        $discounts = [];
        foreach ($calculator->getCartRulesData() as $cartRuleData) {
            $ruleData = $cartRuleData->getRuleData();
            if (!$ruleData['free_shipping'] && !$ruleData['gift_product']) {
                $discounts[(int) $ruleData['id_cart_rule']] = $cartRuleData->getDiscountApplied();
            }
        }

        return $discounts;
    }
}
