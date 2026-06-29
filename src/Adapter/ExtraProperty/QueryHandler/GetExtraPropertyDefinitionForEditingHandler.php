<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryHandler\GetExtraPropertyDefinitionForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\EditableExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;

/**
 * Loads all fields of one extra property definition for the BO edit form.
 */
#[AsQueryHandler]
final class GetExtraPropertyDefinitionForEditingHandler implements GetExtraPropertyDefinitionForEditingHandlerInterface
{
    public function __construct(
        private readonly ExtraPropertyDefinitionRepositoryInterface $repository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ExtraPropertyDefinitionNotFoundException
     */
    public function handle(GetExtraPropertyDefinitionForEditing $query): EditableExtraPropertyDefinition
    {
        $id = $query->getId()->getValue();
        $definition = $this->repository->getDefinitionById($id);

        if (null === $definition) {
            throw new ExtraPropertyDefinitionNotFoundException(
                sprintf('Extra property definition with id %d was not found.', $id)
            );
        }

        return new EditableExtraPropertyDefinition(
            id: $id,
            entityName: $definition->getEntityName(),
            moduleName: $definition->getModuleName(),
            propertyName: $definition->getPropertyName(),
            fieldType: $definition->getType(),
            fieldScope: $definition->getScope(),
            sqlIndex: $definition->getSqlIndex(),
            nullable: $definition->isNullable(),
            size: $definition->getSize(),
            defaultValue: null !== $definition->getDefaultValue() ? (string) $definition->getDefaultValue() : null,
            enumValues: $definition->getEnumValues(),
            displayFront: $definition->isDisplayFront(),
            required: $definition->isRequired(),
            labelWording: $definition->getLabelWording(),
            labelDomain: $definition->getLabelDomain(),
            descriptionWording: $definition->getDescriptionWording(),
            descriptionDomain: $definition->getDescriptionDomain(),
            constraints: $definition->getConstraints(),
            formFieldType: $definition->getFormFieldType(),
            formOptions: $definition->getFormOptions(),
            associatedForms: $definition->getAssociatedForms(),
            associatedGrids: $definition->getAssociatedGrids(),
            associatedApis: $definition->getAssociatedApis(),
        );
    }
}
