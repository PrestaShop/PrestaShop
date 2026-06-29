<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\UpdateExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\UpdateExtraPropertyDefinitionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;

/**
 * Updates editable metadata fields of a core extra property definition.
 *
 * Structural fields (entity_name, property_name, type, scope) are preserved from the
 * existing row. nullable, size, enumValues and sql_index may be overridden, but only
 * non-destructive changes are accepted — the registry refuses the write otherwise
 * (see ExtraPropertyRegistry::hasStorageChanges()).
 */
#[AsCommandHandler]
final class UpdateExtraPropertyDefinitionHandler implements UpdateExtraPropertyDefinitionHandlerInterface
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
    public function handle(UpdateExtraPropertyDefinitionCommand $command): void
    {
        $id = $command->getId()->getValue();
        $definition = $this->repository->getUnprotectedDefinitionById($id);

        // Build the overrides map from non-null setters in the command.
        $overrides = [];

        if (null !== $command->getDisplayFront()) {
            $overrides['displayFront'] = $command->getDisplayFront();
        }
        if (null !== $command->getRequired()) {
            $overrides['required'] = $command->getRequired();
        }
        if (null !== $command->getNullable()) {
            $overrides['nullable'] = $command->getNullable();
        }
        if (null !== $command->getSize()) {
            $overrides['size'] = $command->getSize();
        }
        if (null !== $command->getEnumValues()) {
            $overrides['enumValues'] = $command->getEnumValues();
        }
        if (null !== $command->getSqlIndex()) {
            $overrides['sqlIndex'] = $command->getSqlIndex();
        }
        if (null !== $command->getLabelWording()) {
            $overrides['labelWording'] = $command->getLabelWording() ?: null;
        }
        if (null !== $command->getLabelDomain()) {
            $overrides['labelDomain'] = $command->getLabelDomain() ?: null;
        }
        if (null !== $command->getDescriptionWording()) {
            $overrides['descriptionWording'] = $command->getDescriptionWording() ?: null;
        }
        if (null !== $command->getDescriptionDomain()) {
            $overrides['descriptionDomain'] = $command->getDescriptionDomain() ?: null;
        }
        if (null !== $command->getConstraints()) {
            $overrides['constraints'] = $command->getConstraints();
        }
        if (null !== $command->getFormFieldType()) {
            $overrides['formFieldType'] = $command->getFormFieldType() ?: null;
        }
        if (null !== $command->getFormOptions()) {
            $overrides['formOptions'] = $command->getFormOptions();
        }
        if (null !== $command->getAssociatedForms()) {
            $overrides['associatedForms'] = $command->getAssociatedForms();
        }
        if (null !== $command->getAssociatedGrids()) {
            $overrides['associatedGrids'] = $command->getAssociatedGrids();
        }
        if (null !== $command->getAssociatedApis()) {
            $overrides['associatedApis'] = $command->getAssociatedApis();
        }

        $updated = $definition->withOverrides($overrides);
        $saved = $this->registry->register($updated);

        if (false === $saved) {
            throw new ExtraPropertyRegistrationFailureException(
                sprintf('Failed to update extra property definition with id %d.', $id)
            );
        }
    }
}
