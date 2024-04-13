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

use Address;
use BoOrderCore;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Employee;
use Exception;
use Message;
use Module;
use PaymentModule;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\AddOrderFromBackOfficeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class AddOrderFromBackOfficeHandler extends AbstractOrderCommandHandler implements AddOrderFromBackOfficeHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(ContextStateManager $contextStateManager)
    {
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddOrderFromBackOfficeCommand $command)
    {
        $paymentModule = !Configuration::get('PS_CATALOG_MODE') ?
            Module::getInstanceByName($command->getPaymentModuleName()) :
            new BoOrderCore();

        if (false === $paymentModule) {
            throw new OrderException(sprintf('Payment method "%s" does not exist.', $paymentModule));
        }
        /** @var PaymentModule $paymentModule */
        $cart = new Cart($command->getCartId()->getValue());

        $this->assertAddressesAreNotDisabled($cart);

        //Context country, language and currency is used in PaymentModule::validateOrder (it should rely on cart address country instead)
        $this->setCartContext($this->contextStateManager, $cart);

        if ($command->getEmployeeId()->getValue()) {
            $translator = Context::getContext()->getTranslator();
            $employee = new Employee($command->getEmployeeId()->getValue());
            $message = sprintf(
                '%s %s. %s',
                $translator->trans('Manual order -- Employee:', [], 'Admin.Orderscustomers.Feature'),
                $employee->firstname[0],
                $employee->lastname
            );
        } else {
            $message = '';
        }

        try {
            $orderMessage = $command->getOrderMessage();
            if (!empty($orderMessage)) {
                $this->addOrderMessage($cart, $orderMessage);
            }

            $paymentModule->validateOrder(
                (int) $cart->id,
                $command->getOrderStateId(),
                $cart->getOrderTotal(),
                $paymentModule->displayName,
                $message,
                [],
                null,
                false,
                $cart->secure_key
            );
        } catch (Exception $e) {
            throw new OrderException('Failed to add order. ' . $e->getMessage(), 0, $e);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }

        if (!$paymentModule->currentOrder) {
            throw new OrderException('Failed to add order.');
        }

        return new OrderId((int) $paymentModule->currentOrder);
    }

    /**
     * Saves customer message and link it to the cart.
     *
     * @param Cart $cart
     * @param string $orderMessage
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws OrderConstraintException
     */
    private function addOrderMessage(Cart $cart, string $orderMessage): void
    {
        if (!Validate::isMessage($orderMessage)) {
            throw new OrderConstraintException('The order message is invalid', OrderConstraintException::INVALID_CUSTOMER_MESSAGE);
        }

        $messageId = null;
        if ($oldMessage = Message::getMessageByCartId((int) $cart->id)) {
            $messageId = $oldMessage['id_message'];
        }
        $message = new Message((int) $messageId);
        $message->message = $orderMessage;
        $message->id_cart = (int) $cart->id;
        $message->id_customer = (int) $cart->id_customer;
        $message->save();
    }

    /**
     * @param Cart $cart
     */
    private function assertAddressesAreNotDisabled(Cart $cart)
    {
        $isDeliveryCountryDisabled = !Address::isCountryActiveById((int) $cart->id_address_delivery);
        $isInvoiceCountryDisabled = !Address::isCountryActiveById((int) $cart->id_address_invoice);

        if ($isDeliveryCountryDisabled) {
            throw new OrderException(sprintf('Delivery country for cart with id "%d" is disabled.', $cart->id));
        }

        if ($isInvoiceCountryDisabled) {
            throw new OrderException(sprintf('Invoice country for cart with id "%d" is disabled.', $cart->id));
        }
    }
}
