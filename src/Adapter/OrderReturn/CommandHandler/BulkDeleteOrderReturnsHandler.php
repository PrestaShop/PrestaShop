<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\CommandHandler;

use PrestaShop\PrestaShop\Adapter\OrderReturn\Repository\OrderReturnRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteOrderReturnsCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\BulkDeleteOrderReturnsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteOrderReturnsException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;

#[AsCommandHandler]
class BulkDeleteOrderReturnsHandler extends AbstractBulkCommandHandler implements BulkDeleteOrderReturnsHandlerInterface
{
    private OrderReturnRepository $orderReturnRepository;

    public function __construct(OrderReturnRepository $orderReturnRepository)
    {
        $this->orderReturnRepository = $orderReturnRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteOrderReturnsCommand $command): void
    {
        $orderReturnIds = $command->getOrderReturnIds();
        if ($orderReturnIds === []) {
            return;
        }

        // The repository throws OrderReturnNotFoundException for an unknown id: every exception of the domain is
        // collected, so one failing id does not stop the others
        $this->handleBulkAction($orderReturnIds, OrderReturnException::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        /* @var OrderReturnId $id */
        $this->orderReturnRepository->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof OrderReturnId;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDeleteOrderReturnsException($caughtExceptions);
    }
}
