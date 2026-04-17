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
use PrestaShopDatabaseException;
use PrestaShopException;
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
