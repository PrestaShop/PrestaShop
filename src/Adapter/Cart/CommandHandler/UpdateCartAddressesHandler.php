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

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartAddressesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;

/**
 * @internal
 */
final class UpdateCartAddressesHandler extends AbstractCartHandler implements UpdateCartAddressesHandlerInterface
{
    /**
     * @var UpdateCartCarrierHandlerInterface
     */
    private $updateCartCarrierHandler;

    /**
     * @param UpdateCartCarrierHandlerInterface $updateCartCarrierHandler
     */
    public function __construct(UpdateCartCarrierHandlerInterface $updateCartCarrierHandler)
    {
        $this->updateCartCarrierHandler = $updateCartCarrierHandler;
    }

    /**
     * @param UpdateCartAddressesCommand $command
     */
    public function handle(UpdateCartAddressesCommand $command)
    {
        $cart = $this->getCart($command->getCartId());
        $this->fillCartWithCommandData($cart, $command);

        if (false === $cart->update()) {
            throw new CartException(sprintf('Failed to update addresses for cart with id "%s"', $cart->id));
        }

        $this->updateCartCarrierHandler->handle(new UpdateCartCarrierCommand($cart->id, $cart->id_carrier));
    }

    /**
     * Fetches updatable fields from command to cart
     *
     * @param Cart $cart
     * @param UpdateCartAddressesCommand $command
     */
    private function fillCartWithCommandData(Cart $cart, UpdateCartAddressesCommand $command): void
    {
        if ($command->getNewDeliveryAddressId()) {
            // updateDeliveryAddressId() will actually allow the address change to be impacted on all
            // other data linked to the cart delivery address (and it doesn't modify the invoice address)
            $cart->updateDeliveryAddressId((int) $cart->id_address_delivery, $command->getNewDeliveryAddressId()->getValue());
        }

        if ($command->getNewInvoiceAddressId()) {
            $cart->id_address_invoice = $command->getNewInvoiceAddressId()->getValue();
        }
    }
}
