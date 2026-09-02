<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveCartRuleFromCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\RemoveCartRuleFromCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;

/**
 * @internal
 */
#[AsCommandHandler]
final class RemoveCartRuleFromCartHandler extends AbstractCartHandler implements RemoveCartRuleFromCartHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(RemoveCartRuleFromCartCommand $command)
    {
        $cart = $this->getCart($command->getCartId());

        if (!$cart->removeCartRule($command->getCartRuleId()->getValue())) {
            throw new CartException(sprintf('Failed to remove cart rule with id "%d" from cart', $command->getCartRuleId()->getValue()));
        }
    }
}
