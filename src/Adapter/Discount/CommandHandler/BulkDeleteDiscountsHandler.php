<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\BulkDeleteDiscountsCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\BulkDeleteDiscountsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\BulkDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotDeleteDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkDeleteDiscountsHandler extends AbstractBulkCommandHandler implements BulkDeleteDiscountsHandlerInterface
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteDiscountException
     */
    public function handle(BulkDeleteDiscountsCommand $command): void
    {
        $this->handleBulkAction($command->getDiscountIds(), DiscountException::class);
    }

    /**
     * @param DiscountId $id
     * @param BulkDeleteDiscountsCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $this->discountRepository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDiscountException(
            $caughtExceptions,
            'Errors occurred during discount bulk change status action',
            BulkDiscountException::FAILED_BULK_DELETE
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function supports($id): bool
    {
        return $id instanceof DiscountId;
    }
}
