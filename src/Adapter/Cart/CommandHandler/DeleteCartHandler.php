<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\AbstractCartHandler;
use PrestaShop\PrestaShop\Adapter\Cart\Repository\CartRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\DeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\DeleteCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteOrderedCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Handles deletion of cart using legacy object model
 */
#[AsCommandHandler]
class DeleteCartHandler extends AbstractCartHandler implements DeleteCartHandlerInterface
{
    public function __construct(
        protected readonly CartRepository $cartRepository,
        protected readonly OrderRepository $orderRepository
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteCartException
     * @throws CannotDeleteOrderedCartException
     * @throws CartException
     * @throws CoreException
     */
    public function handle(DeleteCartCommand $command): void
    {
        try {
            $this->orderRepository->getByCartId($command->getCartId());
            throw new CannotDeleteOrderedCartException(sprintf('Cart "%s" with order cannot be deleted.', $command->getCartId()->getValue()));
        } catch (OrderNotFoundException) {
            // Cart is not linked to any order, we can safely delete it
            $this->cartRepository->delete($command->getCartId());
        }
    }
}
