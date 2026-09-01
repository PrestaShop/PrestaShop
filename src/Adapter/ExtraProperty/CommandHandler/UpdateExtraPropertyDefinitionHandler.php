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
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException as CoreExtraPropertyException;

/**
 * Updates editable metadata fields of a core extra property definition.
 *
 * Structural fields (entity_name, property_name, type, scope) are preserved from the
 * existing row. nullable, size, enumValues and sql_index may be overridden, but only
 * non-destructive changes are accepted — the registry refuses the write otherwise
 * (see ExtraPropertyRegistry::hasStorageChanges()).
 *
 * Module-owned definitions are a deliberate carve-out: they accept exactly one
 * modification — the shop association (setAssociatedShopIds()) — because the module is
 * the source of truth for everything else while the merchant remains in charge of which
 * of their shops the property applies to. Any other setter used together with a
 * module-owned id throws ProtectedModuleExtraPropertyDefinitionException.
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
     * @throws ExtraPropertyRegistrationFailureException carries the failure reason as its code
     *                                                   and the core exception as previous
     */
    public function handle(UpdateExtraPropertyDefinitionCommand $command): void
    {
        $id = $command->getId()->getValue();
        $definition = $this->repository->getDefinitionById($id);
        if (null === $definition) {
            throw new ExtraPropertyDefinitionNotFoundException(
                sprintf('Extra property definition with id %d was not found.', $id)
            );
        }

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
        if (null !== $command->getFormType()) {
            $overrides['formType'] = $command->getFormType() ?: null;
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
        // Null = setter never used (association untouched — the loaded value is kept and
        // save() re-persists it identically); [] = explicit revert to the fallback.
        if (null !== $command->getAssociatedShopIds()) {
            $overrides['associatedShopIds'] = $command->getAssociatedShopIds();
        }

        // The shop-association carve-out: the ONLY field a module-owned definition accepts.
        // Any other modification requested together with a module-owned id is rejected.
        if ($definition->isModuleOwned() && [] !== array_diff_key($overrides, ['associatedShopIds' => null])) {
            throw new ProtectedModuleExtraPropertyDefinitionException(
                sprintf(
                    'Extra property definition "%s.%s" is owned by module "%s": only its shop association can be modified from the BO.',
                    $definition->getEntityName(),
                    $definition->getPropertyName(),
                    $definition->getModuleName()
                )
            );
        }

        if ([] === $overrides) {
            return;
        }

        try {
            $this->registry->register($definition->withOverrides($overrides));
        } catch (CoreExtraPropertyException $exception) {
            throw ExtraPropertyRegistrationFailureException::fromCoreException(
                $exception,
                sprintf('Failed to update extra property definition with id %d.', $id)
            );
        }
    }
}
