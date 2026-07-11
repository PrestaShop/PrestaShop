<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Address;
use BoOrderCore;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use CustomerMessage;
use CustomerThread;
use Employee;
use Exception;
use Message;
use Module;
use Order;
use PaymentModule;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\AddOrderFromBackOfficeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopDatabaseException;
use PrestaShopException;
use Tools;
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

        // Context country, language and currency is used in PaymentModule::validateOrder (it should rely on cart address country instead)
        $this->setCartContext($this->contextStateManager, $cart);

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
                '',
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

        $orderId = (int) $paymentModule->currentOrder;

        // Keep track of which employee created the order from the back office, so it is
        // displayed in the order "Messages" block (regression from 1.6, see issue #9676).
        if ($command->getEmployeeId()->getValue()) {
            $this->addEmployeeCreationMessage($orderId, (int) $command->getEmployeeId()->getValue());
        }

        return new OrderId($orderId);
    }

    /**
     * Records, as a private message attached to the order's customer thread, which employee
     * created the order from the back office, so it is displayed in the order "Messages" block.
     *
     * @param int $orderId
     * @param int $employeeId
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function addEmployeeCreationMessage(int $orderId, int $employeeId): void
    {
        $employee = new Employee($employeeId);
        if (!Validate::isLoadedObject($employee)) {
            return;
        }

        $order = new Order($orderId);
        $customer = new Customer((int) $order->id_customer);

        $customerThreadId = (int) CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, (int) $order->id);
        if (!$customerThreadId) {
            $customerThread = new CustomerThread();
            $customerThread->id_contact = 0;
            $customerThread->id_customer = (int) $order->id_customer;
            $customerThread->id_shop = (int) $order->id_shop;
            $customerThread->id_order = (int) $order->id;
            $customerThread->id_lang = (int) $order->id_lang;
            $customerThread->email = $customer->email;
            $customerThread->status = 'open';
            $customerThread->token = Tools::passwdGen(12);
            $customerThread->add();
            $customerThreadId = (int) $customerThread->id;
        }

        $translator = Context::getContext()->getTranslator();
        $message = sprintf(
            '%s %s. %s',
            $translator->trans('Manual order -- Employee:', [], 'Admin.Orderscustomers.Feature'),
            $employee->firstname[0],
            $employee->lastname
        );

        $customerMessage = new CustomerMessage();
        $customerMessage->id_customer_thread = $customerThreadId;
        $customerMessage->id_employee = $employeeId;
        $customerMessage->message = $message;
        $customerMessage->private = true;
        $customerMessage->add();
    }

    /**
     * Saves customer message and link it to the cart.
     *
     * @param Cart $cart
     * @param string $orderMessage
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
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
