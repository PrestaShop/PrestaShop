<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\EditableExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * Provides form data for the extra property definition create / edit form.
 *
 * The data is nested by card section (field_definition, visibility, labels, validation,
 * advanced), matching ExtraPropertyDefinitionType's sub-form structure.
 *
 * getData() dispatches GetExtraPropertyDefinitionForEditing and maps the DTO to form field names.
 * getDefaultData() provides sensible defaults for the creation form.
 */
final class ExtraPropertyDefinitionFormDataProvider implements FormDataProviderInterface
{
    public function __construct(protected readonly CommandBusInterface $queryBus)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     *
     * @return array<string, mixed>
     */
    public function getData($id): array
    {
        /** @var EditableExtraPropertyDefinition $definition */
        $definition = $this->queryBus->handle(new GetExtraPropertyDefinitionForEditing((int) $id));

        return [
            'field_definition' => [
                'entity_name' => $definition->getEntityName(),
                'property_name' => $definition->getPropertyName(),
                'module_name' => $definition->getModuleName(),
                'type' => $definition->getFieldType()->value,
                'scope' => $definition->getFieldScope()->value,
                'sql_index' => $definition->getSqlIndex()->value,
                'nullable' => $definition->isNullable(),
                'size' => $definition->getSize(),
                'default_value' => $definition->getDefaultValue(),
                'enum_values' => null !== $definition->getEnumValues() ? implode("\n", $definition->getEnumValues()) : null,
            ],
            'visibility' => [
                'display_front' => $definition->isDisplayFront(),
                'required' => $definition->isRequired(),
            ],
            'labels' => [
                'label_wording' => $definition->getLabelWording(),
                'label_domain' => $definition->getLabelDomain(),
                'description_wording' => $definition->getDescriptionWording(),
                'description_domain' => $definition->getDescriptionDomain(),
            ],
            'validation' => [
                'constraints' => ExtraPropertyConstraintMapper::toNames($definition->getConstraints()),
            ],
            'advanced' => [
                'form_field_type' => $definition->getFormFieldType(),
                'form_options' => null !== $definition->getFormOptions() ? json_encode($definition->getFormOptions(), JSON_UNESCAPED_SLASHES) : null,
                'associated_forms' => null !== $definition->getAssociatedForms() ? json_encode($definition->getAssociatedForms(), JSON_UNESCAPED_SLASHES) : null,
                'associated_grids' => null !== $definition->getAssociatedGrids() ? json_encode($definition->getAssociatedGrids(), JSON_UNESCAPED_SLASHES) : null,
                'associated_apis' => null !== $definition->getAssociatedApis() ? json_encode($definition->getAssociatedApis(), JSON_UNESCAPED_SLASHES) : null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function getDefaultData(): array
    {
        return [
            'field_definition' => [
                'type' => ExtraPropertyType::STRING->value,
                'scope' => ExtraPropertyScope::COMMON->value,
                'sql_index' => ExtraPropertySqlIndex::NONE->value,
                'nullable' => true,
            ],
            'visibility' => [
                'display_front' => false,
                'required' => false,
            ],
        ];
    }
}
