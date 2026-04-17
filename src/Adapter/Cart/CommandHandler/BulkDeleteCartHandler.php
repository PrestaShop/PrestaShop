<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Cart\Repository\CartRepository;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\BulkDeleteCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler\BulkDeleteCartHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\BulkCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CannotDeleteOrderedCartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Deletes cart in bulk action using legacy object model
 */
#[AsCommandHandler]
class BulkDeleteCartHandler extends AbstractBulkCommandHandler implements BulkDeleteCartHandlerInterface
{
    public function __construct(
        protected readonly CartRepository $cartRepository,
        protected readonly OrderRepository $orderRepository
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws CartException
     * @throws CoreException
     */
    public function handle(BulkDeleteCartCommand $command): void
    {
        $this->handleBulkAction($command->getCartIds(), CartException::class);
    }

    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkCartException(
            $caughtExceptions,
            'Errors occurred during Alias bulk delete action',
        );
    }

    /**
     * @param CartId $id
     * @param mixed $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        try {
            $this->orderRepository->getByCartId($id);
            throw new CannotDeleteOrderedCartException(sprintf('Cart "%s" with order cannot be deleted.', $id->getValue()));
        } catch (OrderNotFoundException) {
            // Cart is not linked to any order, we can safely delete it
            $this->cartRepository->delete($id);
        }
    }

    protected function supports($id): bool
    {
        return $id instanceof CartId;
    }
}
