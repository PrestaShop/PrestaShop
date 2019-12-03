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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SetFreeShippingToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\QuantityAction;
use Product;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CartFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given the current currency is :currencyIsoCode
     */
    public function addCurrencyToContext($currencyIsoCode)
    {
        $currency = new Currency();
        $currency->name = $currencyIsoCode;
        $currency->precision = 2;
        $currency->iso_code = $currencyIsoCode;
        $currency->active = 1;
        $currency->conversion_rate = 1;

        Context::getContext()->currency = $currency;
    }

    /**
     * @When I create an empty cart :cartReference for customer :customerReference
     */
    public function createEmptyCartForCustomer($cartReference, $customerReference)
    {
        // Clear static cache each time you create a cart
        Cart::resetStaticCache();
        $customer = SharedStorage::getStorage()->get($customerReference);

        /** @var CartId $cartId */
        $cartId = $this->getCommandBus()->handle(
            new CreateEmptyCustomerCartCommand(
                (int) $customer->id,
                (int) Context::getContext()->shop->id
            )
        );

        SharedStorage::getStorage()->set($cartReference, new Cart($cartId->getValue()));
    }

    /**
     * @When I add :quantity products with reference :productReference to the cart :reference
     */
    public function addProductToCarts($quantity, $productReference, $reference)
    {
        $productId = (int) Product::getIdByReference($productReference);

        $this->getCommandBus()->handle(
            new UpdateProductQuantityInCartCommand(
                (int) SharedStorage::getStorage()->get($reference)->id,
                $productId,
                (int) $quantity,
                QuantityAction::INCREASE_PRODUCT_QUANTITY
            )
        );
    }

    /**
     * @When I select :countryIsoCode address as delivery and invoice address for customer :customerReference in cart :cartReference
     */
    public function selectAddressAsDeliveryAndInvoiceAddress($countryIsoCode, $customerReference, $cartReference)
    {
        $customer = SharedStorage::getStorage()->get($customerReference);

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
                (int) SharedStorage::getStorage()->get($cartReference)->id,
                $addressId,
                $addressId
            )
        );
    }

    /**
     * @When I set Free shipping to the cart :reference
     */
    public function setFreeShippingToCart($reference)
    {
        $this->getCommandBus()->handle(
            new SetFreeShippingToCartCommand(
                (int) SharedStorage::getStorage()->get($reference)->id,
                true
            )
        );
    }
}
