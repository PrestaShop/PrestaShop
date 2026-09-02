<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use Context;
use Customer;
use Mail;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SendCartToCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\SendCartToCustomerHanlderInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use Validate;

/**
 * @internal
 *
 * @deprecated Since 9.0 and will be removed in the next major.
 */
#[AsCommandHandler]
final class SendCartToCustomerHandler implements SendCartToCustomerHanlderInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(SendCartToCustomerCommand $command)
    {
        $cart = $this->getCart($command->getCartId());
        $customer = $this->getCustomer($cart->id_customer);

        $mailVars = [
            '{order_link}' => $this->generateCheckoutUrl($cart),
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
        ];

        $cartLanguage = $cart->getAssociatedLanguage();

        $emailWasSent = Mail::send(
            (int) $cartLanguage->getId(),
            'backoffice_order',
            Context::getContext()->getTranslator()->trans(
                'Process the payment of your order',
                [],
                'Emails.Subject',
                $cartLanguage->locale
            ),
            $mailVars,
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            true,
            $cart->id_shop
        );

        if (!$emailWasSent) {
            throw new CartException('Failed to send email to customer.');
        }
    }

    /**
     * @param CartId $cartId
     *
     * @return Cart
     *
     * @throws CartNotFoundException
     */
    private function getCart(CartId $cartId)
    {
        $cart = new Cart($cartId->getValue());

        if (!Validate::isLoadedObject($cart)) {
            throw new CartNotFoundException(sprintf('Cart with id "%d" was not found', $cartId->getValue()));
        }

        return $cart;
    }

    /**
     * @param int $customerId
     *
     * @return Customer
     *
     * @throws CartException
     */
    private function getCustomer($customerId)
    {
        $customer = new Customer($customerId);

        if (!Validate::isLoadedObject($customer)) {
            throw new CartException(sprintf('Customer with id "%d" was not found', $customerId));
        }

        return $customer;
    }

    /**
     * @param Cart $cart
     *
     * @return string
     */
    private function generateCheckoutUrl(Cart $cart)
    {
        return Context::getContext()->link->getPageLink(
            'order',
            null,
            (int) $cart->getAssociatedLanguage()->getId(),
            [
                'step' => 3,
                'recover_cart' => $cart->id,
                'token_cart' => md5(_COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id),
            ]
        );
    }
}
