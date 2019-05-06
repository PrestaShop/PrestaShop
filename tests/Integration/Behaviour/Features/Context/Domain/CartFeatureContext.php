<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Configuration;
use Context;
use Country;
use Customer;
use Exception;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SetFreeShippingToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\QuantityAction;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use Product;

class CartFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create an empty cart for customer with email :customerEmail
     */
    public function createEmptyCartForCustomer($customerEmail)
    {
        $customer = Customer::getCustomersByEmail($customerEmail);

        $this->getCommandBus()->handle(
            new CreateEmptyCustomerCartCommand(
                (int) $customer[0]['id_customer'],
                (int) Context::getContext()->shop->id
            )
        );
    }

    /**
     * @When I add :quantity products with reference :productReference to the cart
     */
    public function addProductToCarts($quantity, $productReference)
    {
        $productId = (int) Product::getIdByReference($productReference);

        $this->getCommandBus()->handle(
            new UpdateProductQuantityInCartCommand(
                (int) Context::getContext()->cart->id,
                $productId,
                (int) $quantity,
                QuantityAction::INCREASE_PRODUCT_QUANTITY
            )
        );
    }

    /**
     * @When I select :countryIsoCode address as delivery and invoice address
     */
    public function selectAddressAsDeliveryAndInvoiceAddress($countryIsoCode)
    {
        $customer = new Customer(Context::getContext()->cart->id_customer);

        $getAddressByCountryIsoCode = static function ($isoCode) use ($customer) {
            $customerAddresses = $customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));

            foreach ($customerAddresses as $address) {
                $country = new Country($address['id_country']);

                if ($country->iso_code === $isoCode) {
                    return (int) $address['id_address'];
                }
            }

            throw new Exception(sprintf('Customer does not have address in "%s" country.', $isoCode));
        };

        $addressId = $getAddressByCountryIsoCode($countryIsoCode);

        $this->getCommandBus()->handle(
            new UpdateCartAddressesCommand(
                (int) Context::getContext()->cart->id,
                $addressId,
                $addressId
            )
        );
    }

    /**
     * @When I set Free shipping to the cart
     */
    public function setFreeShippingToCart()
    {
        $this->getCommandBus()->handle(
            new SetFreeShippingToCartCommand(
                (int) Context::getContext()->cart->id,
                true
            )
        );
    }

    /**
     * @When I place order with :paymentModuleName payment method and :orderStatus order status
     */
    public function placeOrderWithPaymentMethodAndOrderStatus($paymentModuleName, $orderStatus)
    {
        $orderStates = OrderState::getOrderStates(Context::getContext()->language->id);
        $orderStatusId = null;

        foreach ($orderStates as $state) {
            if ($state['name'] === $orderStatus) {
                $orderStatusId = (int) $state['id_order_state'];
            }
        }

        $this->getCommandBus()->handle(
            new AddOrderFromBackOfficeCommand(
                (int) Context::getContext()->cart->id,
                (int) Context::getContext()->employee->id,
                '',
                $paymentModuleName,
                $orderStatusId
            )
        );
    }
}
