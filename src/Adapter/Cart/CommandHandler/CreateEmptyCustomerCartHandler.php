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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use Configuration;
use Customer;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\CreateEmptyCustomerCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * @internal
 */
final class CreateEmptyCustomerCartHandler implements CreateEmptyCustomerCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(CreateEmptyCustomerCartCommand $command)
    {
        $customer = new Customer($command->getCustomerId()->getValue());

        $cart = new Cart();

        $cart->recyclable = 0;
        $cart->gift = 0;
        $cart->id_customer = $customer->id;
        $cart->secure_key = $customer->secure_key;

        $cart->id_shop = $command->getShopId()->getValue();
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');

        $addresses = $customer->getAddresses($cart->id_lang);
        $addressId = !empty($addresses) ? (int) reset($addresses)['id_address'] : null;
        $cart->id_address_delivery = $addressId;
        $cart->id_address_invoice = $addressId;

        $cart->setNoMultishipping();
        $cart->save();

        return new CartId((int) $cart->id);
    }
}
