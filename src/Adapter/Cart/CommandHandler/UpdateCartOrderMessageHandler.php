<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Cart;
use Message;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\UpdateCartOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use Validate;

/**
 * Stores the order message of a cart, so a draft written while preparing an order survives the page.
 */
#[AsCommandHandler]
final class UpdateCartOrderMessageHandler extends AbstractCartHandler implements UpdateCartOrderMessageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCartOrderMessageCommand $command): void
    {
        $cart = $this->getCart($command->getCartId());

        if ($cart->orderExists()) {
            throw new CartException(sprintf('Order for cart with id "%s" already exists.', $cart->id));
        }
        $orderMessage = $command->getOrderMessage();

        if ('' !== $orderMessage && !Validate::isMessage($orderMessage)) {
            throw new CartConstraintException(
                'The order message is invalid',
                CartConstraintException::INVALID_ORDER_MESSAGE
            );
        }

        $message = $this->findCartMessage($cart);

        if ('' === $orderMessage) {
            if (null !== $message) {
                $message->delete();
            }

            return;
        }

        if (null === $message) {
            $message = new Message();
            $message->id_cart = (int) $cart->id;
            $message->id_customer = (int) $cart->id_customer;
        }

        $message->message = $orderMessage;

        if (!(null === $message->id ? $message->add() : $message->update())) {
            throw new CartException('Failed to store the cart order message');
        }
    }

    private function findCartMessage(Cart $cart): ?Message
    {
        $existing = Message::getMessageByCartId((int) $cart->id);

        if (empty($existing['id_message'])) {
            return null;
        }

        return new Message((int) $existing['id_message']);
    }
}
