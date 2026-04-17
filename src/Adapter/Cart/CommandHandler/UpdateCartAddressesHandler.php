<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartAddressesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;

/**
 * @internal
 */
#[AsCommandHandler]
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
        // updateDeliveryAddressId() will actually allow the address change to be impacted on all
        // other data linked to the cart delivery address (and it doesn't modify the invoice address)
        $cart->updateDeliveryAddressId((int) $cart->id_address_delivery, $command->getNewDeliveryAddressId()->getValue());

        $cart->id_address_invoice = $command->getNewInvoiceAddressId()->getValue();
    }
}
