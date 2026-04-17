<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use Configuration;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\CreateEmptyCustomerCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShopException;

/**
 * @internal
 */
#[AsCommandHandler]
final class CreateEmptyCustomerCartHandler implements CreateEmptyCustomerCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(CreateEmptyCustomerCartCommand $command)
    {
        $customer = new Customer($command->getCustomerId()->getValue());

        $lastEmptyCartId = $customer->getLastEmptyCart(false);

        if ($lastEmptyCartId) {
            $cart = new Cart($lastEmptyCartId);
        } else {
            $cart = $this->createEmptyCustomerCart($customer);
        }

        return new CartId((int) $cart->id);
    }

    /**
     * @param Customer $customer
     *
     * @return Cart
     *
     * @throws PrestaShopException
     */
    private function createEmptyCustomerCart(Customer $customer): Cart
    {
        $cart = new Cart();

        $cart->recyclable = false;
        $cart->gift = false;
        $cart->id_customer = $customer->id;
        $cart->secure_key = $customer->secure_key;

        $cart->id_shop = $customer->id_shop;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_currency = Currency::getDefaultCurrencyId();

        $addresses = $customer->getAddresses($cart->id_lang);
        $addressId = !empty($addresses) ? (int) reset($addresses)['id_address'] : null;
        $cart->id_address_delivery = $addressId;
        $cart->id_address_invoice = $addressId;

        $cart->save();

        return $cart;
    }
}
