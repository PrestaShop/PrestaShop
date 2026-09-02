<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\DuplicateOrderCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateOrderCartException;
use Shop;

/**
 * @internal
 */
#[AsCommandHandler]
final class DuplicateOrderCartHandler implements DuplicateOrderCartHandlerInterface
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
    public function handle(DuplicateOrderCartCommand $command)
    {
        // IMPORTANT: context customer must be set in order to correctly fill the address
        $cart = Cart::getCartByOrderId($command->getOrderId()->getValue());
        $this->contextStateManager
            ->setCart($cart)
            ->setCustomer(new Customer($cart->id_customer))
            ->setCurrency(new Currency($cart->id_currency))
            ->setLanguage($cart->getAssociatedLanguage())
            ->setShop(new Shop($cart->id_shop))
        ;
        $result = $cart->duplicate();

        if (false === $result || !isset($result['cart'])) {
            $this->contextStateManager->restorePreviousContext();
            throw new DuplicateOrderCartException(sprintf('Cannot duplicate cart from order "%s"', $command->getOrderId()->getValue()));
        }

        $this->contextStateManager->restorePreviousContext();

        return new CartId((int) $result['cart']->id);
    }
}
