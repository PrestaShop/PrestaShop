<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use JsonException;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\UpdateExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\AssociationRowSerializer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ConstraintRowSerializer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\EnumValuesParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * Handles form data submission for extra property definitions.
 *
 * The submitted data is nested by card section (field_definition, visibility, labels,
 * validation, advanced), matching ExtraPropertyDefinitionType's sub-form structure. Each
 * section's fields are always present (Symfony Form guarantees this), so no defensive `?? ''`
 * fallback is needed for required fields — only the empty-string to null normalization (via the
 * Elvis operator) for optional string fields.
 *
 * create() dispatches AddExtraPropertyDefinitionCommand (no module_name — always null for BO-created fields).
 * update() dispatches UpdateExtraPropertyDefinitionCommand (structural fields are intentionally excluded).
 *
 * The associations and constraints arrive as builder-row arrays (the mapped row collections'
 * data) and are serialized back into the entry strings / DSL value the commands accept — the
 * inverse of what the form data provider presented. The row form types already validated every
 * row through the same parser/mapper, so serialization cannot fail here on a form-validated
 * submission. Only form_options is edited as raw JSON — the one boundary where JSON decoding
 * belongs; the CQRS commands themselves only ever carry native arrays. Malformed form_options
 * JSON throws instead of being silently dropped — the form's Json constraint normally blocks it
 * before this handler runs (see ExtraPropertyDefinitionAdvancedType), so a throw here only
 * surfaces for programmatic submissions or hook-mutated form data.
 */
class ExtraPropertyDefinitionFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(protected readonly CommandBusInterface $commandBus)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $data
     *
     * @return int
     */
    public function create(array $data): int
    {
        $fieldDefinition = $data['field_definition'];
        $visibility = $data['visibility'];
        $labels = $data['labels'];
        $validation = $data['validation'];
        $advanced = $data['advanced'];

        /** @var ExtraPropertyDefinitionId $id */
        $id = $this->commandBus->handle(new AddExtraPropertyDefinitionCommand(
            entityName: $fieldDefinition['entity_name'],
            propertyName: $fieldDefinition['property_name'],
            fieldType: ExtraPropertyType::from($fieldDefinition['type']),
            fieldScope: ExtraPropertyScope::from($fieldDefinition['scope']),
            sqlIndex: ExtraPropertySqlIndex::from($fieldDefinition['sql_index']),
            displayFront: (bool) $visibility['display_front'],
            required: (bool) $visibility['required'],
            nullable: (bool) ($fieldDefinition['nullable'] ?? true),
            size: $fieldDefinition['size'] ?: null,
            defaultValue: $fieldDefinition['default_value'] ?: null,
            enumValues: EnumValuesParser::parse($fieldDefinition['enum_values'] ?? null),
            labelWording: $labels['label_wording'] ?: null,
            labelDomain: $labels['label_domain'] ?: null,
            descriptionWording: $labels['description_wording'] ?: null,
            descriptionDomain: $labels['description_domain'] ?: null,
            constraints: ExtraPropertyConstraintMapper::fromNames(ConstraintRowSerializer::serialize($validation['constraints'] ?? [])),
            formType: $advanced['form_type'] ?: null,
            formOptions: $this->parseJsonObject($advanced['form_options'] ?? null),
            associatedForms: AssociationRowSerializer::formEntries($advanced['associated_forms'] ?? []),
            associatedGrids: AssociationRowSerializer::gridEntries($advanced['associated_grids'] ?? []),
            associatedApis: AssociationRowSerializer::apiEntries($advanced['associated_apis'] ?? []),
        ));

        return $id->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @param array<string, mixed> $data
     */
    public function update($id, array $data): void
    {
        $fieldDefinition = $data['field_definition'];
        $visibility = $data['visibility'];
        $labels = $data['labels'];
        $validation = $data['validation'];
        $advanced = $data['advanced'];

        $command = (new UpdateExtraPropertyDefinitionCommand((int) $id))
            ->setDisplayFront((bool) $visibility['display_front'])
            ->setRequired((bool) $visibility['required'])
            ->setNullable((bool) ($fieldDefinition['nullable'] ?? true))
            ->setSqlIndex(ExtraPropertySqlIndex::from($fieldDefinition['sql_index']))
            ->setLabelWording($labels['label_wording'] ?: null)
            ->setLabelDomain($labels['label_domain'] ?: null)
            ->setDescriptionWording($labels['description_wording'] ?: null)
            ->setDescriptionDomain($labels['description_domain'] ?: null)
            ->setConstraints(ExtraPropertyConstraintMapper::fromNames(ConstraintRowSerializer::serialize($validation['constraints'] ?? [])))
            ->setFormType($advanced['form_type'] ?: null)
            ->setFormOptions($this->parseJsonObject($advanced['form_options'] ?? null))
            ->setAssociatedForms(AssociationRowSerializer::formEntries($advanced['associated_forms'] ?? []))
            ->setAssociatedGrids(AssociationRowSerializer::gridEntries($advanced['associated_grids'] ?? []))
            ->setAssociatedApis(AssociationRowSerializer::apiEntries($advanced['associated_apis'] ?? []));

        if (!empty($fieldDefinition['size'])) {
            $command->setSize((int) $fieldDefinition['size']);
        }

        $enumValues = EnumValuesParser::parse($fieldDefinition['enum_values'] ?? null);
        if (null !== $enumValues) {
            $command->setEnumValues($enumValues);
        }

        $this->commandBus->handle($command);
    }

    /**
     * Decodes the JSON object submitted by the form_options textarea.
     *
     * @param string|null $rawValue
     *
     * @return array<string, mixed>|null
     *
     * @throws InvalidExtraPropertyDefinitionException when the value is not valid JSON or does not decode to a JSON object
     */
    protected function parseJsonObject(?string $rawValue): ?array
    {
        if (null === $rawValue || '' === trim($rawValue)) {
            return null;
        }

        try {
            $decoded = json_decode($rawValue, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidExtraPropertyDefinitionException(
                sprintf('The form options field contains invalid JSON: %s.', $e->getMessage()),
                0,
                $e
            );
        }

        // A JSON list ([1, 2]) is an array too but is not a set of named form options — reject it
        // like any other non-object so the message matches what is actually accepted ({} is fine).
        if (!is_array($decoded) || ($decoded !== [] && array_is_list($decoded))) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'The form options field must contain a JSON object, got %s.',
                is_array($decoded) ? 'a JSON list' : get_debug_type($decoded)
            ));
        }

        return $decoded;
    }
}
