<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Exception\BulkCommandExceptionInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\BulkDeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\BulkDeleteExtraPropertyDefinitionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\BulkExtraPropertyException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;

/**
 * Deletes several core extra property definitions in bulk.
 *
 * Goes through the repository + registry directly (the same guard and unregister call as
 * DeleteExtraPropertyDefinitionHandler) rather than executing that handler from this one —
 * handlers never call other handlers. Per-item ExtraPropertyException failures (e.g.
 * module-owned definitions) are aggregated instead of stopping the batch midway.
 */
#[AsCommandHandler]
final class BulkDeleteExtraPropertyDefinitionHandler extends AbstractBulkCommandHandler implements BulkDeleteExtraPropertyDefinitionHandlerInterface
{
    public function __construct(
        private readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        private readonly ExtraPropertyRegistryInterface $registry,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteExtraPropertyDefinitionCommand $command): void
    {
        $this->handleBulkAction($command->getIds(), ExtraPropertyException::class, $command);
    }

    /**
     * @param int $id
     * @param BulkDeleteExtraPropertyDefinitionCommand $command
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $definition = $this->repository->getUnprotectedDefinitionById($id);
        $unregistered = $this->registry->unregister($definition, $command->shouldDropColumn());

        if (!$unregistered) {
            throw new ExtraPropertyRegistrationFailureException(
                sprintf('Failed to delete extra property definition with id %d.', $id)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function buildBulkException(array $caughtExceptions): BulkCommandExceptionInterface
    {
        return new BulkExtraPropertyException($caughtExceptions);
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(mixed $id): bool
    {
        return is_int($id);
    }
}
