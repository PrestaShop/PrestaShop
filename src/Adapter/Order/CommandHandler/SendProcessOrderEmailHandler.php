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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use Customer;
use Language;
use Link;
use Mail;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendProcessOrderEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\SendProcessOrderEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailSendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShopException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Handles SendProcessOrderEmail command using legacy object model
 */
class SendProcessOrderEmailHandler implements SendProcessOrderEmailHandlerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Link
     */
    private $contextLink;

    public function __construct(
        TranslatorInterface $translator,
        Link $contextLink
    ) {
        $this->translator = $translator;
        $this->contextLink = $contextLink;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SendProcessOrderEmailCommand $command): void
    {
        $cartId = $command->getCartId();

        try {
            $cart = $this->getCart($cartId);
            $customer = $this->getCustomer(new CustomerId((int) $cart->id_customer));
            $cartLanguage = $cart->getAssociatedLanguage();
            $langId = (int) $cartLanguage->getId();

            if (!Mail::send(
                $langId,
                'backoffice_order',
                $this->getSubject($cartLanguage),
                $this->getEmailTemplateVars($cartId->getValue(), $cartLanguage, $customer),
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null,
                null,
                null,
                null,
                _PS_MAIL_DIR_,
                true,
                $cart->id_shop
            )) {
                throw new OrderEmailSendException('Failed to send order process email to customer', OrderEmailSendException::FAILED_SEND_PROCESS_ORDER);
            }
        } catch (PrestaShopException $e) {
            throw new OrderException('An error occurred when trying to get info for order processing');
        }
    }

    /**
     * Provides legacy cart object
     *
     * @param CartId $cartId
     *
     * @return Cart
     *
     * @throws CartNotFoundException
     */
    private function getCart(CartId $cartId)
    {
        $cartIdValue = $cartId->getValue();
        $cart = new Cart($cartIdValue);

        if ($cart->id !== $cartIdValue) {
            throw new CartNotFoundException(sprintf('Cart #%s not found', $cartIdValue));
        }

        return $cart;
    }

    /**
     * Provides legacy customer object
     *
     * @param CustomerId $customerId
     *
     * @return Customer
     *
     * @throws CustomerNotFoundException
     */
    private function getCustomer(CustomerId $customerId)
    {
        $customerIdValue = $customerId->getValue();
        $customer = new Customer($customerIdValue);

        if ($customer->id !== $customerIdValue) {
            throw new CustomerNotFoundException(new CustomerId($customerIdValue), sprintf('Customer #%s not found', $customerIdValue));
        }

        return $customer;
    }

    /**
     * Provides translated subject for email
     *
     * @param Language $cartLanguage
     *
     * @return string
     */
    private function getSubject(Language $cartLanguage): string
    {
        return $this->translator->trans(
            'Process the payment of your order',
            [],
            'Emails.Subject',
            $cartLanguage->locale
        );
    }

    /**
     * Provides email template variables
     *
     * @param int $cartId
     * @param Language $cartLanguage
     * @param Customer $customer
     *
     * @return array
     */
    private function getEmailTemplateVars(int $cartId, Language $cartLanguage, Customer $customer): array
    {
        $orderLink = $this->contextLink->getPageLink(
            'order',
            false,
            $cartLanguage->id,
            http_build_query([
                'step' => 3,
                'recover_cart' => $cartId,
                'token_cart' => md5(_COOKIE_KEY_ . 'recover_cart_' . $cartId),
            ])
        );

        return [
            '{order_link}' => $orderLink,
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
        ];
    }
}
