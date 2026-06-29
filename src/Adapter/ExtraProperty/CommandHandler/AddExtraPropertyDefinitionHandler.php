<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler\AddExtraPropertyDefinitionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;

/**
 * Creates a new core extra property definition via the registry.
 *
 * Delegates to ExtraPropertyRegistryInterface::register() which creates the definition
 * row and the physical SQL column, returning the new row id directly.
 */
#[AsCommandHandler]
final class AddExtraPropertyDefinitionHandler implements AddExtraPropertyDefinitionHandlerInterface
{
    public function __construct(
        private readonly ExtraPropertyRegistryInterface $registry,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ExtraPropertyRegistrationFailureException
     */
    public function handle(AddExtraPropertyDefinitionCommand $command): ExtraPropertyDefinitionId
    {
        $definition = new ExtraPropertyDefinition(
            entityName: $command->getEntityName(),
            propertyName: $command->getPropertyName(),
            type: $command->getFieldType(),
            scope: $command->getFieldScope(),
            moduleName: null,
            enumValues: $command->getEnumValues(),
            defaultValue: $command->getDefaultValue(),
            nullable: $command->isNullable(),
            required: $command->isRequired(),
            size: $command->getSize(),
            sqlIndex: $command->getSqlIndex(),
            displayFront: $command->isDisplayFront(),
            associatedForms: $command->getAssociatedForms(),
            associatedGrids: $command->getAssociatedGrids(),
            associatedApis: $command->getAssociatedApis(),
            formFieldType: $command->getFormFieldType() ?: null,
            formOptions: $command->getFormOptions(),
            constraints: $command->getConstraints(),
            labelWording: $command->getLabelWording() ?: null,
            labelDomain: $command->getLabelDomain() ?: null,
            descriptionWording: $command->getDescriptionWording() ?: null,
            descriptionDomain: $command->getDescriptionDomain() ?: null,
        );

        $id = $this->registry->register($definition);

        if (false === $id) {
            throw new ExtraPropertyRegistrationFailureException(
                sprintf(
                    'Failed to register extra property "%s" on entity "%s".',
                    $command->getPropertyName(),
                    $command->getEntityName()
                )
            );
        }

        return new ExtraPropertyDefinitionId($id);
    }
}
