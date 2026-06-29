<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\DeleteExtraPropertyDefinitionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;

/**
 * Deletes a core extra property definition and optionally its physical SQL column.
 *
 * Delegates to ExtraPropertyRegistryInterface::unregister() which handles
 * the column drop (when requested) and cache invalidation.
 */
#[AsCommandHandler]
final class DeleteExtraPropertyDefinitionHandler implements DeleteExtraPropertyDefinitionHandlerInterface
{
    public function __construct(
        private readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        private readonly ExtraPropertyRegistryInterface $registry,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ExtraPropertyDefinitionNotFoundException
     * @throws ProtectedModuleExtraPropertyDefinitionException
     * @throws ExtraPropertyRegistrationFailureException
     */
    public function handle(DeleteExtraPropertyDefinitionCommand $command): void
    {
        $id = $command->getId()->getValue();
        $definition = $this->repository->getUnprotectedDefinitionById($id);

        $unregistered = $this->registry->unregister($definition, $command->shouldDropColumn());

        if (!$unregistered) {
            throw new ExtraPropertyRegistrationFailureException(
                sprintf('Failed to delete extra property definition with id %d.', $id)
            );
        }
    }
}
