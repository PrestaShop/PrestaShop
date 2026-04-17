<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use CartRule;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\BulkUpdateDiscountsStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\BulkUpdateDiscountsStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\BulkDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotUpdateDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;

#[AsCommandHandler]
final class BulkUpdateDiscountsStatusHandler extends AbstractBulkCommandHandler implements BulkUpdateDiscountsStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateDiscountException
     * @throws DiscountNotFoundException
     */
    public function handle(BulkUpdateDiscountsStatusCommand $command): void
    {
        $this->handleBulkAction($command->getDiscountIds(), DiscountException::class, $command);
    }

    /**
     * @param DiscountId $id
     * @param BulkUpdateDiscountsStatusCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $entity = new CartRule($id->getValue());
        $entity->active = $command->getNewStatus();

        if (!$entity->id) {
            throw new DiscountNotFoundException(sprintf('Discount with id "%s" was not found', $id->getValue()));
        }
        if (!$entity->update()) {
            throw new CannotUpdateDiscountException(sprintf('Cannot update status for discount with id "%s"', $id->getValue()));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkDiscountException(
            $caughtExceptions,
            'Errors occurred during discount bulk change status action',
            BulkDiscountException::FAILED_BULK_UPDATE_STATUS
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
