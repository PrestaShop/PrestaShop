<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use Context;
use Customer;
use Language;
use Mail;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SendCartToCustomerCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\SendCartToCustomerHanlderInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use Validate;

/**
 * @internal
 */
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

        $cartLanguage = new Language((int) $cart->id_lang);

        $emailWasSent = Mail::send(
            (int) $cart->id_lang,
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
            false,
            (int) $cart->id_lang,
            [
                'step' => 3,
                'recover_cart' => $cart->id,
                'token_cart' => md5(_COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id),
            ]
        );
    }
}
