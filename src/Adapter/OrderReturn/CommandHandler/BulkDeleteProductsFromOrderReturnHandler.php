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
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\BulkDeleteProductsFromOrderReturnCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler\BulkDeleteProductsFromOrderReturnHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\BulkDeleteProductsFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\CannotDeleteLastProductFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\DeleteProductFromOrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnProductId;

#[AsCommandHandler]
class BulkDeleteProductsFromOrderReturnHandler extends AbstractBulkCommandHandler implements BulkDeleteProductsFromOrderReturnHandlerInterface
{
    /**
     * @var OrderReturnRepository
     */
    private $orderReturnRepository;

    public function __construct(OrderReturnRepository $orderReturnRepository)
    {
        $this->orderReturnRepository = $orderReturnRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductsFromOrderReturnCommand $command): void
    {
        $productIds = $command->getProductIds();
        if ($productIds === []) {
            return;
        }

        // Pre-check the legacy parity guard once, before any row deletion, so partial bulk
        // deletes can never leave the return empty.
        $currentCount = $this->orderReturnRepository->countProductLines($command->getOrderReturnId());
        if ($currentCount - count($productIds) < 1) {
            throw new CannotDeleteLastProductFromOrderReturnException(
                'A merchandise return must contain at least one product after deletion.'
            );
        }

        $this->handleBulkAction($productIds, DeleteProductFromOrderReturnException::class, $command);
    }

    /**
     * {@inheritdoc}
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        /* @var OrderReturnProductId $id */
        /* @var BulkDeleteProductsFromOrderReturnCommand $command */
        $this->orderReturnRepository->deleteProductLine($command->getOrderReturnId(), $id);
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof OrderReturnProductId;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDeleteProductsFromOrderReturnException($caughtExceptions);
    }
}
