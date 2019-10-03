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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Address;
use Cart;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartAddressesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;

/**
 * @internal
 */
final class UpdateCartAddressesHandler extends AbstractCartHandler implements UpdateCartAddressesHandlerInterface
{
    /**
     * @param UpdateCartAddressesCommand $command
     */
    public function handle(UpdateCartAddressesCommand $command)
    {
        $cart = $this->getCart($command->getCartId());

        $this->assertAddressCanBeUsedInCart($cart, $command->getNewDeliveryAddressId());
        $this->assertAddressCanBeUsedInCart($cart, $command->getNewInvoiceAddressId());

        $cart->id_address_delivery = $command->getNewDeliveryAddressId()->getValue();
        $cart->id_address_invoice = $command->getNewInvoiceAddressId()->getValue();

        if (false === $cart->save()) {
            throw new CartException(sprintf(
                'Failed to update addresses for cart with id "%s"',
                $cart->id
            ));
        }

        // @todo: Should context be changed at controller layer instead?
        \Context::getContext()->cart = $cart;
    }

    /**
     * @param Cart $cart
     * @param AddressId $addressId
     */
    private function assertAddressCanBeUsedInCart(Cart $cart, AddressId $addressId)
    {
        $address = new Address($addressId->getValue());

        if ((int) $address->id_customer !== (int) $cart->id_customer) {
            throw new CartException(
                sprintf(
                    'Address with id "%s" does not belong to cart customer, thus it cannot be used.',
                    $address->id
                )
            );
        }
    }
}
