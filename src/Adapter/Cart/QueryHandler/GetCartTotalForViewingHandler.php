<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use Cart;
use Context;
use Currency;
use Customer;
use Group;
use Order;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartTotalForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartTotalForViewingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartTotalForViewing;
use Validate;

/**
 * Computes only a cart's displayed total. This mirrors how GetCartForViewingHandler produces the
 * "total" of its summary, but skips loading the products and the rest of the cart view, so the
 * carts grids can fill the total column of every row without the full view being built per row.
 */
#[AsQueryHandler]
final class GetCartTotalForViewingHandler implements GetCartTotalForViewingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCartTotalForViewing $query)
    {
        $cartId = $query->getCartId()->getValue();
        $cart = new Cart($cartId);

        if ($cart->id !== $cartId) {
            throw new CartNotFoundException(sprintf('Cart with id "%s" were not found', $cartId));
        }

        // getOrderTotal() relies on the context cart/currency/customer, so set them exactly as
        // GetCartForViewingHandler does before reading the total.
        $context = Context::getContext();
        $context->cart = $cart;
        $context->currency = new Currency($cart->id_currency);
        $context->customer = new Customer($cart->id_customer);

        $order = new Order((int) Order::getIdByCartId($cart->id));
        if (Validate::isLoadedObject($order)) {
            $taxCalculationMethod = $order->getTaxCalculationMethod();
        } else {
            $taxCalculationMethod = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        }

        $withTax = PS_TAX_EXC != $taxCalculationMethod;

        return new CartTotalForViewing(
            (float) $cart->getOrderTotal($withTax),
            (float) $cart->getOrderTotal($withTax, Cart::ONLY_PRODUCTS)
        );
    }
}
