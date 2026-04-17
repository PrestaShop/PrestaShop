<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveProductFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\RemoveProductFromCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use Shop;

/**
 * Handles removing product from context cart.
 *
 * @internal
 */
#[AsCommandHandler]
final class RemoveProductFromCartHandler extends AbstractCartHandler implements RemoveProductFromCartHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(
        ContextStateManager $contextStateManager
    ) {
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RemoveProductFromCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency(new Currency($cart->id_currency))
            ->setLanguage($cart->getAssociatedLanguage())
            ->setCustomer(new Customer($cart->id_customer))
            ->setShop(new Shop($cart->id_shop))
        ;

        try {
            $removed = $cart->deleteProduct(
                $command->getProductId()->getValue(),
                $command->getCombinationId() ?: 0,
                $command->getCustomizationId() ?: 0
            );

            if (!$removed) {
                throw new CartException(sprintf('Failed to remove product with id "%d" from cart', $command->getProductId()->getValue()));
            }
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }
}
