<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
use Module;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\AddOrderFromBackOfficeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * @internal
 */
final class AddOrderFromBackOfficeHandler implements AddOrderFromBackOfficeHandlerInterface
{
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

        $cart = new Cart($command->getCartId()->getValue());

        Context::getContext()->currency = new Currency((int) $cart->id_currency);
        Context::getContext()->customer = new Customer((int) $cart->id_customer);

        $this->assertAddressesAreNotDisabled($cart);

        $translator = Context::getContext()->getTranslator();
        $employee = new Employee($command->getEmployeeId()->getValue());
        $message = sprintf(
            '%s %s. %s',
            $translator->trans('Manual order -- Employee:', [], 'Admin.Orderscustomers.Feature'),
            $employee->firstname[0],
            $employee->lastname
        );

        try {
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
        }

        if (!$paymentModule->currentOrder) {
            throw new OrderException('Failed to add order.');
        }

        return new OrderId((int) $paymentModule->currentOrder);
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
